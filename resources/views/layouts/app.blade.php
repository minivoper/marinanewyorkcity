<!DOCTYPE html>
<html lang="en-US">
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
    <link rel="preload" href="/fonts/arial-w01-black.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/avenir-lt-w01_35-light.woff2" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header {{ ($transparentHeader ?? false) ? '' : 'site-header--solid' }}">
        <div class="wrap">
            <a class="site-logo" href="{{ route('home') }}" aria-label="marina.newyorkcity home">
                <img src="{{ asset('media/brand/logo.png') }}" alt="marina.newyorkcity" width="76" height="76">
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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.5-3.5" stroke-linecap="round"></path>
                    </svg>
                </a>
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
                        <img src="{{ asset('media/brand/logo.png') }}" alt="marina.newyorkcity" width="88" height="88">
                    </a>
                    <p class="text-mid footer-blurb">Contact us<br>
                        <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
                    </p>
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
            <p class="footer-bottom">© {{ now()->year }} @marina.newyorkcity. All Rights Reserved</p>
        </div>
    </footer>
</body>
</html>
