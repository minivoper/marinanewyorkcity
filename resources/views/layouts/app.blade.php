<!DOCTYPE html>
<html lang="en-US" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-seo-head
        :title="$seoTitle ?? null"
        :description="$seoDescription ?? null"
        :image="$seoImage ?? null"
        :type="$seoType ?? 'website'"
    />
    <x-json-ld
        :post="$post ?? null"
        :event="$event ?? null"
        :occurrence="$selectedOccurrence ?? null"
    />
    {{-- The mark is near-white on a transparent field, so these are composited
         onto the brand black — left alone it disappears against a light tab.
         favicon.ico stays because browsers request it unprompted; the one that
         was there was zero bytes, which is worse than none. --}}
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#000000">

    <link rel="preload" href="/fonts/arial-w01-black.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/avenir-lt-w01_35-light.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/din-next-w01-light.woff2" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- The header is fixed on every page now, transparent over a hero and
         solid once scrolled. One header, one behaviour, no per-page variant. --}}
    <header class="site-header">
        <div class="wrap">
            <a class="site-logo" href="{{ route('home') }}" aria-label="marina.newyorkcity home">
                <img src="{{ asset('media/brand/logo.png') }}" alt="marina.newyorkcity" width="62" height="62">
            </a>
            <nav id="primary-nav" class="site-nav" aria-label="Primary navigation">
                <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
                <a href="{{ route('pages.news-and-press') }}" @if(request()->routeIs('pages.news-and-press')) aria-current="page" @endif>News and Press</a>
                <a href="{{ route('pages.free-events') }}" @if(request()->routeIs('pages.free-events')) aria-current="page" @endif>Free Events</a>
                <a href="{{ route('pages.travel-usa') }}" @if(request()->routeIs('pages.travel-usa')) aria-current="page" @endif>Travel USA</a>
                <a href="{{ route('pages.how-i-create') }}" @if(request()->routeIs('pages.how-i-create')) aria-current="page" @endif>How I Create</a>
                <a href="{{ route('pages.shop') }}" @if(request()->routeIs('pages.shop')) aria-current="page" @endif>Shop</a>
                <a href="{{ route('contact.show') }}" @if(request()->routeIs('contact.show')) aria-current="page" @endif>Contact Us</a>
            </nav>
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">Menu</button>
            <div class="header-tools">
                <a class="header-search" href="{{ route('search') }}" aria-label="Search">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.5-3.5" stroke-linecap="round"></path>
                    </svg>
                </a>
                <a class="btn btn--sm" href="{{ route('contact.show') }}">Contact us <span class="btn-arrow" aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="wrap">
            <div class="footer-grid">
                <div>
                    <a class="footer-logo" href="{{ route('home') }}">
                        <img src="{{ asset('media/brand/logo.png') }}" alt="marina.newyorkcity" width="82" height="82">
                    </a>
                    <div class="footer-blurb">
                        <p class="eyebrow eyebrow--label">Contact us</p>
                        <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
                    </div>
                </div>
                <nav aria-label="Footer navigation">
                    <p class="footer-title">Explore</p>
                    <a href="{{ route('posts.index') }}">Blog</a>
                    <a href="{{ route('posts.news') }}">News — What's going on</a>
                    <a href="{{ route('posts.guides') }}">NYC Guides</a>
                    <a href="{{ route('events.index') }}">Events</a>
                    <a href="{{ route('pages.shop') }}">Digital Products</a>
                </nav>
                <nav aria-label="Legal navigation">
                    <p class="footer-title">Useful links</p>
                    <a href="{{ route('pages.about') }}">About</a>
                    <a href="{{ route('pages.privacy') }}">Privacy Policy</a>
                    <a href="{{ route('pages.terms') }}">Terms &amp; Conditions</a>
                    <a href="{{ route('pages.accessibility') }}">Accessibility Statement</a>
                </nav>
                <nav aria-label="Social links">
                    <p class="footer-title">Stay connected</p>
                    <a href="https://www.instagram.com/marina.newyorkcity/" rel="noopener">Instagram</a>
                    <a href="https://www.tiktok.com/@marina.newyorkcity" rel="noopener">TikTok</a>
                    <a href="https://www.threads.com/@marina.newyorkcity" rel="noopener">Threads</a>
                    <a href="https://www.facebook.com/marina.nycity" rel="noopener">Facebook</a>
                </nav>
            </div>
            <div class="footer-bottom">
                <p>© {{ now()->year }} @marina.newyorkcity. All Rights Reserved</p>
                <p>New York City</p>
            </div>
        </div>
    </footer>
</body>
</html>
