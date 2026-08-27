@extends('layouts.app')

@php
    use Eshlink\Cms\Facades\Cms;

    $seoTitle = Cms::value('home.seo_title');
    $seoDescription = Cms::value('home.seo_description');
    $transparentHeader = true;
@endphp

@section('content')
    <section class="hero">
        <div class="hero-media">
            <img src="{{ asset(Cms::value('home.hero_image')) }}" alt="{{ Cms::value('home.hero_image_alt') }}" fetchpriority="high">
        </div>
        <div class="hero-copy">
            <h1 class="display t-hero">@cms('home.hero_heading')</h1>
            <p class="hero-sub">@cms('home.hero_subheading')</p>
        </div>
    </section>

    <section class="section" aria-labelledby="whats-going-on">
        <div class="wrap">
            <div class="section-head fade-up">
                <h2 id="whats-going-on" class="display t-h2">@cms('home.news_heading')</h2>
                <p class="kicker">@cms('home.news_kicker')</p>
            </div>
            <div class="card-grid">
                @foreach ($newsPosts as $postItem)
                    <a class="card fade-up" href="{{ route('posts.show', $postItem->slug) }}">
                        @if ($postItem->cover_path)
                            <div class="card-media">
                                <img src="{{ asset($postItem->cover_path) }}" alt="{{ $postItem->title }}" loading="lazy">
                            </div>
                        @endif
                        <h3 class="card-title">{{ $postItem->title }}</h3>
                        <p class="card-meta">{{ $postItem->published_at->format('M j, Y') }}</p>
                        <p class="card-excerpt">{{ $postItem->excerpt }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="nyc-guides">
        <div class="wrap">
            <div class="section-head fade-up">
                <h2 id="nyc-guides" class="display t-h2">@cms('home.guides_heading')</h2>
                <p class="kicker">@cms('home.guides_kicker')</p>
            </div>
            <div class="card-grid">
                @foreach ($guidePosts as $postItem)
                    <a class="card fade-up" href="{{ route('posts.show', $postItem->slug) }}">
                        @if ($postItem->cover_path)
                            <div class="card-media">
                                <img src="{{ asset($postItem->cover_path) }}" alt="{{ $postItem->title }}" loading="lazy">
                            </div>
                        @endif
                        <h3 class="card-title">{{ $postItem->title }}</h3>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="social-media">
        <div class="wrap split">
            <div class="split-media fade-up">
                <img src="{{ asset(Cms::value('home.social_image')) }}" alt="{{ Cms::value('home.social_image_alt') }}" loading="lazy">
            </div>
            <div class="split-body fade-up">
                <h2 id="social-media" class="display t-h3">@cms('home.social_heading')</h2>
                @cms('home.social_body')

            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="content-licensing">
        <div class="wrap split split--flip">
            <div class="split-media fade-up">
                <img src="{{ asset(Cms::value('home.licensing_image')) }}" alt="{{ Cms::value('home.licensing_image_alt') }}" loading="lazy">
            </div>
            <div class="split-body fade-up">
                <h2 id="content-licensing" class="display display--sentence t-h3">@cms('home.licensing_heading')</h2>
                @cms('home.licensing_body')

                <a class="btn btn--ghost cluster" href="{{ route('contact.show') }}">@cms('home.licensing_cta_label')</a>
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="about-me">
        <div class="wrap split">
            <div class="split-media fade-up">
                <img src="{{ asset(Cms::value('home.about_image')) }}" alt="{{ Cms::value('home.about_image_alt') }}" loading="lazy">
            </div>
            <div class="split-body fade-up">
                <h2 id="about-me" class="display display--sentence t-h3">@cms('home.about_heading')</h2>
                @cms('home.about_body')

            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="instagram">
        <div class="wrap">
            <div class="section-head fade-up">
                <h2 id="instagram" class="display t-h2">@cms('home.instagram_heading')</h2>
            </div>
            <div class="ig-grid fade-up">
                @foreach ($instagramItems as $item)
                    <a class="card" href="{{ $instagramProfileUrl }}" rel="noopener" aria-label="Instagram post {{ $item['index'] }}">
                        <div class="card-media card-media--square">
                            <img src="{{ asset($item['path']) }}" alt="{{ $item['alt'] }}" loading="lazy">
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="clients-partners">
        <div class="wrap">
            <div class="section-head fade-up">
                <h2 id="clients-partners" class="display t-h2">@cms('home.partners_heading')</h2>
                <p class="kicker">@cms('home.partners_kicker')</p>
            </div>
            <div class="logo-grid fade-up">
                @foreach (Cms::value('home.partner_logos', []) as $logo)
                    <img src="{{ asset('media/about/'.$logo['file']) }}" alt="{{ $logo['alt'] }}" loading="lazy">
                @endforeach
            </div>
        </div>
    </section>
@endsection
