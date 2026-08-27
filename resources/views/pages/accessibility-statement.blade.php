@extends('layouts.app')

@php
    use Eshlink\Cms\Facades\Cms;

    $seoTitle = Cms::value('accessibility_statement.seo_title');
    $seoDescription = Cms::value('accessibility_statement.seo_description');
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">@cms('accessibility_statement.heading')</h1>
            </div>
            <div class="prose fade-up">
                @cms('accessibility_statement.body')

            </div>
        </div>
    </section>
@endsection
