<?php

namespace Tests\Feature;

use App\Models\Post;
use Eshlink\Cms\Support\CacheVersion;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The home page's two card rows, and the cache in front of them.
 *
 * They were the last thing on this site still asking the database on an
 * ordinary page view. Everything else it serves comes from a file cache now, so
 * the home page — the page nearly every visitor lands on — was the reason a
 * cluster that bills by the second for being awake never got to sleep.
 *
 * Everything here goes through the route rather than through the controller's
 * own helper. A test that rebuilds the caching it is checking passes whatever
 * the controller does, which is the failure this file is most at risk of.
 */
class HomeCardsCacheTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Not named `post()`: Laravel's TestCase already has an HTTP helper by that
     * name, and a private one here is a fatal at class load rather than a
     * failing test — the whole suite refuses to start.
     */
    private function publishPost(string $type, string $title): Post
    {
        // Her own factory, so this fixture keeps up with her schema instead of
        // being a list of columns somebody has to remember to extend.
        return Post::factory()->create([
            'type' => $type,
            'title' => $title,
            'slug' => str()->slug($title),
            'published_at' => now()->subMinute(),
        ]);
    }

    public function test_a_second_visitor_costs_the_database_nothing(): void
    {
        $this->publishPost(Post::TYPE_NEWS, 'Something happened');
        $this->publishPost(Post::TYPE_GUIDE, 'Where to eat');

        $this->get('/')->assertOk();

        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get('/')->assertOk();
        $posts = array_filter(
            DB::getQueryLog(),
            static fn (array $q): bool => str_contains($q['query'], '"posts"'),
        );
        DB::disableQueryLog();

        $this->assertSame([], $posts, 'The home page went back to the posts table on a warm cache.');
    }

    public function test_publishing_reaches_the_page_at_once_and_does_not_wait_for_a_ttl(): void
    {
        $this->publishPost(Post::TYPE_NEWS, 'The old headline');

        $this->get('/')->assertOk()->assertSeeText('The old headline');

        $this->publishPost(Post::TYPE_NEWS, 'The new headline');

        // What a publish through the CMS does, inside its own transaction: bump
        // the version token that is part of the cache key. Without this line the
        // assertion below fails, which is the proof that the token is what makes
        // this cache safe rather than the TTL.
        CacheVersion::bump('post');

        $this->get('/')
            ->assertOk()
            ->assertSeeText('The new headline');
    }

    public function test_the_two_rows_stay_their_own_rows(): void
    {
        $this->publishPost(Post::TYPE_NEWS, 'A news item');
        $this->publishPost(Post::TYPE_GUIDE, 'A guide to something');

        $response = $this->get('/')->assertOk();

        // One cache entry per row. Keyed on the type as well as the version,
        // because a single key would have served the guides their news.
        $response->assertSeeText('A news item');
        $response->assertSeeText('A guide to something');
    }
}
