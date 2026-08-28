{{--
    The page somebody lands on when a link is wrong.

    The ways to arrive here are ordinary: the legal pages carry links written on
    the old Wix site, a renamed story keeps its old address in somebody's
    bookmarks, and Instagram captions outlive the posts they point at. So it is
    her own page — same header, same navigation, same footer, same page-head as
    every other screen — and it says what happened in one sentence and then
    offers somewhere to go.
--}}
@extends('layouts.app')

@php
    $seoTitle = 'Page Not Found | marina.newyorkcity';
    $seoDescription = 'This address does not lead anywhere. Find stories, events and guides on marina.newyorkcity.';
@endphp

@section('content')
    @include('partials.page-head', [
        'eyebrow' => 'Error 404',
        'meta' => 'Page not found',
        'title' => 'PAGE NOT FOUND',
        'kicker' => 'That address does not lead anywhere any more. It may have moved, or the link that brought you here may have a typo in it.',
    ])

    <section class="section page" style="padding-top: 0">
        <div class="wrap">
            <div class="notfound-actions fade-up">
                <a class="btn" href="{{ route('home') }}">Back to the home page <span class="btn-arrow" aria-hidden="true">&rarr;</span></a>
                <a class="btn btn--ghost" href="{{ route('posts.index') }}">Read the blog</a>
                <a class="btn btn--ghost" href="{{ route('events.index') }}">See what is on</a>
            </div>

            <div class="section-head-top fade-up" style="margin: clamp(56px, 6vw, 90px) 0 clamp(20px, 2vw, 32px)">
                <p class="eyebrow">Or try one of these</p>
                <span class="rule" aria-hidden="true"></span>
            </div>

            <div class="notfound-links fade-up">
                <a href="{{ route('posts.guides') }}">NYC Guides <span aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('posts.news') }}">News <span aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('pages.free-events') }}">Free Events <span aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('pages.travel-usa') }}">Travel USA <span aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('pages.how-i-create') }}">How I Create <span aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('pages.shop') }}">Shop <span aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('contact.show') }}">Contact Us <span aria-hidden="true">&rarr;</span></a>
            </div>

            <form class="search-bar fade-up" action="{{ route('search') }}" method="GET" role="search" style="margin-top: clamp(48px, 5vw, 80px)">
                <label class="visually-hidden" for="q">Search stories and events</label>
                <input id="q" name="q" type="search" placeholder="Search stories and events">
                <button class="btn" type="submit">Search</button>
            </form>
        </div>
    </section>
@endsection
