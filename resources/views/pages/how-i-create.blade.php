@extends('layouts.app')

@php
    $seoTitle = 'HOW I CREATE | marina.newyorkcity';
    $seoDescription = 'How Marina Kapler creates cinematic New York City content with an iPhone.';
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">HOW I CREATE</h1>
            </div>
            @if ($featuredPost)
                <div class="card-grid card-grid--single">
                    <a class="card fade-up" href="{{ route('posts.show', $featuredPost->slug) }}">
                        @if ($featuredPost->cover_path)
                            <div class="card-media">
                                <img src="{{ asset($featuredPost->cover_path) }}" alt="{{ $featuredPost->title }}" loading="lazy">
                            </div>
                        @endif
                        <h2 class="card-title">{{ $featuredPost->title }}</h2>
                        <p class="card-excerpt">{{ $featuredPost->excerpt }}</p>
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
