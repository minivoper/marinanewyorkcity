<?php

namespace Tests\Feature;

use App\Cms\Types\HomeType;
use App\Cms\Types\ShopType;
use Eshlink\Cms\Contracts\ContentType;
use Eshlink\Cms\Contracts\Presentable;
use Eshlink\Cms\Models\CmsSetting;
use Eshlink\Cms\Services\EntryService;
use Eshlink\Cms\Support\SiteMap;
use Eshlink\Cms\Support\TypeRegistry;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CmsContentTypeTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_every_declared_type_resolves_with_a_schema_and_a_source(): void
    {
        $types = app(TypeRegistry::class)->all();

        $this->assertNotEmpty($types);

        foreach ($types as $key => $type) {
            $this->assertInstanceOf(ContentType::class, $type);
            $this->assertSame($key, $type->key());
            $this->assertNotEmpty($type->schema()->keys(), "{$key} declares no fields.");
        }
    }

    /**
     * Every default has to survive the same validator an editor's save goes
     * through. A default that the site's own rules would refuse is a bug in the
     * type declaration, and install is where it should surface.
     */
    public function test_install_seeds_and_publishes_every_page_singleton(): void
    {
        $this->artisan('cms:install', ['--seed-defaults' => true])->assertSuccessful();

        $entries = app(EntryService::class);

        foreach (app(TypeRegistry::class)->all() as $type) {
            if ($type->defaults() === []) {
                continue;
            }

            $entry = $entries->locate($type, null);

            $this->assertNotNull($entry, "{$type->key()} was not seeded.");
            $this->assertSame('published', $entry['status'], "{$type->key()} was seeded but not published.");
        }
    }

    public function test_install_is_a_no_op_once_the_bootstrap_flag_exists(): void
    {
        $this->artisan('cms:install', ['--seed-defaults' => true])->assertSuccessful();

        $home = app(EntryService::class);
        $type = app(TypeRegistry::class)->get('home');
        $entry = $home->locate($type, null);

        $home->update($type, $entry['id'], ['hero_heading' => 'edited by marina'], [
            'base_revision_id' => $entry['current_version'],
        ]);
        $home->publish($type, $entry['id'], []);

        $this->artisan('cms:install', ['--seed-defaults' => true])->assertSuccessful();

        $this->assertSame(
            'edited by marina',
            $home->locate($type, null)['published_data']['hero_heading'],
        );
    }

    public function test_marinas_dash_and_geo_word_band_rules_are_enforced_on_drafts(): void
    {
        $this->artisan('cms:install', ['--seed-defaults' => true])->assertSuccessful();

        $entries = app(EntryService::class);
        $home = app(TypeRegistry::class)->get('home');
        $entry = $entries->locate($home, null);

        try {
            $entries->update($home, $entry['id'], ['hero_heading' => 'new york — where magic begins'], [
                'base_revision_id' => $entry['current_version'],
            ]);
            $this->fail('Expected the home heading dash to be refused.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('no_dashes', $exception->errors()['hero_heading'][0]);
        }

        $post = app(TypeRegistry::class)->get('post');

        try {
            $entries->create($post, ['geo_summary' => 'Too short']);
            $this->fail('Expected the GEO summary word band to be refused.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('word_band', $exception->errors()['geo_summary'][0]);
        }
    }

    /**
     * The whole migration mechanic in one assertion: the page renders the
     * literal its Blade template used to hardcode, whether or not anything has
     * been seeded.
     */
    public function test_page_singletons_render_their_defaults_before_anything_is_seeded(): void
    {
        $this->assertFalse(CmsSetting::isInstalled());

        $this->get('/shop')
            ->assertOk()
            ->assertSeeText((new ShopType)->defaults()['digital_heading'])
            ->assertSeeText((new ShopType)->defaults()['merch_kicker']);

        $this->get('/')
            ->assertOk()
            ->assertSeeText((new HomeType)->defaults()['hero_subheading']);
    }

    public function test_a_published_edit_reaches_the_public_page(): void
    {
        $this->artisan('cms:install', ['--seed-defaults' => true])->assertSuccessful();

        $entries = app(EntryService::class);
        $type = app(TypeRegistry::class)->get('travel_usa');
        $entry = $entries->locate($type, null);

        $entries->update($type, $entry['id'], ['heading' => 'ROAD TRIP USA'], [
            'base_revision_id' => $entry['current_version'],
        ]);

        $this->get('/travel-usa')
            ->assertOk()
            ->assertSeeText('TRAVEL USA')
            ->assertDontSeeText('ROAD TRIP USA');

        $entries->publish($type, $entry['id'], []);

        $this->get('/travel-usa')
            ->assertOk()
            ->assertSeeText('ROAD TRIP USA');
    }

    public function test_the_instagram_strip_is_content_rather_than_a_disk_read(): void
    {
        $this->artisan('cms:install', ['--seed-defaults' => true])->assertSuccessful();

        $entries = app(EntryService::class);
        $type = app(TypeRegistry::class)->get('instagram_feed');
        $entry = $entries->locate($type, null);

        $entries->update($type, $entry['id'], [
            'items' => [['index' => 1, 'path' => 'media/instagram/ig-01.jpg', 'alt' => 'A single curated tile']],
        ], ['base_revision_id' => $entry['current_version']]);
        $entries->publish($type, $entry['id'], []);

        $response = $this->get('/')->assertOk();

        $response->assertSee('alt="A single curated tile"', false);
        $this->assertSame(1, substr_count($response->getContent(), 'media/instagram/'));
    }

    public function test_the_instagram_importer_replaces_the_strip_rather_than_appending_to_it(): void
    {
        $this->artisan('cms:install', ['--seed-defaults' => true])->assertSuccessful();

        $this->artisan('cms:import-instagram', ['--publish' => true])->assertSuccessful();
        $this->artisan('cms:import-instagram', ['--publish' => true])->assertSuccessful();

        $type = app(TypeRegistry::class)->get('instagram_feed');
        $entry = app(EntryService::class)->locate($type, null);

        $this->assertCount(30, $entry['published_data']['items']);
    }

    /**
     * Marina calls these what her own navigation calls them.
     *
     * The publishing-system names — "Home page", "News and Press page", "Media
     * assets" — were the CMS describing its own furniture. She does not have a
     * Home page and a home; she has a home. Asserted here because a label is
     * the one thing in a type declaration that has no other test: nothing
     * breaks when it drifts, it just gets a little stranger every time.
     */
    public function test_every_type_is_named_the_way_the_site_owner_names_it(): void
    {
        $expected = [
            'home' => ['Home', 'Home', SiteMap::GROUP_PAGE],
            'news_and_press' => ['News & Press', 'News & Press', SiteMap::GROUP_PAGE],
            'travel_usa' => ['Travel USA', 'Travel USA', SiteMap::GROUP_PAGE],
            'how_i_create' => ['How I Create', 'How I Create', SiteMap::GROUP_PAGE],
            'free_events' => ['Free Events', 'Free Events', SiteMap::GROUP_PAGE],
            'shop' => ['Shop', 'Shop', SiteMap::GROUP_PAGE],
            'merch' => ['Merch', 'Merch', SiteMap::GROUP_PAGE],
            'accessibility_statement' => ['Accessibility', 'Accessibility', SiteMap::GROUP_PAGE],
            'instagram_feed' => ['Instagram', 'Instagram', SiteMap::GROUP_PAGE],
            'post' => ['Story', 'Stories', SiteMap::GROUP_COLLECTION],
            'event' => ['Event', 'Events', SiteMap::GROUP_COLLECTION],
            'page' => ['Page', 'Pages', SiteMap::GROUP_COLLECTION],
            'media_asset' => ['Photo description', 'Photo descriptions', SiteMap::GROUP_SETUP],
            'site_settings' => ['Settings', 'Settings', SiteMap::GROUP_SETUP],
        ];

        $types = app(TypeRegistry::class)->all();

        // Every type, and nothing but: a new one added without a name of its
        // own fails here rather than turning up on the dashboard as a card
        // called "Wix Import".
        $this->assertEqualsCanonicalizing(array_keys($expected), array_keys($types));

        foreach ($expected as $key => [$label, $plural, $group]) {
            $type = $types[$key];

            $this->assertSame($label, $type->label());
            $this->assertSame($plural, $type->pluralLabel());
            $this->assertSame($group, SiteMap::group($type), "{$key} is filed under the wrong heading.");

            // Every one of them says something, and says it as a sentence: the
            // card has a line for it either way, and a blank one is worse than
            // no card at all.
            $this->assertInstanceOf(Presentable::class, $type);
            $this->assertNotNull($type->blurb(), "{$key} has nothing to say for itself.");
            $this->assertStringEndsWith('.', $type->blurb());

            // No invented symbols. The plate gets the first letter of the name
            // she just read in the sidebar.
            $this->assertNull($type->glyph());
            $this->assertSame(mb_strtoupper(mb_substr($label, 0, 1)), SiteMap::glyph($type));
        }
    }

    /**
     * A label is written into prose — "New story", "Delete this story" — so it
     * has to survive being lowercased mid-sentence. "Story" does. "Home page"
     * did too, which is how it lasted this long; "News and Press page" did not.
     */
    public function test_a_label_reads_correctly_in_the_middle_of_a_sentence(): void
    {
        foreach (app(TypeRegistry::class)->all() as $key => $type) {
            $this->assertMatchesRegularExpression(
                '/^[A-Z]/',
                $type->label(),
                "{$key} has a label that does not start with a capital.",
            );

            $this->assertStringNotContainsString(
                ' page',
                $type->label(),
                "{$key} still calls itself a page. She calls it by its name.",
            );
        }
    }
}
