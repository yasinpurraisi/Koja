@extends('layouts.app')

@section('title', 'Explore salons')

@section('content')
    <section class="hero">
        <span class="eyebrow">YOUR NEXT GOOD HAIR DAY</span>

        <h1>Find a salon you'll <span class="text-accent">love.</span></h1>

        <p class="hero-description">
            Discover local salons, explore customer reviews, and find the right
            place for your next appointment.
        </p>

        <form method="GET" action="{{ route('salons.index') }}" class="search-panel">
            <div class="form-field search-field">
                <label for="search">Salon or location</label>
                <input
                    id="search"
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Salon name, address..."
                >
            </div>

            <div class="form-field city-field">
                <label for="city">City</label>
                <input
                    id="city"
                    type="text"
                    name="city"
                    value="{{ request('city') }}"
                    placeholder="Enter a city"
                >
            </div>

            <button class="button button-primary search-button" type="submit">
                Search salons
            </button>
        </form>
    </section>

    <section class="section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">DISCOVER</span>
                <h2>Explore salons</h2>
                <p class="muted">
                    @if(request()->filled('search') || request()->filled('city'))
                        Results matching your search.
                    @else
                        Find your next favourite place.
                    @endif
                </p>
            </div>

            @if(request()->filled('search') || request()->filled('city'))
                <a href="{{ route('salons.index') }}" class="button button-ghost">
                    Clear filters
                </a>
            @endif
        </div>

        @if($salons->count())
            <div class="salon-grid">
                @foreach($salons as $salon)
                    @include('partials.salon-card', ['salon' => $salon])
                @endforeach
            </div>

            <div class="pagination-wrap">
                {{ $salons->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">⌕</div>
                <h3>No salons found</h3>
                <p>Try a different salon name or city.</p>
                <a href="{{ route('salons.index') }}" class="button button-primary">
                    Explore all salons
                </a>
            </div>
        @endif
    </section>

    <section class="owner-cta">
        <div>
            <span class="eyebrow">SALON OWNERS</span>
            <h2>Put your salon on the map.</h2>
            <p>
                Create a listing, share your details, and help new customers
                discover your business.
            </p>
        </div>

        @auth
            @if(auth()->user()->role === 'owner')
                <a href="{{ route('owner.dashboard') }}" class="button button-light">
                    Manage my salon
                </a>
            @else
                <p class="muted">
                    Salon-owner accounts can be managed through the platform.
                </p>
            @endif
        @else
            <a href="{{ route('login') }}" class="button button-light">
                Get started
            </a>
        @endauth
    </section>
@endsection