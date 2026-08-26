<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\File;
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
            'instagramItems' => $this->instagramItems(),
        ]);
    }

    /**
     * @return array<int, array{local_path: string, caption: string}>
     */
    private function instagramItems(): array
    {
        $path = base_path('docs/wix-ref/instagram.json');

        if (! File::exists($path)) {
            return [];
        }

        $items = File::json($path)['items'] ?? [];

        return array_values(array_filter(
            $items,
            fn (array $item): bool => ($item['downloaded'] ?? false) && isset($item['local_path']),
        ));
    }
}
