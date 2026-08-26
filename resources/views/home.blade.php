@extends('layouts.app')

@php
    $seoTitle = 'HOME | marina.newyorkcity';
    $seoDescription = 'New York City news, events, guides, and cinematic stories by Marina Kapler.';
    $transparentHeader = true;

    $partnerLogos = [
        'e628c7_fcebfbb5c8bb4397b080bdca7bdffb4b.png',
        'e628c7_7ea067f9a024426082d23fcd2751bd28.png',
        'e628c7_bc2d366f915f4c699d5fa6c8890fa948.png',
        'e628c7_69394db87db0469fa261462937994f6e.png',
        'e628c7_7f495f107f134fe386b3c93c687418dd.png',
        'e628c7_3c12e21a92474ac99615f8147d8c6110.png',
        'e628c7_e040479eacfc44eea5268f403ed55e21.png',
        'e628c7_42855d22127a4f05843c845d4670a8e3.png',
        'e628c7_cdbe2df1db6a48a19d220b858619b0f4.png',
        'e628c7_a3e5d7952b254201bec5d85d042fe33e.png',
        'e628c7_23a09831f672420ea3e40591a79c296b.png',
        'e628c7_d12527494e37415ea3b9b9e6def457be.png',
        'e628c7_8582203f5809464585d86248be7d124b.png',
        'e628c7_0850b80059f943108b866232ed233ebb.png',
        'e628c7_d2cbda09a54a40f888f8e3231b6786a8.png',
        'e628c7_14039dffd1554404821871ff7df7d666.png',
        'e628c7_619c8ccad2b44ca097aa20fb5cdfed65.png',
        'e628c7_07cf95bd81454c6eae6280f5f0588d65.png',
        'e628c7_4ad5a2225b6a4fee8412ef8a83b9264a.png',
        'e628c7_b2d915048f0e46539e25259ec300b0fa.png',
        'e628c7_6cb1dc2bcca340e787bc849cc4fcae9b.png',
        'e628c7_962c7ada051348e88e16c9073303460d.png',
    ];
@endphp

@section('content')
    <section class="hero">
        <div class="hero-media">
            <img src="{{ asset('media/posts/e628c7_3a6853e78b2d4eb9a542319ab7c27352.jpg') }}" alt="New York City skyline" fetchpriority="high">
        </div>
        <div class="hero-copy">
            <h1 class="display t-hero">new york</h1>
            <p class="hero-sub">where magic begins</p>
        </div>
    </section>

    <section class="section" aria-labelledby="whats-going-on">
        <div class="wrap">
            <div class="section-head fade-up">
                <h2 id="whats-going-on" class="display t-h2">WHAT'S GOING ON</h2>
                <p class="kicker">New York News and events</p>
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
                <h2 id="nyc-guides" class="display t-h2">NYC GUIDES</h2>
                <p class="kicker">what to do | where to stay | what to eat</p>
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
                <img src="{{ asset('media/about/e628c7_a0ff2346577549efb596b2953cf5db24.jpg') }}" alt="Cinematic New York City content" loading="lazy">
            </div>
            <div class="split-body fade-up">
                <h2 id="social-media" class="display t-h3">SOCIAL MEDIA AND MORE</h2>
                <p>• Cinematic Storytelling: Crafting short-form video content (reels, shorts) where products and experiences are seamlessly integrated into authentic NYC narratives, avoiding overt advertising.</p>
                <p>• Luxury &amp; Lifestyle: Showcasing high-end experiences, products, and destinations with an elevated aesthetic.</p>
                <p>• Tech Integration: Demonstrating cutting-edge technology within real-world, aspirational contexts.</p>
                <p>• NYC Focus: Deep expertise in New York City visuals, events, and cultural experiences, offering unparalleled local insight.</p>
                <p>Extended Production Capabilities (Available Upon Request):</p>
                <p>In addition to core deliverables, Marina New York City offers access to a curated network of industry professionals to support higher-scale productions and specialized campaign needs.</p>
                <p>• Professional Photography &amp; Videography Team: Access to experienced photographers and videographers using high-end equipment (Sony, Canon systems) for commercial-grade production.</p>
                <p>• Licensed Drone Operations: FAA-compliant drone operators for cinematic aerial footage and dynamic city perspectives.</p>
                <p>• Original Music &amp; Sound Design: Custom compositions by professional musicians to create signature audio identities for campaigns and branded content.</p>
                <p>These services are available as add-ons and can be integrated into custom production packages based on campaign scope.</p>
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="content-licensing">
        <div class="wrap split split--flip">
            <div class="split-media fade-up">
                <img src="{{ asset('media/about/e628c7_9a81cf94d27847e48a75b7ef7bdef6ff.jpg') }}" alt="New York City sunset" loading="lazy">
            </div>
            <div class="split-body fade-up">
                <h2 id="content-licensing" class="display display--sentence t-h3">content licensing</h2>
                <p>Licensing cinematic New York City photo and video content for editorial, commercial, tourism, hospitality, media, and digital campaigns. Custom footage, event coverage, and archive content available for licensed use across social, web, advertising, and broadcast platforms.</p>
                <a class="btn btn--ghost cluster" href="{{ route('contact.show') }}">Contact us</a>
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="about-me">
        <div class="wrap split">
            <div class="split-media fade-up">
                <img src="{{ asset('media/about/e628c7_ac7a17d721e543e280e1c0932ce97771.png') }}" alt="Marina Kapler" loading="lazy">
            </div>
            <div class="split-body fade-up">
                <h2 id="about-me" class="display display--sentence t-h3">about me</h2>
                <p>Marina Kapler is the creator behind @marina.newyorkcity. A NYC-based content creator, marketing professional, and visual storyteller with a background in media, psychology, and design.</p>
                <p>Specializing in cinematic iPhone videography, I consistently produce short-form content that goes viral, with individual Reels reaching over 800K views and more than 5M monthly views across my platforms.</p>
                <p>My content doesn't look like advertising. It looks like New York.</p>
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="instagram">
        <div class="wrap">
            <div class="section-head fade-up">
                <h2 id="instagram" class="display t-h2">Follow us on Instagram</h2>
            </div>
            <div class="ig-grid fade-up">
                @foreach ($instagramItems as $item)
                    <a class="card" href="https://www.instagram.com/marina.newyorkcity/" rel="noopener" aria-label="Instagram post {{ $item['index'] }}">
                        <div class="card-media card-media--square">
                            <img src="{{ asset(str_replace('public/', '', $item['local_path'])) }}" alt="{{ \Illuminate\Support\Str::limit(strtok($item['caption'], "\n"), 120) }}" loading="lazy">
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="clients-partners">
        <div class="wrap">
            <div class="section-head fade-up">
                <h2 id="clients-partners" class="display t-h2">OUR CLIENTS AND PARTNERS</h2>
                <p class="kicker">We believe every client is a valuable long-term partner.</p>
            </div>
            <div class="logo-grid fade-up">
                @foreach ($partnerLogos as $logo)
                    <img src="{{ asset('media/about/'.$logo) }}" alt="Client and partner logo" loading="lazy">
                @endforeach
            </div>
        </div>
    </section>
@endsection
