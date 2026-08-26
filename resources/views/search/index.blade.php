@extends('layouts.app')

@php
    $seoTitle = 'Search Results | marina.newyorkcity';
    $seoDescription = 'Search marina.newyorkcity stories and events.';
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">SEARCH</h1>
            </div>

            <form class="search-bar fade-up" action="{{ route('search') }}" method="GET" role="search">
                <label class="visually-hidden" for="q">Search stories and events</label>
                <input id="q" name="q" type="search" value="{{ $searchTerm }}" placeholder="Search stories and events">
                <button class="btn" type="submit">Search</button>
            </form>

            @if ($searchTerm !== '')
                <h2 class="display t-h4 stack-title">Results for “{{ $searchTerm }}”</h2>

                <h3 class="display t-h5 stack-title">Stories</h3>
                <div class="card-grid cluster">
                    @forelse ($posts as $postItem)
                        <a class="card" href="{{ route('posts.show', $postItem->slug) }}">
                            @if ($postItem->cover_path)
                                <div class="card-media">
                                    <img src="{{ asset($postItem->cover_path) }}" alt="{{ $postItem->title }}" loading="lazy">
                                </div>
                            @endif
                            <h4 class="card-title">{{ $postItem->title }}</h4>
                            <p class="card-excerpt">{{ $postItem->excerpt }}</p>
                        </a>
                    @empty
                        <p class="text-sand">No matching stories.</p>
                    @endforelse
                </div>

                <h3 class="display t-h5 stack-title">Events</h3>
                <div class="card-grid cluster">
                    @forelse ($events as $eventItem)
                        <a class="card" href="{{ route('events.show', $eventItem->slug) }}">
                            @if ($eventItem->cover_path)
                                <div class="card-media card-media--portrait">
                                    <img src="{{ asset($eventItem->cover_path) }}" alt="{{ $eventItem->title }}" loading="lazy">
                                </div>
                            @endif
                            <h4 class="card-title">{{ $eventItem->title }}</h4>
                            <p class="card-excerpt">{{ $eventItem->excerpt }}</p>
                        </a>
                    @empty
                        <p class="text-sand">No matching events.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </section>
@endsection
