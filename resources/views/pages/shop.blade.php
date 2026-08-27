@extends('layouts.app')

@php
    use Eshlink\Cms\Facades\Cms;

    $seoTitle = Cms::value('shop.seo_title');
    $seoDescription = Cms::value('shop.seo_description');
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">@cms('shop.heading')</h1>
            </div>

            <div class="section-head fade-up">
                <h2 class="display t-h3">@cms('shop.digital_heading')</h2>
            </div>
            <div class="card-grid fade-up">
{{-- Loop directives sit at column 0 so the rendered indentation matches the cards they replaced. --}}@foreach (Cms::value('shop.digital_products', []) as $digitalProduct)
                <a class="card" href="{{ $digitalProduct['url'] }}" rel="noopener">
                    <div class="card-media card-media--portrait">
                        <img src="{{ asset($digitalProduct['image']) }}" alt="{{ $digitalProduct['alt'] }}" loading="lazy">
                    </div>
                    <h3 class="card-title">{{ $digitalProduct['title'] }}</h3>
                </a>
@endforeach
            </div>
            <p class="fade-up cluster">
                <a class="btn" href="{{ Cms::value('shop.digital_cta_url') }}" rel="noopener">@cms('shop.digital_cta_label')</a>
            </p>

            <div class="section-head fade-up section-follow">
                <h2 class="display t-h3">@cms('shop.merch_heading')</h2>
                <p class="kicker">@cms('shop.merch_kicker')</p>
            </div>
            <div class="card-grid fade-up">
@foreach (Cms::value('shop.merch_images', []) as $merchImage)
                <div class="card">
                    <div class="card-media card-media--square">
                        <img src="{{ asset($merchImage['image']) }}" alt="{{ $merchImage['alt'] }}" loading="lazy">
                    </div>
                </div>
@endforeach
            </div>
            <p class="fade-up cluster">
                <a class="btn btn--ghost" href="{{ route('pages.merch') }}">@cms('shop.merch_cta_label')</a>
            </p>

            <div class="section-head fade-up section-follow">
                <h2 class="display t-h3">@cms('shop.affiliate_heading')</h2>
                <p class="kicker">@cms('shop.affiliate_kicker')</p>
            </div>
        </div>
    </section>
@endsection
