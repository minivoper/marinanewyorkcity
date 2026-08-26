<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventOccurrence;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->with(['occurrences' => function (HasMany $query): void {
                $query->oldest('starts_at');
            }])
            ->withMin('occurrences', 'starts_at')
            ->orderBy('occurrences_min_starts_at')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    public function show(string $slug): View
    {
        $event = Event::query()
            ->with(['occurrences' => function (HasMany $query): void {
                $query->oldest('starts_at');
            }])
            ->where('slug', $slug)
            ->orWhereHas('occurrences', fn (Builder $query) => $query->where('occurrence_slug', $slug))
            ->firstOrFail();

        $selectedOccurrence = $event->occurrences
            ->first(fn (EventOccurrence $occurrence): bool => $occurrence->occurrence_slug === $slug);

        return view('events.show', compact('event', 'selectedOccurrence'));
    }
}
