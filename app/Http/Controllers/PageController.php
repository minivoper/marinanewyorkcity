<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Page;
use App\Models\Post;
use Eshlink\Cms\Facades\Cms;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;

class PageController extends Controller
{
    public function privacy(): View
    {
        return $this->renderPage('privacy-policy');
    }

    public function terms(): View
    {
        return $this->renderPage('terms-and-conditions');
    }

    public function about(): View
    {
        return $this->renderPage('about');
    }

    public function work(): View
    {
        return $this->renderPage('work-with-me');
    }

    public function press(): View
    {
        return $this->renderPage('press');
    }

    public function newsAndPress(): View
    {
        $posts = Post::published()
            ->select(['id', 'type', 'slug', 'title', 'excerpt', 'cover_path', 'published_at', 'read_minutes'])
            ->where('type', Post::TYPE_NEWS)
            ->latest('published_at')
            ->paginate(12);

        return view('pages.news-and-press', compact('posts'));
    }

    public function travelUsa(): View
    {
        return view('pages.travel-usa');
    }

    public function howICreate(): View
    {
        $featuredPost = Post::published()
            ->where('slug', Cms::value('how_i_create.featured_post_slug'))
            ->first();

        return view('pages.how-i-create', compact('featuredPost'));
    }

    public function shop(): View
    {
        return view('pages.shop');
    }

    public function merch(): View
    {
        return view('pages.merch');
    }

    public function freeEvents(): View
    {
        $events = Event::query()
            ->with(['occurrences' => function (HasMany $query): void {
                $query->oldest('starts_at');
            }])
            ->withMin('occurrences', 'starts_at')
            ->orderBy('occurrences_min_starts_at')
            ->get();

        return view('pages.free-events', compact('events'));
    }

    public function accessibility(): View
    {
        return view('pages.accessibility-statement');
    }

    private function renderPage(string $slug): View
    {
        $page = Page::query()->where('slug', $slug)->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
