@extends('layouts.app')

@php
    $seoTitle = $post->meta_title;
    $seoDescription = $post->meta_description;
    $seoImage = $post->cover_path;
    $seoType = 'article';
@endphp

@section('content')
    @include('partials.page-head', [
        'eyebrow' => $post->type === 'guide' ? 'Guide' : 'Story',
        'meta' => $post->published_at->format('M j, Y'),
        'title' => $post->title,
        'sentence' => true,
    ])

    <article class="section page" style="padding-top: 0">
        <div class="wrap">
            @if ($post->cover_path)
                <div class="card-media card-media--wide article-cover fade-up">
                    <div data-parallax="0.12" style="position: absolute; inset: -10% 0">
                        <img src="{{ asset($post->cover_path) }}" alt="{{ $post->title }}">
                    </div>
                </div>
            @endif

            <div class="article-layout">
                <aside class="article-aside fade-up">
                    <p class="eyebrow eyebrow--label">Author</p>
                    <p>Marina Kapler</p>
                    <p class="eyebrow eyebrow--label">Published</p>
                    <p>
                        {{ $post->published_at->format('F j, Y') }}
                        @if ($post->read_minutes)
                            · {{ $post->read_minutes }} min read
                        @endif
                    </p>
                    @if ($post->location_name)
                        <p class="eyebrow eyebrow--label">Location</p>
                        <p>{{ $post->location_name }}</p>
                    @endif
                </aside>

                <div class="article-body fade-up" data-delay="100">
                    <p class="t-lead article-lede">{{ $post->excerpt }}</p>
                    <div class="prose">{!! $post->body !!}</div>
                </div>
            </div>
        </div>
    </article>
@endsection
