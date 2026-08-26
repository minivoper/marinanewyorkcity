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
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">{{ $heading }}</h1>
                @if ($type === 'guide')
                    <p class="kicker">what to do | where to stay | what to eat</p>
                @else
                    <p class="kicker">New York News and events</p>
                @endif
            </div>
            <div class="card-grid">
                @forelse ($posts as $postItem)
                    <a class="card fade-up" href="{{ route('posts.show', $postItem->slug) }}">
                        @if ($postItem->cover_path)
                            <div class="card-media">
                                <img src="{{ asset($postItem->cover_path) }}" alt="{{ $postItem->title }}" loading="lazy">
                            </div>
                        @endif
                        <h2 class="card-title">{{ $postItem->title }}</h2>
                        <p class="card-meta">
                            {{ $postItem->published_at->format('M j, Y') }}
                            @if ($postItem->read_minutes)
                                · {{ $postItem->read_minutes }} min read
                            @endif
                        </p>
                        <p class="card-excerpt">{{ $postItem->excerpt }}</p>
                    </a>
                @empty
                    <p class="text-sand">No published stories found.</p>
                @endforelse
            </div>
            {{ $posts->links() }}
        </div>
    </section>
@endsection
