<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Eshlink\Cms\Facades\Cms;
use Eshlink\Cms\Support\CacheVersion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'newsPosts' => self::cards(Post::TYPE_NEWS, 5),
            'guidePosts' => self::cards(Post::TYPE_GUIDE, 4),
            'instagramItems' => Cms::value('instagram_feed.items', []),
            'instagramProfileUrl' => Cms::value('instagram_feed.profile_url'),
        ]);
    }

    /**
     * One row of cards, read once and then remembered.
     *
     * These two queries were the last thing on this site still asking the
     * database on an ordinary page view. Everything else it serves comes from a
     * file cache now, so the home page — the page nearly every visitor lands on
     * — was the reason a cluster that bills by the second for being awake never
     * got to sleep.
     *
     * **Invalidation is the CMS's own, not a guess at one.**
     * `EntryService::mutate()` bumps a version token for the type inside the
     * same transaction as the write, and that token is part of the key here, so
     * publishing a post makes this a miss on the very next read. Marina never
     * waits to see her own work, which is the one thing that would make a cache
     * here worse than no cache at all.
     *
     * The TTL behind it is not belt and braces, it is load bearing, and for a
     * reason worth naming: `Post::published()` is `published_at <= now()` and
     * nothing else. A post dated forward becomes public by the clock, with no
     * write anywhere and therefore no token to bump. Without a TTL a scheduled
     * post would wait for the next unrelated publish to appear. Five minutes is
     * how late one can now be.
     *
     * The other writer outside the seam is `PostSeeder`, and it runs at deploy
     * time against a cache that is cold anyway.
     *
     * @return Collection<int, Post>
     */
    private static function cards(string $type, int $limit): Collection
    {
        $key = 'marina.home.cards.'.$type.'.'.CacheVersion::of('post');

        return Cache::remember($key, 300, static fn (): Collection => Post::published()
            ->select(['id', 'type', 'slug', 'title', 'excerpt', 'cover_path', 'published_at'])
            ->where('type', $type)
            ->latest('published_at')
            ->limit($limit)
            ->get());
    }
}
