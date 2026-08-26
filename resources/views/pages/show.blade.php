@extends('layouts.app')

@php
    $seoTitle = $page->meta_title;
    $seoDescription = $page->meta_description;
@endphp

@section('content')
    <article class="section page">
        <div class="wrap">
            <header class="section-head fade-up">
                <h1 class="display t-h1">{{ $page->title }}</h1>
            </header>
            <div class="prose fade-up">{!! $page->body !!}</div>
        </div>
    </article>
@endsection
