<?php

namespace Tests\Feature;

use App\Cms\Types\SettingsType;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\Post;
use App\Models\Setting;
use Eshlink\Cms\Services\EntryService;
use Eshlink\Cms\Support\TypeRegistry;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Marina's models are wrapped, not migrated: the CMS reads and writes the same
 * `posts`, `events` and `settings` rows her controllers already query, and the
 * draft layer lives beside them in `cms_drafts`.
 */
class CmsModelSourceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_a_post_draft_does_not_change_the_public_page_until_it_is_published(): void
    {
        $post = Post::factory()->create(['slug' => 'a-wrapped-story', 'title' => 'The published title']);

        $entries = app(EntryService::class);
        $type = app(TypeRegistry::class)->get('post');
        $entry = $entries->locate($type, 'a-wrapped-story');

        $entries->update($type, $entry['id'], ['title' => 'The drafted title'], [
            'base_revision_id' => $entry['current_version'],
        ]);

        $this->assertSame('The published title', $post->fresh()->title);

        $this->get('/post/a-wrapped-story')
            ->assertOk()
            ->assertSeeText('The published title')
            ->assertDontSeeText('The drafted title');

        $entries->publish($type, $entry['id'], []);

        $this->assertSame('The drafted title', $post->fresh()->title);
        $this->get('/post/a-wrapped-story')->assertOk()->assertSeeText('The drafted title');
    }

    /**
     * The published projection has to answer exactly as the site does, which
     * means using her own `Post::published()` scope: a story dated tomorrow is
     * scheduled, not live.
     */
    public function test_the_post_source_reads_through_marinas_own_published_scope(): void
    {
        Post::factory()->create(['slug' => 'live-story', 'published_at' => now()->subDay()]);
        Post::factory()->create(['slug' => 'future-story', 'published_at' => now()->addWeek()]);

        $type = app(TypeRegistry::class)->get('post');
        $slugs = $type->source()->published($type)->pluck('slug');

        $this->assertContains('live-story', $slugs);
        $this->assertNotContains('future-story', $slugs);
    }

    public function test_publishing_an_event_syncs_occurrences_without_moving_the_existing_dates(): void
    {
        $this->seed();

        $event = Event::query()->where('slug', 'bryant-park-movie-nights')->firstOrFail();
        $firstOccurrenceId = $event->occurrences()->oldest('starts_at')->firstOrFail()->id;

        $entries = app(EntryService::class);
        $type = app(TypeRegistry::class)->get('event');
        $entry = $entries->locate($type, 'bryant-park-movie-nights');

        $occurrences = $entry['data']['occurrences'];
        $this->assertCount(12, $occurrences);

        // Drop the last date and add one of our own.
        array_pop($occurrences);
        $occurrences[] = [
            'starts_at' => '2026-10-05T17:00:00+00:00',
            'ends_at' => null,
            'occurrence_slug' => 'bryant-park-movie-nights-2026-10-05',
        ];

        $entries->update($type, $entry['id'], ['occurrences' => $occurrences], [
            'base_revision_id' => $entry['current_version'],
        ]);
        $entries->publish($type, $entry['id'], []);

        $event->refresh();

        $this->assertCount(12, $event->occurrences);
        $this->assertSame(
            $firstOccurrenceId,
            $event->occurrences()->oldest('starts_at')->firstOrFail()->id,
            'Matching by id is what keeps an untouched date at the address the sitemap already lists.',
        );
        $this->get('/event-details/bryant-park-movie-nights-2026-10-05')->assertOk();
        $this->get('/event-details/bryant-park-movie-nights-2026-09-28')->assertNotFound();
    }

    public function test_every_imported_post_and_page_can_be_saved_after_one_time_normalization(): void
    {
        $this->seed();

        $exit = Artisan::call('cms:normalize-content', ['--type' => ['post', 'page']]);
        $this->assertSame(0, $exit, Artisan::output());

        $entries = app(EntryService::class);
        $types = app(TypeRegistry::class);

        foreach (['post' => 7, 'page' => 5] as $key => $expected) {
            $type = $types->get($key);
            $records = $entries->list($type);

            $this->assertCount($expected, $records);

            foreach ($records as $record) {
                $updated = $entries->update($type, $record['id'], ['body' => $record['data']['body']], [
                    'base_revision_id' => $record['current_version'],
                ]);

                $this->assertSame($record['data']['body'], $updated['data']['body']);
            }
        }
    }

    public function test_a_typed_local_event_time_and_an_untouched_resave_are_byte_stable(): void
    {
        $this->seed();

        $event = Event::query()->where('slug', 'bryant-park-movie-nights')->firstOrFail();
        $entries = app(EntryService::class);
        $type = app(TypeRegistry::class)->get('event');
        $entry = $entries->find($type, (string) $event->id);
        $occurrences = $entry['data']['occurrences'];
        $occurrences[0]['starts_at'] = '2026-07-13T19:30';

        $updated = $entries->update($type, $entry['id'], ['occurrences' => $occurrences], [
            'base_revision_id' => $entry['current_version'],
        ]);
        $entries->publish($type, $entry['id'], ['base_revision_id' => $updated['current_version']]);

        $firstBytes = DB::table('event_occurrences')
            ->where('event_id', $event->id)
            ->orderBy('id')
            ->pluck('starts_at')
            ->all();

        $this->assertSame('2026-07-13 19:30:00', $firstBytes[0]);

        $entry = $entries->find($type, (string) $event->id);
        $this->assertSame('2026-07-13T19:30:00-04:00', $entry['data']['occurrences'][0]['starts_at']);

        $updated = $entries->update($type, $entry['id'], ['occurrences' => $entry['data']['occurrences']], [
            'base_revision_id' => $entry['current_version'],
        ]);
        $entries->publish($type, $entry['id'], ['base_revision_id' => $updated['current_version']]);

        $secondBytes = DB::table('event_occurrences')
            ->where('event_id', $event->id)
            ->orderBy('id')
            ->pluck('starts_at')
            ->all();

        $this->assertSame($firstBytes, $secondBytes);
    }

    public function test_a_reverted_event_matches_a_replacement_occurrence_by_its_public_slug(): void
    {
        $this->seed();

        $event = Event::query()->where('slug', 'bryant-park-movie-nights')->firstOrFail();
        $entries = app(EntryService::class);
        $type = app(TypeRegistry::class)->get('event');
        $entry = $entries->find($type, (string) $event->id);

        $updated = $entries->update($type, $entry['id'], ['excerpt' => $entry['data']['excerpt']], [
            'base_revision_id' => $entry['current_version'],
        ]);
        $published = $entries->publish($type, $entry['id'], ['base_revision_id' => $updated['current_version']]);

        $original = $event->occurrences()->orderBy('id')->firstOrFail();
        $attributes = $original->only(['starts_at', 'ends_at', 'occurrence_slug']);
        $originalId = $original->id;
        $original->delete();
        $replacement = $event->occurrences()->create($attributes);

        $this->assertNotSame($originalId, $replacement->id);

        $reverted = $entries->revert($type, $entry['id'], 1, [
            'base_revision_id' => $published['current_version'],
        ]);
        $entries->publish($type, $entry['id'], ['base_revision_id' => $reverted['current_version']]);

        $this->assertDatabaseHas('event_occurrences', [
            'id' => $replacement->id,
            'occurrence_slug' => $replacement->occurrence_slug,
        ]);
        $this->assertSame(12, EventOccurrence::query()->where('event_id', $event->id)->count());
    }

    /**
     * `settings.value` is a JSON column behind an Eloquent cast, so the field
     * an editor sees is JSON text and the model still hands back an array.
     *
     * Marina no longer edits settings this way — {@see SiteSettingsTest}
     * covers the form of labelled boxes she actually gets, and `SettingsType` is
     * no longer listed in `config/cms.php`. It is still the site's only worked
     * example of adapting a cast column to a scalar field schema, so the type is
     * built here directly rather than looked up in the registry.
     */
    public function test_a_setting_round_trips_through_its_json_column(): void
    {
        $setting = Setting::query()->create([
            'key' => 'site.socials',
            'value' => ['instagram' => 'https://www.instagram.com/marina.newyorkcity/'],
        ]);

        $entries = app(EntryService::class);
        $type = new SettingsType;
        $entry = $entries->find($type, (string) $setting->id);

        $this->assertSame(
            ['instagram' => 'https://www.instagram.com/marina.newyorkcity/'],
            json_decode($entry['data']['value'], true),
        );

        $entries->update($type, $entry['id'], [
            'value' => json_encode(['instagram' => 'https://www.instagram.com/marina.newyorkcity/', 'tiktok' => 'https://www.tiktok.com/@marina.newyorkcity']),
        ], ['base_revision_id' => $entry['current_version']]);
        $entries->publish($type, $entry['id'], []);

        $this->assertSame([
            'instagram' => 'https://www.instagram.com/marina.newyorkcity/',
            'tiktok' => 'https://www.tiktok.com/@marina.newyorkcity',
        ], $setting->fresh()->value);
    }

    public function test_malformed_setting_json_is_refused_rather_than_stored_as_an_empty_array(): void
    {
        $setting = Setting::query()->create(['key' => 'site.name', 'value' => ['value' => 'marina.newyorkcity']]);

        $entries = app(EntryService::class);
        $type = new SettingsType;
        $entry = $entries->find($type, (string) $setting->id);

        $this->expectException(ValidationException::class);

        $entries->update($type, $entry['id'], ['value' => 'not json at all'], [
            'base_revision_id' => $entry['current_version'],
        ]);
    }
}
