@extends('layouts.app')

@php
    use Eshlink\Cms\Facades\Cms;

    $seoTitle = Cms::value('how_i_create.seo_title');
    $seoDescription = Cms::value('how_i_create.seo_description');
@endphp

@section('content')
    @include('partials.page-head', [
        'eyebrow' => 'Process',
        'meta' => 'Featured story',
        'title' => Cms::value('how_i_create.heading'),
        'kicker' => Cms::value('how_i_create.seo_description'),
    ])

    <section class="section page" style="padding-top: 0">
        <div class="wrap">
            @if ($featuredPost)
                {{-- One story, so it gets the full width as a split rather than
                     a single lonely card in a three-column grid. --}}
                <a class="card split fade-up" href="{{ route('posts.show', $featuredPost->slug) }}">
                    @if ($featuredPost->cover_path)
                        <div class="card-media">
                            <img src="{{ asset($featuredPost->cover_path) }}" alt="{{ $featuredPost->title }}" loading="lazy">
                            <span class="card-bar" aria-hidden="true"></span>
                        </div>
                    @endif
                    <div>
                        <p class="eyebrow">Featured</p>
                        <h2 class="display display--sentence t-h3" style="margin-top: 20px">{{ $featuredPost->title }}</h2>
                        <p class="card-excerpt" style="font-size: 16.5px; max-width: 52ch; margin-top: 22px">{{ $featuredPost->excerpt }}</p>
                        <span class="eyebrow eyebrow--label" style="display: inline-block; margin-top: 32px; color: var(--color-sand)">Read the story &rarr;</span>
                    </div>
                </a>
            @endif
        </div>
    </section>
@endsection
