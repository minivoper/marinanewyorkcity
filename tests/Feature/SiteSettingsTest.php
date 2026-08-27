<?php

namespace Tests\Feature;

use App\Cms\Types\SiteSettingsType;
use App\Models\Setting;
use App\Models\User;
use Eshlink\Cms\Auth\TwoFactor;
use Eshlink\Cms\Http\Middleware\RequireTwoFactor;
use Eshlink\Cms\Services\EntryService;
use Eshlink\Cms\Support\TypeRegistry;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Settings are five rows of key/value JSON shown as one form of labelled boxes.
 *
 * The translation is the whole feature, so it is the whole test: the boxes must
 * come back out in the exact shape they went in as, because `site.email` is a
 * scalar under a `value` key and `site.emails` is a bare list and whatever
 * reads them was written against those two facts.
 */
class SiteSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * The seeded starting point, restated here rather than read from the
     * seeder: this test is the record of what the stored shape IS, so it has to
     * fail when the seeder changes it, not follow along.
     *
     * @var array<string, array<mixed>>
     */
    private const STORED = [
        'site.name' => ['value' => 'marina.newyorkcity'],
        'site.email' => ['value' => 'info@marinanewyorkcity.com'],
        'site.emails' => ['info@marinanewyorkcity.com'],
        'site.socials' => [
            'instagram' => 'https://www.instagram.com/marina.newyorkcity/',
            'tiktok' => 'https://www.tiktok.com/@marina.newyorkcity',
            'threads' => 'https://www.threads.com/@marina.newyorkcity',
            'facebook' => 'https://www.facebook.com/marina.nycity',
            'kit' => 'https://kit.marinanewyorkcity.com',
            'links' => 'https://links.marinanewyorkcity.com',
        ],
        'site.monthly_views' => ['value' => '5M+'],
    ];

    public function test_the_settings_screen_is_the_one_this_site_declares(): void
    {
        $this->assertSame('site_settings', config('cms.settings_type'));

        $type = app(TypeRegistry::class)->get('site_settings');

        $this->assertInstanceOf(SiteSettingsType::class, $type);
        $this->assertTrue($type->isSingleton());

        // The old five-rows-of-JSON type is gone from the sidebar. Two things
        // called Settings in one admin is one more than anybody can hold in
        // their head, and it was the unreadable one that went.
        $this->assertNull(app(TypeRegistry::class)->find('setting'));
    }

    public function test_every_stored_row_arrives_in_a_box_of_its_own(): void
    {
        $this->seedSettings();

        $data = $this->record()['data'];

        $this->assertSame([
            'name' => 'marina.newyorkcity',
            'email' => 'info@marinanewyorkcity.com',
            'monthly_views' => '5M+',
            'instagram' => 'https://www.instagram.com/marina.newyorkcity/',
            'tiktok' => 'https://www.tiktok.com/@marina.newyorkcity',
            'threads' => 'https://www.threads.com/@marina.newyorkcity',
            'facebook' => 'https://www.facebook.com/marina.nycity',
            'kit' => 'https://kit.marinanewyorkcity.com',
            'links' => 'https://links.marinanewyorkcity.com',
        ], $data);

        // No JSON anywhere. That is the point of the screen, so it is asserted
        // rather than assumed: a field whose value happens to start with `{`
        // means the fan-out silently stopped working.
        foreach ($data as $value) {
            $this->assertIsString($value);
            $this->assertStringStartsNotWith('{', $value);
            $this->assertStringStartsNotWith('[', $value);
        }
    }

    public function test_saving_the_same_values_back_leaves_every_row_byte_identical(): void
    {
        $this->seedSettings();

        $before = $this->storedJson();

        $this->save($this->record()['data']);

        $this->assertSame($before, $this->storedJson());
    }

    public function test_each_field_writes_back_into_the_shape_its_row_has_always_had(): void
    {
        $this->seedSettings();

        $this->save([
            'name' => 'marina.nyc',
            'email' => 'hello@marinanewyorkcity.com',
            'monthly_views' => '6M+',
            'instagram' => 'https://www.instagram.com/marina.nyc/',
        ]);

        $this->assertSame(['value' => 'marina.nyc'], $this->stored('site.name'));
        $this->assertSame(['value' => '6M+'], $this->stored('site.monthly_views'));

        // One box, two rows. `site.email` is a scalar under `value` and
        // `site.emails` is a bare list, and they are written together so they
        // cannot drift: mail addressed to the one nobody updated is mail
        // nobody receives and nobody notices.
        $this->assertSame(['value' => 'hello@marinanewyorkcity.com'], $this->stored('site.email'));
        $this->assertSame(['hello@marinanewyorkcity.com'], $this->stored('site.emails'));

        // The five untouched networks are still there, in their own map, with
        // the one that changed changed.
        $this->assertSame(
            array_merge(self::STORED['site.socials'], ['instagram' => 'https://www.instagram.com/marina.nyc/']),
            $this->stored('site.socials'),
        );
    }

    public function test_a_social_network_the_schema_does_not_know_about_survives_a_save(): void
    {
        $this->seedSettings();

        Setting::query()->where('key', 'site.socials')->firstOrFail()->update([
            'value' => self::STORED['site.socials'] + ['bluesky' => 'https://bsky.app/profile/marina.nyc'],
        ]);

        $this->save(['facebook' => 'https://www.facebook.com/marina.newyorkcity']);

        $socials = $this->stored('site.socials');

        // Merged through, not replaced. A save from a screen that has never
        // heard of Bluesky is not the moment for the footer link to Bluesky to
        // quietly disappear.
        $this->assertSame('https://bsky.app/profile/marina.nyc', $socials['bluesky']);
        $this->assertSame('https://www.facebook.com/marina.newyorkcity', $socials['facebook']);
        $this->assertSame(self::STORED['site.socials']['tiktok'], $socials['tiktok']);
    }

    public function test_an_empty_box_stores_an_empty_string_rather_than_a_null(): void
    {
        $this->seedSettings();

        $this->save(['monthly_views' => null]);

        // The row keeps its shape. A reader doing `config(...)['value']` on a
        // null would get an error rather than nothing.
        $this->assertSame(['value' => ''], $this->stored('site.monthly_views'));
    }

    public function test_the_pages_that_read_these_settings_render_identically_across_a_save(): void
    {
        $this->seed();
        $this->seedSettings();

        // The footer draws the contact address and the JSON-LD component draws
        // it again beside the social profiles, so the home page is where a
        // change to any of these would show up first. Asserted, so that this is
        // known to be the right page to be comparing.
        $before = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('info@marinanewyorkcity.com', $before);
        $this->assertStringContainsString('https://www.instagram.com/marina.newyorkcity/', $before);

        $this->save($this->record()['data']);

        $this->assertSame($before, $this->get('/')->assertOk()->getContent());
    }

    /**
     * Worth stating plainly, because it is the reason the assertion above is
     * cheap to keep true: nothing on the public site reads these rows yet. The
     * footer and the JSON-LD component both read `config('site.*')`, which is a
     * committed PHP file. So Marina's Settings screen currently changes what
     * the CMS shows her and nothing else — a fact worth failing on the day
     * somebody wires the footer to `Setting` without noticing that the two
     * sources of the same email address had drifted.
     */
    public function test_the_public_site_still_reads_its_addresses_from_config_not_from_these_rows(): void
    {
        $this->assertSame('info@marinanewyorkcity.com', config('site.email'));
        $this->assertContains('https://www.instagram.com/marina.newyorkcity/', config('site.socials'));

        $this->assertSame(
            config('site.email'),
            self::STORED['site.email']['value'],
            'config/site.php and the settings table hold different contact addresses.',
        );
    }

    /**
     * The whole path, through the screen she actually opens.
     *
     * Everything above talks to the service. This talks to the browser: the
     * form renders ten boxes with her values already in them, the Save button
     * posts to a route that accepts it, and the rows come back out in their
     * stored shape. If the fan-out were wrong anywhere between the blade and
     * the column, it would be wrong here.
     */
    public function test_the_owner_opens_the_settings_screen_and_saves_it(): void
    {
        config()->set('cms.admin_domain', 'cms.localhost');

        $this->seedSettings();
        $this->signInAsOwner();

        $this->get(route('cms.settings.edit'))
            ->assertOk()
            ->assertSee('Site name')
            ->assertSee('Where the contact form sends messages.')
            ->assertSee('marina.newyorkcity')
            ->assertSee('https://www.threads.com/@marina.newyorkcity');

        $this->put(route('cms.settings.update'), [
            'base_revision_id' => $this->record()['current_version'],
            'data' => array_merge($this->record()['data'], ['monthly_views' => '6M+']),
        ])->assertRedirect(route('cms.settings.edit'));

        $this->assertSame(['value' => '6M+'], $this->stored('site.monthly_views'));
        $this->assertSame(self::STORED['site.socials'], $this->stored('site.socials'));
        $this->assertSame(self::STORED['site.emails'], $this->stored('site.emails'));

        // These rows are also reachable through the generic content routes, and
        // the screens there must not fall over on a type that keeps no version
        // history: an empty History page is the honest answer, a 500 is not.
        $this->get(route('cms.entries.edit', ['site_settings', 'site_settings']))->assertOk();
        $this->get(route('cms.entries.history', ['site_settings', 'site_settings']))->assertOk();
    }

    /**
     * Editors are not shown a screen they cannot use. Reading and writing these
     * are one ability, because there is nothing on this form to read that is
     * not the thing you would have come here to change.
     */
    public function test_an_editor_is_not_let_into_the_settings_screen_at_all(): void
    {
        config()->set('cms.admin_domain', 'cms.localhost');

        $this->seedSettings();
        $this->signInAsOwner('editor');

        $this->get(route('cms.settings.edit'))->assertForbidden();
        $this->put(route('cms.settings.update'), ['data' => ['name' => 'Renamed']])->assertForbidden();
    }

    private function signInAsOwner(string $role = 'owner'): void
    {
        // `role` is added to Marina's users table by the package migration and
        // is deliberately not fillable on her model, so it is forced here
        // rather than mass-assigned.
        $user = User::query()->create([
            'name' => ucfirst($role),
            'email' => $role.'@marinanewyorkcity.test',
            'password' => Hash::make('correct-horse'),
        ]);

        $user->forceFill(['role' => $role])->save();

        $twoFactor = app(TwoFactor::class);
        $twoFactor->beginEnrolment($user);
        $twoFactor->row($user)->forceFill(['confirmed_at' => now()])->save();

        $this->actingAs($user);
        $this->withSession([RequireTwoFactor::VERIFIED_KEY => now()->toIso8601String()]);
    }

    /**
     * Save through the same service the Settings screen posts to, so what is
     * proven here is what happens when she presses the button — not what
     * happens when a test calls the source directly.
     *
     * @param  array<string, mixed>  $data
     */
    private function save(array $data): void
    {
        $entries = app(EntryService::class);
        $type = app(TypeRegistry::class)->get('site_settings');
        $entry = $entries->locate($type, null);

        $entries->update($type, $entry['id'], $data, [
            'base_revision_id' => $entry['current_version'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function record(): array
    {
        $type = app(TypeRegistry::class)->get('site_settings');

        return app(EntryService::class)->locate($type, null);
    }

    /**
     * @return array<string, mixed>
     */
    private function stored(string $key): array
    {
        return (array) Setting::query()->where('key', $key)->firstOrFail()->value;
    }

    /**
     * Every row as the exact JSON text the column holds, which is the thing
     * that must not change.
     *
     * @return array<string, string>
     */
    private function storedJson(): array
    {
        return DB::table('settings')
            ->orderBy('key')
            ->pluck('value', 'key')
            ->map(fn (mixed $value): string => (string) $value)
            ->all();
    }

    private function seedSettings(): void
    {
        foreach (self::STORED as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
