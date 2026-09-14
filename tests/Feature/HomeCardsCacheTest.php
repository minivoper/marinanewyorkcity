<?php

namespace Tests\Feature;

use App\Models\Post;
use Eshlink\Cms\Support\CacheVersion;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
 *
 * **And it runs on a real file store, not the array store phpunit.xml selects.**
 * That is not a detail. The array store keeps the live object and never
 * serializes anything, so under it every assertion here passes while production
 * serves a 500: this app sets `cache.serializable_classes` to `false`, so a
 * cached Eloquent model comes back from disk as `__PHP_Incomplete_Class`. That
 * is exactly how the first version of this change reached production. The store
 * is pointed at a temporary directory so the suite neither reads nor destroys
 * anything in `storage/framework/cache`.
 */
class HomeCardsCacheTest extends TestCase
{
    use LazilyRefreshDatabase;

    private string $cacheDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheDir = sys_get_temp_dir().'/marina-cards-'.bin2hex(random_bytes(6));

        Config::set('cache.default', 'file');
        Config::set('cache.stores.file.path', $this->cacheDir);
        Config::set('cache.stores.file.lock_path', $this->cacheDir);

        // The setting the production failure turned on. Asserted rather than
        // assumed: if it is ever relaxed in config, this test should stop
        // claiming to cover the thing it is named for.
        $this->assertFalse(
            Config::get('cache.serializable_classes'),
            'This test is only meaningful while the cache refuses to unserialize classes.',
        );

        Cache::purge('file');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->cacheDir);

        parent::tearDown();
    }

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

    public function test_the_two_rows_stay_their_own_rows_and_survive_the_round_trip(): void
    {
        $post = $this->publishPost(Post::TYPE_NEWS, 'A news item');
        $this->publishPost(Post::TYPE_GUIDE, 'A guide to something');

        // Cold, then warm. The warm read is the one that goes through the
        // cache, and therefore the only one that can catch a value that does
        // not survive being written to disk and read back.
        $this->get('/')->assertOk();

        $response = $this->get('/')->assertOk();

        // One cache entry per row. Keyed on the type as well as the version,
        // because a single key would have served the guides their news.
        $response->assertSeeText('A news item');
        $response->assertSeeText('A guide to something');

        // The date the card prints, off a value that spent the request as a
        // string in a cache file. The view calls `format()` on it, so a
        // published_at that came back unhydrated would be a 500 rather than a
        // wrong date, and this pins the cast that prevents it.
        $response->assertSeeText($post->published_at->format('M j, Y'));
    }
}
