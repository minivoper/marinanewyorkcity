<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->route('type');
        $type = in_array($type, [Post::TYPE_NEWS, Post::TYPE_GUIDE], true) ? $type : null;

        $posts = Post::published()
            ->select(['id', 'type', 'slug', 'title', 'excerpt', 'published_at', 'read_minutes', 'location_name'])
            ->when($type, fn (Builder $query): Builder => $query->where('type', $type))
            ->latest('published_at')
            ->paginate(12);

        return view('posts.index', compact('posts', 'type'));
    }

    public function show(string $slug): View
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        return view('posts.show', compact('post'));
    }
}
