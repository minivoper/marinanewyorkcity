@extends('layouts.app')

@php
    use Eshlink\Cms\Facades\Cms;

    $seoTitle = Cms::value('shop.seo_title');
    $seoDescription = Cms::value('shop.seo_description');
@endphp

@section('content')
    @include('partials.page-head', [
        'eyebrow' => 'Store',
        'meta' => 'Digital · Merch',
        'title' => Cms::value('shop.heading'),
        'kicker' => Cms::value('shop.seo_description'),
    ])

    <section class="section page" style="padding-top: 0">
        <div class="wrap">
            <div class="section-head-top fade-up" style="margin-bottom: clamp(28px, 3vw, 44px)">
                <p class="eyebrow">01 — @cms('shop.digital_heading')</p>
                <span class="rule" aria-hidden="true"></span>
            </div>
            <div class="card-grid">
@foreach (Cms::value('shop.digital_products', []) as $index => $digitalProduct)
                <a class="card fade-up" href="{{ $digitalProduct['url'] }}" rel="noopener">
                    <div class="card-media card-media--portrait">
                        <img src="{{ asset($digitalProduct['image']) }}" alt="{{ $digitalProduct['alt'] }}" loading="lazy">
                        <span class="card-bar" aria-hidden="true"></span>
                    </div>
                    <div class="card-body">
                        <span class="card-index" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="card-title">{{ $digitalProduct['title'] }}</h3>
                    </div>
                </a>
@endforeach
            </div>
            <p class="fade-up cluster">
                <a class="btn" href="{{ Cms::value('shop.digital_cta_url') }}" rel="noopener">@cms('shop.digital_cta_label') <span class="btn-arrow" aria-hidden="true">&rarr;</span></a>
            </p>

            <div class="section-head-top section-follow fade-up" style="margin-bottom: clamp(28px, 3vw, 44px)">
                <p class="eyebrow">02 — @cms('shop.merch_heading')</p>
                <span class="rule" aria-hidden="true"></span>
                <p class="text-mid" style="font-size: 15px; margin: 0">@cms('shop.merch_kicker')</p>
            </div>
            <div class="card-grid card-grid--tight">
@foreach (Cms::value('shop.merch_images', []) as $merchImage)
                <div class="card fade-up">
                    <div class="card-media card-media--square">
                        <img src="{{ asset($merchImage['image']) }}" alt="{{ $merchImage['alt'] }}" loading="lazy">
                    </div>
                </div>
@endforeach
            </div>
            <p class="fade-up cluster">
                <a class="btn btn--ghost" href="{{ route('pages.merch') }}">@cms('shop.merch_cta_label') <span class="btn-arrow" aria-hidden="true">&rarr;</span></a>
            </p>

            <div class="section-head-top section-follow fade-up">
                <p class="eyebrow">03 — @cms('shop.affiliate_heading')</p>
                <span class="rule" aria-hidden="true"></span>
                <p class="eyebrow eyebrow--muted">@cms('shop.affiliate_kicker')</p>
            </div>
        </div>
    </section>
@endsection
