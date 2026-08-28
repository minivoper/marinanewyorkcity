@extends('layouts.app')

@php
    $seoTitle = $event->meta_title;
    $seoDescription = $event->meta_description;
    $seoImage = $event->cover_path;
@endphp

@section('content')
    @include('partials.page-head', [
        'eyebrow' => 'Event',
        'meta' => $selectedOccurrence
            ? $selectedOccurrence->starts_at->format('M j, Y')
            : optional($event->occurrences->first())->starts_at?->format('M j, Y'),
        'title' => $event->title,
        'sentence' => true,
    ])

    <article class="section page" style="padding-top: 0">
        <div class="wrap">
            <div class="split">
                <div class="split-media split-media--ruled fade-up">
                    @if ($event->cover_path)
                        <div data-parallax="0.09">
                            <img src="{{ asset($event->cover_path) }}" alt="{{ $event->title }}">
                        </div>
                    @endif
                </div>
                <div class="split-body fade-up" data-delay="120">
                    <p class="t-lead text-sand" style="margin: 0">{{ $event->excerpt }}</p>

                    @if ($event->venue_name)
                        <div style="margin-top: 34px; border-top: 1px solid var(--hairline-soft); padding-top: 18px">
                            <p class="eyebrow eyebrow--label">Venue</p>
                            <p style="margin: 10px 0 0">{{ $event->venue_name }}@if($event->venue_address) — {{ $event->venue_address }}@endif</p>
                        </div>
                    @endif

                    @if ($selectedOccurrence)
                        <div style="margin-top: 24px; border-top: 1px solid var(--hairline-soft); padding-top: 18px">
                            <p class="eyebrow eyebrow--label">Selected date</p>
                            <p style="margin: 10px 0 0">
                                {{ $selectedOccurrence->starts_at->format('F j, Y g:i A') }}
                                @if ($selectedOccurrence->ends_at)
                                    – {{ $selectedOccurrence->ends_at->format('g:i A') }}
                                @endif
                            </p>
                        </div>
                    @endif

                    @if ($event->occurrences->isNotEmpty())
                        <div style="margin-top: 24px; border-top: 1px solid var(--hairline-soft); padding-top: 18px">
                            <p class="eyebrow eyebrow--label">Dates</p>
                            <div class="date-pill-list" style="margin-top: 14px">
                                @foreach ($event->occurrences as $occurrence)
                                    <a
                                        class="date-pill {{ $selectedOccurrence && $selectedOccurrence->is($occurrence) ? 'is-active' : '' }}"
                                        href="{{ route('events.show', $occurrence->occurrence_slug) }}"
                                    >
                                        {{ $occurrence->starts_at->format('M j, Y g:i A') }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="prose prose--follow fade-up">{!! $event->body !!}</div>
        </div>
    </article>
@endsection
