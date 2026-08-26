@extends('layouts.app')

@php
    $seoTitle = $event->meta_title;
    $seoDescription = $event->meta_description;
    $seoImage = $event->cover_path;
@endphp

@section('content')
    <article class="section page">
        <div class="wrap">
            <div class="split">
                <div class="split-media fade-up">
                    @if ($event->cover_path)
                        <img src="{{ asset($event->cover_path) }}" alt="{{ $event->title }}">
                    @endif
                </div>
                <div class="split-body fade-up">
                    <h1 class="page-title">{{ $event->title }}</h1>
                    <p class="t-lead text-sand">{{ $event->excerpt }}</p>
                    @if ($event->venue_name)
                        <p class="card-meta">{{ $event->venue_name }}@if($event->venue_address) — {{ $event->venue_address }}@endif</p>
                    @endif

                    @if ($selectedOccurrence)
                        <h2 class="display t-h5 stack-title">Selected date</h2>
                        <p class="text-sand">
                            {{ $selectedOccurrence->starts_at->format('F j, Y g:i A') }}
                            @if ($selectedOccurrence->ends_at)
                                – {{ $selectedOccurrence->ends_at->format('g:i A') }}
                            @endif
                        </p>
                    @endif

                    @if ($event->occurrences->isNotEmpty())
                        <h2 class="display t-h5 stack-title">Dates</h2>
                        <div class="date-pill-list">
                            @foreach ($event->occurrences as $occurrence)
                                <a
                                    class="date-pill {{ $selectedOccurrence && $selectedOccurrence->is($occurrence) ? 'is-active' : '' }}"
                                    href="{{ route('events.show', $occurrence->occurrence_slug) }}"
                                >
                                    {{ $occurrence->starts_at->format('M j, Y g:i A') }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="prose prose--follow fade-up">{!! $event->body !!}</div>
        </div>
    </article>
@endsection
