@extends('layouts.app')

@section('title', 'Recommended salons')

@section('content')
    <div class="container page-section">
        <div class="page-heading recommendation-page-heading">
            <div>
                <span class="eyebrow">YOUR PERSONALIZED LIST</span>
                <h1>Recommended salons</h1>
                <p>
                    Salons are ranked by a blend of distance and customer
                    ratings, with the strongest match first.
                </p>
            </div>

            <a href="{{ route('salons.index') }}#recommendations" class="button button-ghost">
                Change location
            </a>
        </div>

        @if($salons->isNotEmpty())
            <div class="recommendation-list">
                @foreach($salons as $salon)
                    <article class="salon-card recommendation-card">
                        <div class="salon-card-top">
                            <span class="recommendation-rank" aria-label="Rank {{ $loop->iteration }}">
                                {{ $loop->iteration }}
                            </span>

                            <div class="salon-card-heading">
                                <h2>
                                    <a href="{{ route('salons.show', $salon) }}">
                                        {{ $salon->name }}
                                    </a>
                                </h2>
                                <p class="muted">{{ $salon->city }}</p>
                            </div>

                            <div class="recommendation-score">
                                <span>Match score</span>
                                <strong>{{ number_format($salon->recommendation_score * 100, 1) }}<small>/100</small></strong>
                            </div>
                        </div>

                        <p class="salon-card-description">
                            {{ $salon->description ?: 'Discover this salon and learn more about its services.' }}
                        </p>

                        <div class="salon-card-meta recommendation-meta">
                            <span>
                                <span class="star" aria-hidden="true">★</span>
                                @if($salon->average_rating !== null)
                                    {{ number_format($salon->average_rating, 1) }}
                                    <span class="muted">
                                        ({{ $salon->review_count }}
                                        {{ \Illuminate\Support\Str::plural('review', $salon->review_count) }})
                                    </span>
                                @else
                                    <span class="muted">No reviews yet</span>
                                @endif
                            </span>
                            <span class="recommendation-distance">
                                {{ number_format($salon->distance_km, 1) }} km away
                            </span>
                        </div>

                        <p class="salon-address">
                            <span aria-hidden="true">⌖</span>
                            {{ $salon->address }}
                        </p>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon" aria-hidden="true">⌖</div>
                <h2>No nearby salons to recommend yet</h2>
                <p>
                    There are no approved salons with location details to rank
                    for these coordinates.
                </p>
                <a href="{{ route('salons.index') }}" class="button button-primary">
                    Explore all salons
                </a>
            </div>
        @endif
    </div>
@endsection