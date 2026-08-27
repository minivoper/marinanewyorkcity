@extends('layouts.app')

@php
    use Eshlink\Cms\Facades\Cms;

    $seoTitle = Cms::value('news_and_press.seo_title');
    $seoDescription = Cms::value('news_and_press.seo_description');
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">@cms('news_and_press.heading')</h1>
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
                    <p class="text-sand">@cms('news_and_press.empty_message')</p>
                @endforelse
            </div>
            {{ $posts->links() }}
        </div>
    </section>
@endsection
