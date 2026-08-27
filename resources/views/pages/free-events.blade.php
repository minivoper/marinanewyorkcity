@extends('layouts.app')

@php
    use Eshlink\Cms\Facades\Cms;

    $seoTitle = Cms::value('free_events.seo_title');
    $seoDescription = Cms::value('free_events.seo_description');
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">@cms('free_events.heading')</h1>
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
                        @if ($eventItem->occurrences->isNotEmpty())
                            <p class="card-meta">
                                {{ $eventItem->occurrences->first()->starts_at->format('M j, Y g:i A') }}
                                @if ($eventItem->occurrences->count() > 1)
                                    · +{{ $eventItem->occurrences->count() - 1 }} more dates
                                @endif
                            </p>
                        @endif
                        @if ($eventItem->venue_name)
                            <p class="card-excerpt">{{ $eventItem->venue_name }}</p>
                        @endif
                    </div>
                    <a class="btn btn--ghost" href="{{ route('events.show', $eventItem->slug) }}">@cms('free_events.cta_label')</a>
                </article>
            @empty
                <p class="text-sand">@cms('free_events.empty_message')</p>
            @endforelse
        </div>
    </section>
@endsection
