@extends('layouts.app')

@php
    $seoTitle = $post->meta_title;
    $seoDescription = $post->meta_description;
    $seoImage = $post->cover_path;
    $seoType = 'article';
@endphp

@section('content')
    <article class="section page">
        <div class="wrap">
            <header class="section-head fade-up article-cover">
                <h1 class="page-title">{{ $post->title }}</h1>
                <p class="card-meta">
                    By Marina Kapler · {{ $post->published_at->format('F j, Y') }}
                    @if ($post->read_minutes)
                        · {{ $post->read_minutes }} min read
                    @endif
                    @if ($post->location_name)
                        · {{ $post->location_name }}
                    @endif
                </p>
            </header>

            @if ($post->cover_path)
                <div class="fade-up article-cover">
                    <img src="{{ asset($post->cover_path) }}" alt="{{ $post->title }}">
                </div>
            @endif

            <p class="t-lead text-sand fade-up article-lede">{{ $post->excerpt }}</p>

            <div class="prose fade-up">{!! $post->body !!}</div>
        </div>
    </article>
@endsection
