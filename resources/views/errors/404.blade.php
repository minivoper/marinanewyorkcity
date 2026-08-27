{{--
    The page somebody lands on when a link is wrong.

    Laravel's own not-found page is a white sheet with the words "Not Found" on
    it: no header, no navigation, nothing that says whose site this is and no
    way onward except the back button. That is a page to leave from.

    It matters more here than on most sites, because the ways to arrive at it
    are ordinary. The legal pages carry links written on the old Wix site, a
    story that has been renamed keeps its old address in somebody's bookmarks,
    and Instagram captions outlive the posts they point at. So this is her own
    page — the same header, the same navigation, the same footer — and it says
    what happened in one sentence and then offers somewhere to go.

    The admin host has a separate one of these, in the CMS package, registered
    from bootstrap/app.php. This is the public half.
--}}
@extends('layouts.app')

@php
    $seoTitle = 'Page Not Found | marina.newyorkcity';
    $seoDescription = 'This address does not lead anywhere. Find stories, events and guides on marina.newyorkcity.';
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">PAGE NOT FOUND</h1>
                <p class="kicker">
                    That address does not lead anywhere any more. It may have moved,
                    or the link that brought you here may have a typo in it.
                </p>
            </div>

            <div class="cluster notfound-actions fade-up">
                <a class="btn" href="{{ route('home') }}">Back to the home page</a>
                <a class="btn btn--ghost" href="{{ route('posts.index') }}">Read the blog</a>
                <a class="btn btn--ghost" href="{{ route('events.index') }}">See what is on</a>
            </div>

            <h2 class="display t-h4 stack-title">Or try one of these</h2>

            <div class="cluster notfound-links fade-up">
                <a href="{{ route('posts.guides') }}">NYC Guides</a>
                <a href="{{ route('posts.news') }}">News</a>
                <a href="{{ route('pages.free-events') }}">Free Events</a>
                <a href="{{ route('pages.travel-usa') }}">Travel USA</a>
                <a href="{{ route('pages.how-i-create') }}">How I Create</a>
                <a href="{{ route('pages.shop') }}">Shop</a>
                <a href="{{ route('contact.show') }}">Contact Us</a>
            </div>

            <form class="search-bar fade-up" action="{{ route('search') }}" method="GET" role="search">
                <label class="visually-hidden" for="q">Search stories and events</label>
                <input id="q" name="q" type="search" placeholder="Search stories and events">
                <button class="btn" type="submit">Search</button>
            </form>
        </div>
    </section>
@endsection
