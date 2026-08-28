@extends('layouts.app')

@php
    $seoTitle = 'CONTACT US | marina.newyorkcity';
    $seoDescription = 'Contact marina.newyorkcity for partnerships, press, and event invitations.';
@endphp

@section('content')
    @include('partials.page-head', [
        'eyebrow' => 'Get in touch',
        'meta' => 'Partnerships · Press · Events',
        'title' => 'CONTACT US',
        'kicker' => 'Contact marina.newyorkcity for partnerships, press, and event invitations.',
    ])

    <section class="section page" style="padding-top: 0">
        <div class="wrap">
            @if (session('status'))
                <p class="flash-success" role="status">{{ session('status') }}</p>
            @endif

            <div class="contact-layout">
                {{-- A direct address for anyone who would rather not use a form.
                     Licensing enquiries mostly arrive by email. --}}
                <div class="fade-up">
                    <p class="eyebrow eyebrow--label">Email</p>
                    <a class="contact-email" href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
                    <p class="eyebrow eyebrow--label">Social</p>
                    <div class="stack-links">
                        <a href="https://www.instagram.com/marina.newyorkcity/" rel="noopener">Instagram</a>
                        <a href="https://www.tiktok.com/@marina.newyorkcity" rel="noopener">TikTok</a>
                        <a href="https://www.threads.com/@marina.newyorkcity" rel="noopener">Threads</a>
                        <a href="https://www.facebook.com/marina.nycity" rel="noopener">Facebook</a>
                    </div>
                </div>

                <form class="form-grid fade-up" data-delay="120" method="POST" action="{{ route('contact.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-field form-field--full">
                        <label for="contacting_for">Contacting for</label>
                        <select id="contacting_for" name="contacting_for" required>
                            <option value="" @selected(old('contacting_for') === null)>Contacting for</option>
                            <option value="SM Partnership Inquiry" @selected(old('contacting_for') === 'SM Partnership Inquiry')>SM Partnership Inquiry</option>
                            <option value="Website Press Release" @selected(old('contacting_for') === 'Website Press Release')>Website Press Release</option>
                            <option value="Event Invitation" @selected(old('contacting_for') === 'Event Invitation')>Event Invitation</option>
                            <option value="Other" @selected(old('contacting_for') === 'Other')>Other</option>
                        </select>
                        @error('contacting_for')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label for="name">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="First / Last name" required>
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field">
                        <label for="company">Company name</label>
                        <input id="company" name="company" type="text" value="{{ old('company') }}">
                        @error('company')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field form-field--full">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Email" required>
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field form-field--full">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Message" required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field form-field--full">
                        <label for="file">File upload — e.g., upload any additional info</label>
                        <input id="file" name="file" type="file">
                        @error('file')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-field form-field--full">
                        <button class="btn" type="submit">Submit <span class="btn-arrow" aria-hidden="true">&rarr;</span></button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
