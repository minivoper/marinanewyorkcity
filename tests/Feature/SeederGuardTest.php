<?php

namespace Tests\Feature;

use App\Models\EventOccurrence;
use App\Models\Post;
use Eshlink\Cms\Models\CmsSetting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * The deploy command is `migrate --force --seed`, and these seeders
 * `updateOrCreate` content. Once the CMS is installed, running them again would
 * quietly revert every edit made since the last deploy.
 */
class SeederGuardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seeding_populates_a_fresh_database(): void
    {
        $this->seed();

        $this->assertGreaterThan(0, Post::query()->count());
    }

    public function test_seeding_is_a_no_op_once_the_cms_bootstrap_flag_exists(): void
    {
        $this->seed();

        $post = Post::query()->where('slug', 'how-to-create-a-cinematic-video-with-an-iphone')->firstOrFail();
        $post->update(['title' => 'A title Marina edited after launch']);

        CmsSetting::put(CmsSetting::INSTALLED_FLAG, now()->toIso8601String());

        $this->seed();

        $this->assertSame('A title Marina edited after launch', $post->fresh()->title);
    }

    /**
     * `EventSeeder` deletes and recreates every occurrence, which would move a
     * dozen `/event-details/{slug}` pages onto new rows on every deploy.
     */
    public function test_seeding_does_not_recreate_event_occurrences_once_installed(): void
    {
        $this->seed();

        $occurrenceIds = EventOccurrence::query()->orderBy('id')->pluck('id')->all();

        CmsSetting::put(CmsSetting::INSTALLED_FLAG, now()->toIso8601String());

        $this->seed();

        $this->assertSame(
            $occurrenceIds,
            EventOccurrence::query()->orderBy('id')->pluck('id')->all(),
        );
    }
}
