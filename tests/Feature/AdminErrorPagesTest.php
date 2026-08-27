<?php

namespace Tests\Feature;

use App\Models\User;
use Eshlink\Cms\Auth\TwoFactor;
use Eshlink\Cms\Http\Middleware\RequireTwoFactor;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * The admin's error screens, wired in bootstrap/app.php, and the line they
 * must not cross.
 *
 * Two halves, and the second is the one that must never fail. On the admin
 * host a refusal is a written sentence on the same panel the sign-in screen
 * uses. On marinanewyorkcity.com nothing has changed at all: a dead link there
 * is answered by Laravel exactly as it was before this was wired, because the
 * public site's pages are Marina's design and this package has no business
 * touching them.
 *
 * The case with the history behind it is the editor's. Before this, clicking
 * Settings printed the ability out of the role table on a white page, which is
 * a fact about a database and reads like an accusation.
 */
class AdminErrorPagesTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const PUBLIC_HOST = 'http://localhost';

    /**
     * Signed in, through the second factor, at a role.
     */
    private function signIn(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        $twoFactor = $this->app->make(TwoFactor::class);
        $twoFactor->beginEnrolment($user);
        $twoFactor->row($user)->forceFill(['confirmed_at' => Carbon::now()])->save();

        $this->actingAs($user);
        $this->withSession([RequireTwoFactor::VERIFIED_KEY => Carbon::now()->toIso8601String()]);

        return $user;
    }

    public function test_an_editor_refused_a_screen_is_told_whose_it_is_and_not_which_ability_it_wanted(): void
    {
        $this->signIn('editor');

        $response = $this->get(route('cms.settings.edit'))->assertForbidden();

        $response->assertSee('Only the site owner can open this');
        $response->assertSee('This screen is kept for the site owner.', false);
        $response->assertSee('you have done nothing wrong', false);
        $response->assertSee('Back to your site');

        $response->assertDontSee('Your role does not include', false);
        $response->assertDontSee('settings.write', false);
    }

    public function test_the_activity_log_refuses_an_editor_in_the_same_words(): void
    {
        $this->signIn('editor');

        $response = $this->get(route('cms.audit.index'))->assertForbidden();

        $response->assertSee('Only the site owner can open this');
        $response->assertDontSee('audit.read', false);
    }

    public function test_a_refusal_is_kept_out_of_search_like_every_other_admin_screen(): void
    {
        $this->signIn('editor');

        $this->get(route('cms.settings.edit'))
            ->assertForbidden()
            ->assertSee('<meta name="robots" content="noindex, nofollow', false);
    }

    /**
     * The public site answers its dead links in its own voice.
     *
     * It used to be Laravel's white page; it is now
     * resources/views/errors/404.blade.php, which is Marina's design and hers
     * to change. What must never happen is the admin's screen appearing there
     * — that panel is written for the person who signs in, and none of its
     * words mean anything to a reader who followed a stale Instagram link.
     */
    public function test_the_public_site_answers_a_dead_link_in_its_own_words_and_never_the_admins(): void
    {
        $response = $this->get(self::PUBLIC_HOST.'/no-such-page')->assertNotFound();

        $response->assertDontSee('That page is not here');
        $response->assertDontSee('Back to your site');
        $response->assertDontSee('cms-guest', false);
    }

    /**
     * The framing and sniffing headers a 404 used to lose.
     *
     * `cms.security-headers` is on the admin route group, and a URI that
     * matched no route never enters a group — so `/login` carried
     * X-Frame-Options and the not-found screen beside it did not. Registering
     * the middleware globally in bootstrap/app.php is what closes it, and the
     * middleware asks HostMode whose host answered so the public site is
     * untouched.
     */
    public function test_an_admin_screen_that_matched_no_route_still_refuses_to_be_framed(): void
    {
        $host = 'http://'.config('cms.admin_domain');

        foreach ([
            $this->get($host.'/no-such-screen')->assertNotFound(),
            $this->get($host.'/login')->assertOk(),
        ] as $response) {
            $this->assertSame('DENY', $response->headers->get('X-Frame-Options'));
            $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
            $this->assertStringContainsString('noindex', (string) $response->headers->get('X-Robots-Tag'));
        }
    }

    public function test_the_public_site_gets_none_of_the_admins_headers(): void
    {
        $response = $this->get(self::PUBLIC_HOST.'/no-such-page')->assertNotFound();

        $this->assertNull($response->headers->get('X-Frame-Options'));
        $this->assertNull($response->headers->get('Content-Security-Policy-Report-Only'));
    }
}
