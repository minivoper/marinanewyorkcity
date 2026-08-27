<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Eshlink\Cms\Facades\Cms;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $newsPosts = Post::published()
            ->select(['id', 'type', 'slug', 'title', 'excerpt', 'cover_path', 'published_at'])
            ->where('type', Post::TYPE_NEWS)
            ->latest('published_at')
            ->limit(5)
            ->get();

        $guidePosts = Post::published()
            ->select(['id', 'type', 'slug', 'title', 'excerpt', 'cover_path', 'published_at'])
            ->where('type', Post::TYPE_GUIDE)
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('home', [
            'newsPosts' => $newsPosts,
            'guidePosts' => $guidePosts,
            'instagramItems' => Cms::value('instagram_feed.items', []),
            'instagramProfileUrl' => Cms::value('instagram_feed.profile_url'),
        ]);
    }
}
