@extends('layouts.app')

@php
    use Eshlink\Cms\Facades\Cms;

    $seoTitle = Cms::value('home.seo_title');
    $seoDescription = Cms::value('home.seo_description');

    $partnerLogos = Cms::value('home.partner_logos', []);
@endphp

@section('content')
    <section class="hero">
        <div class="hero-media" data-parallax="0.22">
            <img src="{{ asset(Cms::value('home.hero_image')) }}" alt="{{ Cms::value('home.hero_image_alt') }}" fetchpriority="high">
        </div>
        <div class="hero-copy">
            <div class="wrap">
                <div class="hero-sweep" aria-hidden="true"></div>
                <div class="mask">
                    <p class="eyebrow hero-eyebrow">marina.newyorkcity — cinematic new york</p>
                </div>
                <div class="mask" style="margin-top: 12px">
                    <h1 class="display t-hero">@cms('home.hero_heading')</h1>
                </div>
                <div class="hero-foot">
                    <div class="mask">
                        <p class="hero-sub">@cms('home.hero_subheading')</p>
                    </div>
                    <div class="scroll-cue" aria-hidden="true">
                        <span>Scroll</span>
                        <span class="scroll-cue-track"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{--
        Partners, then licensing, then everything else.

        On the old page the licensing block sat fourth and the logo wall last,
        which put the two things a brand buyer came for at the bottom of a very
        long scroll. The strip is proof and the panel is the offer, so they lead.
    --}}
    {{-- The padding is deliberately uneven: the eyebrow row above the strip is
         part of its label, so measuring from there the band sat 43px below the
         rule and 87px above the border. The bottom is trimmed to match the top
         of the band, not the top of the section. --}}
    <section class="section" style="padding-top: clamp(56px, 6vw, 96px); padding-bottom: clamp(28px, 3vw, 44px); border-bottom: 1px solid var(--hairline-faint)" aria-labelledby="clients-partners">
        <div class="wrap section-head-top fade-up" style="margin-bottom: clamp(28px, 3vw, 44px)">
            <p class="eyebrow" id="clients-partners">@cms('home.partners_heading')</p>
            <span class="rule" aria-hidden="true"></span>
            <p class="text-mid" style="font-size: 15px; margin: 0">@cms('home.partners_kicker')</p>
        </div>
        <div class="marquee">
            <div class="marquee-track">
                {{-- Never lazy. These scroll sideways forever, so the browser has
                     no reason to think the far ones are coming into view: 11 of
                     the 44 stayed undecoded and animated past as blank slots,
                     which read as the loop breaking. --}}
                @foreach ($partnerLogos as $logo)
                    <img src="{{ asset('media/about/'.$logo['file']) }}" alt="{{ $logo['alt'] }}" decoding="async">
                @endforeach
                {{-- Second copy: the track translates -50%, so the loop is seamless. --}}
                @foreach ($partnerLogos as $logo)
                    <img src="{{ asset('media/about/'.$logo['file']) }}" alt="" aria-hidden="true" decoding="async">
                @endforeach
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="content-licensing">
        <div class="wrap split">
            <div class="split-media split-media--ruled fade-up">
                <div data-parallax="0.10">
                    <img src="{{ asset(Cms::value('home.licensing_image')) }}" alt="{{ Cms::value('home.licensing_image_alt') }}" loading="lazy">
                </div>
            </div>
            <div class="split-body fade-up" data-delay="120">
                <div class="section-head-top">
                    <p class="eyebrow">01 — Licensing</p>
                    <span class="rule" aria-hidden="true"></span>
                </div>
                <h2 id="content-licensing" class="display display--sentence t-h2">@cms('home.licensing_heading')</h2>
                @cms('home.licensing_body')

                <a class="btn cluster" href="{{ route('contact.show') }}">@cms('home.licensing_cta_label') <span class="btn-arrow" aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
    </section>

    <section class="section section--surface" aria-labelledby="social-media">
        <div class="wrap split split--top">
            <div class="fade-up">
                <div class="section-head-top">
                    <p class="eyebrow">02 — Capabilities</p>
                    <span class="rule" aria-hidden="true"></span>
                </div>
                <h2 id="social-media" class="display t-h2" style="margin-top: 24px">@cms('home.social_heading')</h2>
                <div class="split-media" style="margin-top: 40px">
                    <img src="{{ asset(Cms::value('home.social_image')) }}" alt="{{ Cms::value('home.social_image_alt') }}" loading="lazy">
                </div>
            </div>
            {{--
                The body arrives from the CMS as a run of <p> tags, several of
                which begin with a bullet character. .rule-list turns them into
                hairline-separated rows, so the same markup reads as a spec
                sheet instead of a wall of prose. The copy is untouched.
            --}}
            <div class="split-body rule-list fade-up" data-delay="120">
                @cms('home.social_body')

            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="whats-going-on">
        <div class="wrap">
            <div class="section-head fade-up">
                <div class="section-head-top">
                    <p class="eyebrow">03 — Latest</p>
                    <span class="rule" aria-hidden="true"></span>
                    <a class="link-sweep" href="{{ route('posts.index') }}">All stories &rarr;</a>
                </div>
                <h2 id="whats-going-on" class="display t-h2">@cms('home.news_heading')</h2>
                <p class="kicker">@cms('home.news_kicker')</p>
            </div>
            <div class="card-grid">
                @foreach ($newsPosts as $index => $postItem)
                    <a class="card fade-up" href="{{ route('posts.show', $postItem->slug) }}">
                        @if ($postItem->cover_path)
                            <div class="card-media">
                                <img src="{{ asset($postItem->cover_path) }}" alt="{{ $postItem->title }}" loading="lazy">
                                <span class="card-bar" aria-hidden="true"></span>
                            </div>
                        @endif
                        <div class="card-body">
                            <span class="card-index" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h3 class="card-title">{{ $postItem->title }}</h3>
                                <p class="card-meta">{{ $postItem->published_at->format('M j, Y') }}</p>
                                <p class="card-excerpt">{{ $postItem->excerpt }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--surface" aria-labelledby="nyc-guides">
        <div class="wrap">
            <div class="section-head fade-up">
                <div class="section-head-top">
                    <p class="eyebrow">04 — Guides</p>
                    <span class="rule" aria-hidden="true"></span>
                    <a class="link-sweep" href="{{ route('posts.guides') }}">All guides &rarr;</a>
                </div>
                <h2 id="nyc-guides" class="display t-h2">@cms('home.guides_heading')</h2>
                <p class="kicker">@cms('home.guides_kicker')</p>
            </div>
            <div class="card-grid">
                @foreach ($guidePosts as $index => $postItem)
                    <a class="card fade-up" href="{{ route('posts.show', $postItem->slug) }}">
                        @if ($postItem->cover_path)
                            <div class="card-media">
                                <img src="{{ asset($postItem->cover_path) }}" alt="{{ $postItem->title }}" loading="lazy">
                                <span class="card-bar" aria-hidden="true"></span>
                            </div>
                        @endif
                        <div class="card-body">
                            <span class="card-index" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="card-title">{{ $postItem->title }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="about-me">
        <div class="wrap split">
            {{-- --portrait: this frame holds a person, so the crop is biased to
                 the top. Without it `cover` takes the excess evenly and the hat
                 goes. Keep the modifier if the picture is ever swapped for
                 another portrait; drop it if it becomes a landscape. --}}
            <div class="split-media split-media--portrait fade-up">
                {{-- 0.04, half the drift of the other frames: the hat has only
                     101px of clearance and the travel eats straight into it. --}}
                <div data-parallax="0.04">
                    <img src="{{ asset(Cms::value('home.about_image')) }}" alt="{{ Cms::value('home.about_image_alt') }}" loading="lazy">
                </div>
            </div>
            <div class="split-body fade-up" data-delay="120">
                <div class="section-head-top">
                    <p class="eyebrow">05 — Profile</p>
                    <span class="rule" aria-hidden="true"></span>
                </div>
                <h2 id="about-me" class="display display--sentence t-h2">@cms('home.about_heading')</h2>
                @cms('home.about_body')

            </div>
        </div>
    </section>

    <section class="section section--surface" aria-labelledby="instagram">
        <div class="wrap">
            <div class="section-head-top fade-up" style="margin-bottom: clamp(28px, 3vw, 44px); flex-wrap: wrap">
                <h2 id="instagram" class="display t-h3">@cms('home.instagram_heading')</h2>
                <span class="rule" aria-hidden="true"></span>
                <a class="eyebrow" href="{{ $instagramProfileUrl }}" rel="noopener">@marina.newyorkcity</a>
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
@endsection
