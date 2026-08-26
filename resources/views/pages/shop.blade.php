@extends('layouts.app')

@php
    $seoTitle = 'SHOP | marina.newyorkcity';
    $seoDescription = 'Digital products and New York merch by marina.newyorkcity.';
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">SHOP</h1>
            </div>

            <div class="section-head fade-up">
                <h2 class="display t-h3">Digital products</h2>
            </div>
            <div class="card-grid fade-up">
                <a class="card" href="https://links.marinanewyorkcity.com/digitalproducts" rel="noopener">
                    <div class="card-media card-media--portrait">
                        <img src="{{ asset('media/shop/e628c7_fc77fbaabee5473a806e75cbba584320.jpg') }}" alt="Lightroom Presets Collection" loading="lazy">
                    </div>
                    <h3 class="card-title">Lightroom Presets Collection</h3>
                </a>
                <a class="card" href="https://links.marinanewyorkcity.com/digitalproducts" rel="noopener">
                    <div class="card-media card-media--portrait">
                        <img src="{{ asset('media/shop/e628c7_d414b7c345ed42e39c358fe73d43d22b.jpg') }}" alt="Lightroom Presets Collections" loading="lazy">
                    </div>
                    <h3 class="card-title">Lightroom Presets Collections</h3>
                </a>
                <a class="card" href="https://links.marinanewyorkcity.com/digitalproducts" rel="noopener">
                    <div class="card-media card-media--portrait">
                        <img src="{{ asset('media/shop/e628c7_6775031bad1d46f7b776d8cfff12760b.jpg') }}" alt="Free Screen Savers — sunset by marinanewyorkcity" loading="lazy">
                    </div>
                    <h3 class="card-title">Free Screen Savers</h3>
                </a>
            </div>
            <p class="fade-up cluster">
                <a class="btn" href="https://links.marinanewyorkcity.com/digitalproducts" rel="noopener">SHOP HERE</a>
            </p>

            <div class="section-head fade-up section-follow">
                <h2 class="display t-h3">New York Merch</h2>
                <p class="kicker">Keep or wear a piece of New York</p>
            </div>
            <div class="card-grid fade-up">
                <div class="card">
                    <div class="card-media card-media--square">
                        <img src="{{ asset('media/shop/e628c7_9012ec656b1a4826b97e8ccaa8632aba.jpg') }}" alt="New York merch cup" loading="lazy">
                    </div>
                </div>
                <div class="card">
                    <div class="card-media card-media--square">
                        <img src="{{ asset('media/shop/e628c7_8e5615aa3f2248c3ba809f16e50dd928.jpeg') }}" alt="New York merch ornament" loading="lazy">
                    </div>
                </div>
                <div class="card">
                    <div class="card-media card-media--square">
                        <img src="{{ asset('media/shop/e628c7_470106c04e654b97b37fbf88d643dcc9.jpg') }}" alt="New York merch sticker" loading="lazy">
                    </div>
                </div>
                <div class="card">
                    <div class="card-media card-media--square">
                        <img src="{{ asset('media/shop/e628c7_1bb8f279d4a640828c7f7452b4254701.jpg') }}" alt="New York merch hoodie" loading="lazy">
                    </div>
                </div>
            </div>
            <p class="fade-up cluster">
                <a class="btn btn--ghost" href="{{ route('pages.merch') }}">Explore</a>
            </p>

            <div class="section-head fade-up section-follow">
                <h2 class="display t-h3">Affiliate links</h2>
                <p class="kicker">coming soon</p>
            </div>
        </div>
    </section>
@endsection
