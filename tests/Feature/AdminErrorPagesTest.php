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

    public function test_the_public_site_still_answers_a_dead_link_exactly_as_laravel_does(): void
    {
        $response = $this->get(self::PUBLIC_HOST.'/no-such-page')->assertNotFound();

        // Laravel's own minimal page, untouched.
        $response->assertSee('404', false);
        $response->assertSee('Not Found', false);

        // And not one word of the admin's.
        $response->assertDontSee('That page is not here');
        $response->assertDontSee('Back to your site');
        $response->assertDontSee('cms-guest', false);
    }
}
