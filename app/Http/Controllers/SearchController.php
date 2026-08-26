<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $searchTerm = $request->string('q')->trim()->limit(100)->toString();
        $posts = new Collection;
        $events = new Collection;

        if ($searchTerm !== '') {
            $posts = Post::published()
                ->where(function (Builder $query) use ($searchTerm): void {
                    $query->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                        ->orWhere('body', 'like', "%{$searchTerm}%");
                })
                ->latest('published_at')
                ->limit(25)
                ->get();

            $events = Event::query()
                ->where(function (Builder $query) use ($searchTerm): void {
                    $query->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                        ->orWhere('body', 'like', "%{$searchTerm}%")
                        ->orWhere('venue_name', 'like', "%{$searchTerm}%");
                })
                ->latest()
                ->limit(25)
                ->get();
        }

        return view('search.index', compact('searchTerm', 'posts', 'events'));
    }
}
