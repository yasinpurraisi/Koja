
@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="auth-page">
        <section class="content-panel auth-card">
            <a href="{{ route('salons.index') }}" class="back-link">
                ← Back to salons
            </a>

            <div class="auth-heading">
                <span class="eyebrow">WELCOME BACK</span>
                <h1>Log in to Koja</h1>
                <p class="muted">
                    Discover salons and manage your account.
                </p>
            </div>

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="stack-form">
                @csrf

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
                        autofocus
                    >
                </div>

                <div class="form-field">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <label class="checkbox-label">
                    <input type="checkbox" name="remember" value="1">
                    Remember me
                </label>

                <button type="submit" class="button button-primary button-full">
                    Log in
                </button>
            </form>

            <p class="auth-switch muted">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-link">
                    Create one
                </a>
            </p>
        </section>
    </div>
@endsection
