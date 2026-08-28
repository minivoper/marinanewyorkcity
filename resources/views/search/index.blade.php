@extends('layouts.app')

@php
    $seoTitle = 'Search Results | marina.newyorkcity';
    $seoDescription = 'Search marina.newyorkcity stories and events.';
@endphp

@section('content')
    @include('partials.page-head', [
        'eyebrow' => 'Search',
        'meta' => $searchTerm !== '' ? $searchTerm : null,
        'title' => 'SEARCH',
        'kicker' => 'Search marina.newyorkcity stories and events.',
    ])

    <section class="section page" style="padding-top: 0">
        <div class="wrap">
            <form class="search-bar fade-up" action="{{ route('search') }}" method="GET" role="search">
                <label class="visually-hidden" for="q">Search stories and events</label>
                <input id="q" name="q" type="search" value="{{ $searchTerm }}" placeholder="Search stories and events">
                <button class="btn" type="submit">Search</button>
            </form>

            @if ($searchTerm !== '')
                <div class="section-head-top fade-up" style="margin: clamp(48px, 5vw, 80px) 0 clamp(24px, 3vw, 40px)">
                    <p class="eyebrow">Stories — {{ $posts->count() }} {{ \Illuminate\Support\Str::plural('result', $posts->count()) }}</p>
                    <span class="rule" aria-hidden="true"></span>
                </div>
                <div class="card-grid">
                    @forelse ($posts as $postItem)
                        <a class="card fade-up" href="{{ route('posts.show', $postItem->slug) }}">
                            @if ($postItem->cover_path)
                                <div class="card-media">
                                    <img src="{{ asset($postItem->cover_path) }}" alt="{{ $postItem->title }}" loading="lazy">
                                    <span class="card-bar" aria-hidden="true"></span>
                                </div>
                            @endif
                            <div class="card-body">
                                <h3 class="card-title">{{ $postItem->title }}</h3>
                            </div>
                            <p class="card-excerpt">{{ $postItem->excerpt }}</p>
                        </a>
                    @empty
                        <p class="text-meta">No matching stories.</p>
                    @endforelse
                </div>

                <div class="section-head-top fade-up" style="margin: clamp(56px, 6vw, 90px) 0 clamp(24px, 3vw, 40px)">
                    <p class="eyebrow">Events — {{ $events->count() }} {{ \Illuminate\Support\Str::plural('result', $events->count()) }}</p>
                    <span class="rule" aria-hidden="true"></span>
                </div>
                <div class="card-grid">
                    @forelse ($events as $eventItem)
                        <a class="card fade-up" href="{{ route('events.show', $eventItem->slug) }}">
                            @if ($eventItem->cover_path)
                                <div class="card-media card-media--portrait">
                                    <img src="{{ asset($eventItem->cover_path) }}" alt="{{ $eventItem->title }}" loading="lazy">
                                    <span class="card-bar" aria-hidden="true"></span>
                                </div>
                            @endif
                            <div class="card-body">
                                <h3 class="card-title">{{ $eventItem->title }}</h3>
                            </div>
                            <p class="card-excerpt">{{ $eventItem->excerpt }}</p>
                        </a>
                    @empty
                        <p class="text-meta">No matching events.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </section>
@endsection
