
@extends('layouts.app')

@section('title', 'Create account')

@section('content')
    <div class="auth-page">
        <section class="content-panel auth-card">
            <a href="{{ route('salons.index') }}" class="back-link">
                ← Back to salons
            </a>

            <div class="auth-heading">
                <span class="eyebrow">JOIN KOJA</span>
                <h1>Create your account</h1>
                <p class="muted">
                    Start discovering and reviewing salons.
                </p>
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="stack-form">
                @csrf

                <div class="form-field">
                    <label for="name">Full name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        maxlength="255"
                        required
                        autofocus
                    >
                </div>

                <div class="form-field">
                    <label for="phone">Phone number</label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone') }}"
                        autocomplete="tel"
                        maxlength="30"
                        required
                    >
                    <span class="muted small">
                        Use the same format when logging in.
                    </span>
                </div>

                <div class="form-field">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                    <span class="muted small">
                        Use at least 8 characters.
                    </span>
                </div>

                <div class="form-field">
                    <label for="password_confirmation">Confirm password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                </div>

                <button type="submit" class="button button-primary button-full">
                    Create account
                </button>
            </form>

            <p class="auth-switch muted">
                Already registered?
                <a href="{{ route('login') }}" class="text-link">
                    Log in
                </a>
            </p>
        </section>
    </div>
@endsection
