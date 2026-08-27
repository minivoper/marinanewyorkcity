@extends('layouts.app')

@php
    use Eshlink\Cms\Facades\Cms;

    $seoTitle = Cms::value('merch.seo_title');
    $seoDescription = Cms::value('merch.seo_description');
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">@cms('merch.heading')</h1>
            </div>

            <div class="section-head fade-up">
                <h2 class="display t-h3">@cms('merch.prints_heading')</h2>
            </div>
            <p class="fade-up">
                <a class="btn" href="{{ Cms::value('merch.prints_cta_url') }}" rel="noopener">@cms('merch.prints_cta_label')</a>
            </p>

            <div class="section-head fade-up section-follow">
                <h2 class="display t-h3">@cms('merch.merch_heading')</h2>
            </div>
            <div class="card-grid fade-up">
{{-- Loop directives sit at column 0 so the rendered indentation matches the cards they replaced. --}}@foreach (Cms::value('merch.merch_products', []) as $merchProduct)
                <div class="card">
                    <div class="card-media card-media--square">
                        <img src="{{ asset($merchProduct['image']) }}" alt="{{ $merchProduct['alt'] }}" loading="lazy">
                    </div>
                    <h3 class="card-title">{{ $merchProduct['title'] }}</h3>
                </div>
@endforeach
            </div>
            <p class="fade-up cluster">
                <a class="btn" href="{{ Cms::value('merch.merch_cta_url') }}" rel="noopener">@cms('merch.merch_cta_label')</a>
            </p>
        </div>
    </section>
@endsection
