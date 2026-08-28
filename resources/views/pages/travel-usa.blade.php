@extends('layouts.app')

@php
    use Eshlink\Cms\Facades\Cms;

    $seoTitle = Cms::value('travel_usa.seo_title');
    $seoDescription = Cms::value('travel_usa.seo_description');
@endphp

@section('content')
    <section class="hero hero--page">
        <div class="hero-media" data-parallax="0.18">
            <video autoplay muted loop playsinline poster="{{ asset(Cms::value('travel_usa.video_poster')) }}">
                <source src="{{ asset(Cms::value('travel_usa.video_path')) }}" type="video/mp4">
            </video>
        </div>
        <div class="hero-copy">
            <div class="wrap">
                <div class="hero-sweep" aria-hidden="true"></div>
                <div class="mask">
                    <p class="eyebrow hero-eyebrow">Cinematic travel films</p>
                </div>
                <div class="mask" style="margin-top: 10px">
                    <h1 class="display t-h1">@cms('travel_usa.heading')</h1>
                </div>
            </div>
        </div>
    </section>
@endsection
