<?php

namespace Tests\Feature;

use App\Cms\Types\HomeType;
use App\Cms\Types\ShopType;
use Eshlink\Cms\Contracts\ContentType;
use Eshlink\Cms\Models\CmsSetting;
use Eshlink\Cms\Services\EntryService;
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
}
