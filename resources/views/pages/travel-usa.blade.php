@extends('layouts.app')

@php
    $seoTitle = \Eshlink\Cms\Facades\Cms::value('travel_usa.seo_title');
    $seoDescription = \Eshlink\Cms\Facades\Cms::value('travel_usa.seo_description');
    $transparentHeader = true;
@endphp

@section('content')
    <section class="hero hero--page">
        <div class="hero-media">
            <video autoplay muted loop playsinline poster="{{ asset(\Eshlink\Cms\Facades\Cms::value('travel_usa.video_poster')) }}">
                <source src="{{ asset(\Eshlink\Cms\Facades\Cms::value('travel_usa.video_path')) }}" type="video/mp4">
            </video>
        </div>
        <div class="hero-copy">
            <h1 class="display t-h1">@cms('travel_usa.heading')</h1>
        </div>
    </section>
@endsection
