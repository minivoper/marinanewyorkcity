@extends('layouts.app')

@php
    $seoTitle = 'Merch and Prints | marina.newyorkcity';
    $seoDescription = 'New York prints and merch by marina.newyorkcity.';
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">Merch and Prints</h1>
            </div>

            <div class="section-head fade-up">
                <h2 class="display t-h3">Prints</h2>
            </div>
            <p class="fade-up">
                <a class="btn" href="https://pixels.com/profiles/marina-newyorkcity/shop" rel="noopener">SHOP HERE</a>
            </p>

            <div class="section-head fade-up section-follow">
                <h2 class="display t-h3">Merch</h2>
            </div>
            <div class="card-grid fade-up">
                <div class="card">
                    <div class="card-media card-media--square">
                        <img src="{{ asset('media/shop/e628c7_12ac5abb6e1c4f14a935eac26e4e6719.jpeg') }}" alt="Scented Soy Candle" loading="lazy">
                    </div>
                    <h3 class="card-title">Scented Soy Candle</h3>
                </div>
                <div class="card">
                    <div class="card-media card-media--square">
                        <img src="{{ asset('media/shop/e628c7_8e5615aa3f2248c3ba809f16e50dd928.jpeg') }}" alt="Festive Christmas Ball Ornament" loading="lazy">
                    </div>
                    <h3 class="card-title">Festive Christmas Ball Ornament</h3>
                </div>
                <div class="card">
                    <div class="card-media card-media--square">
                        <img src="{{ asset('media/shop/e628c7_96a036a3e2b94b34bd139c78a1dceb3f.jpeg') }}" alt="Glass Ornaments" loading="lazy">
                    </div>
                    <h3 class="card-title">Glass Ornaments</h3>
                </div>
            </div>
            <p class="fade-up cluster">
                <a class="btn" href="https://marinanewyorkcity.printify.me/" rel="noopener">SHOP HERE</a>
            </p>
        </div>
    </section>
@endsection
