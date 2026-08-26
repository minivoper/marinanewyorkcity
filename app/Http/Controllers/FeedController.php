<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function rss(): Response
    {
        $posts = $this->posts();

        return response()
            ->view('feeds.rss', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function json(): JsonResponse
    {
        $baseUrl = rtrim(config('site.production_url'), '/');
        $items = $this->posts()->map(fn (Post $post): array => [
            'id' => $baseUrl.route('posts.show', $post->slug, false),
            'url' => $baseUrl.route('posts.show', $post->slug, false),
            'title' => $post->title,
            'summary' => $post->excerpt,
            'content_html' => $post->body,
            'date_published' => $post->published_at->toIso8601String(),
            'authors' => [['name' => config('site.author')]],
        ]);

        return response()->json([
            'version' => 'https://jsonfeed.org/version/1.1',
            'title' => config('app.name'),
            'home_page_url' => $baseUrl,
            'feed_url' => $baseUrl.route('feeds.json', absolute: false),
            'items' => $items,
        ])->header('Content-Type', 'application/feed+json; charset=UTF-8');
    }

    /**
     * @return Collection<int, Post>
     */
    private function posts(): Collection
    {
        return Post::published()->latest('published_at')->limit(20)->get();
    }
}
