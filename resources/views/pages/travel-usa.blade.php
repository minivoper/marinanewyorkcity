@extends('layouts.app')

@php
    $seoTitle = 'TRAVEL USA | marina.newyorkcity';
    $seoDescription = 'Cinematic travel stories from across the USA by Marina Kapler.';
    $transparentHeader = true;
@endphp

@section('content')
    <section class="hero hero--page">
        <div class="hero-media">
            <video autoplay muted loop playsinline poster="{{ asset('media/travel/travel-usa-poster.jpg') }}">
                <source src="{{ asset('media/travel/travel-usa-1080p.mp4') }}" type="video/mp4">
            </video>
        </div>
        <div class="hero-copy">
            <h1 class="display t-h1">TRAVEL USA</h1>
        </div>
    </section>
@endsection
