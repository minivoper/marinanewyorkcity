@extends('layouts.app')

@php
    $seoTitle = 'CONTACT US | marina.newyorkcity';
    $seoDescription = 'Contact marina.newyorkcity for partnerships, press, and event invitations.';
@endphp

@section('content')
    <section class="section page">
        <div class="wrap">
            <div class="section-head fade-up">
                <h1 class="display t-h1">CONTACT US</h1>
            </div>

            @if (session('status'))
                <p class="flash-success" role="status">{{ session('status') }}</p>
            @endif

            <form class="form-grid fade-up" method="POST" action="{{ route('contact.store') }}" enctype="multipart/form-data">
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
                    <button class="btn" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </section>
@endsection
