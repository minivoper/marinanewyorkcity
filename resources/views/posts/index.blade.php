@extends('layouts.app')

@php
    $label = $type ? ucfirst($type) : 'Blog';
    $seoTitle = ($type ? strtoupper($label) : "WHATS GOING ON").' | marina.newyorkcity';
    $seoDescription = $type
        ? "New York City {$type} stories by Marina Kapler."
        : 'New York City news and guides by Marina Kapler.';
    $heading = match ($type) {
        'news' => "WHAT'S GOING ON",
        'guide' => 'NYC GUIDES',
        default => "WHAT'S GOING ON",
    };
    $kicker = $type === 'guide'
        ? 'what to do | where to stay | what to eat'
        : 'New York News and events';
@endphp

@section('content')
    @include('partials.page-head', [
        'eyebrow' => 'Journal',
        'meta' => $posts->total().' '.\Illuminate\Support\Str::plural('story', $posts->total()),
        'title' => $heading,
        'kicker' => $kicker,
    ])

    <section class="section page" style="padding-top: 0">
        <div class="wrap">
            {{-- The three collections were only reachable from the footer before. --}}
            <div class="date-pill-list fade-up" style="margin: 0 0 clamp(36px, 4vw, 60px)">
                <a class="date-pill {{ request()->routeIs('posts.index') ? 'is-active' : '' }}" href="{{ route('posts.index') }}">All stories</a>
                <a class="date-pill {{ request()->routeIs('posts.news') ? 'is-active' : '' }}" href="{{ route('posts.news') }}">News</a>
                <a class="date-pill {{ request()->routeIs('posts.guides') ? 'is-active' : '' }}" href="{{ route('posts.guides') }}">NYC Guides</a>
            </div>

            <div class="card-grid">
                @forelse ($posts as $index => $postItem)
                    <a class="card fade-up" href="{{ route('posts.show', $postItem->slug) }}">
                        @if ($postItem->cover_path)
                            <div class="card-media">
                                <img src="{{ asset($postItem->cover_path) }}" alt="{{ $postItem->title }}" loading="lazy">
                                <span class="card-bar" aria-hidden="true"></span>
                                <span class="card-tag">{{ $postItem->type === 'guide' ? 'Guide' : 'News' }}</span>
                            </div>
                        @endif
                        <div class="card-body">
                            <span class="card-index" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h2 class="card-title">{{ $postItem->title }}</h2>
                                <p class="card-meta">
                                    {{ $postItem->published_at->format('M j, Y') }}
                                    @if ($postItem->read_minutes)
                                        · {{ $postItem->read_minutes }} min read
                                    @endif
                                </p>
                                <p class="card-excerpt">{{ $postItem->excerpt }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-sand">No published stories found.</p>
                @endforelse
            </div>
            {{ $posts->links() }}
        </div>
    </section>
@endsection
