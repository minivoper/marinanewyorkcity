@extends('layouts.app')

@php
    $seoTitle = 'Events | marina.newyorkcity';
    $seoDescription = 'A calendar of New York City events selected by Marina Kapler.';
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">EVENTS</h1>
            </div>
            @forelse ($events as $eventItem)
                <article class="event-row fade-up">
                    <div class="event-row-media">
                        @if ($eventItem->cover_path)
                            <img src="{{ asset($eventItem->cover_path) }}" alt="{{ $eventItem->title }}" loading="lazy">
                        @endif
                    </div>
                    <div>
                        <h2 class="card-title">
                            <a href="{{ route('events.show', $eventItem->slug) }}">{{ $eventItem->title }}</a>
                        </h2>
                        @if ($eventItem->venue_name)
                            <p class="card-meta">{{ $eventItem->venue_name }}@if($eventItem->venue_address) — {{ $eventItem->venue_address }}@endif</p>
                        @endif
                        <p class="card-excerpt">{{ $eventItem->excerpt }}</p>
                        @if ($eventItem->occurrences->isNotEmpty())
                            <div class="date-pill-list cluster">
                                @foreach ($eventItem->occurrences->take(6) as $occurrence)
                                    <a class="date-pill" href="{{ route('events.show', $occurrence->occurrence_slug) }}">
                                        {{ $occurrence->starts_at->format('M j, g:i A') }}
                                    </a>
                                @endforeach
                                @if ($eventItem->occurrences->count() > 6)
                                    <span class="date-pill">+{{ $eventItem->occurrences->count() - 6 }} more</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <a class="btn btn--ghost" href="{{ route('events.show', $eventItem->slug) }}">More info</a>
                </article>
            @empty
                <p class="text-sand">No events found.</p>
            @endforelse
            {{ $events->links() }}
        </div>
    </section>
@endsection
