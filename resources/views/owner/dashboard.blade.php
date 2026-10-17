@extends('layouts.app')

@section('title', 'Owner dashboard')

@section('content')
    <div class="dashboard-header">
        <div>
            <span class="eyebrow">OWNER WORKSPACE</span>
            <h1>My salon</h1>
            <p class="muted">
                Manage your listing, opening hours, and customer feedback.
            </p>
        </div>
    </div>

    @if(!$salon)
        <section class="content-panel onboarding-card">
            <div class="empty-icon">✦</div>
            <h2>Let's add your salon.</h2>
            <p class="muted">
                Create your listing to get started. An administrator must approve
                it before it appears in public search results.
            </p>

            <a href="{{ route('owner.salon.create') }}"
               class="button button-primary">
                Create salon listing
            </a>
        </section>
    @else
        <section class="stats-grid">
            <article class="stat-card">
                <span class="stat-label">Listing status</span>
                <strong>
                    <span class="status-badge status-{{ $salon->status }}">
                        {{ ucfirst($salon->status) }}
                    </span>
                </strong>
                <span class="muted small">
                    @switch($salon->status)
                        @case('pending')
                            Waiting for admin approval.
                            @break
                        @case('approved')
                            Your salon is visible to the public.
                            @break
                        @case('rejected')
                            Your listing was rejected.
                            @break
                        @case('suspended')
                            Your listing is not publicly visible.
                            @break
                    @endswitch
                </span>
            </article>

            <article class="stat-card">
                <span class="stat-label">Total reviews</span>
                <strong>{{ $salon->reviews->count() }}</strong>
                <span class="muted small">Including hidden reviews</span>
            </article>

            <article class="stat-card">
                <span class="stat-label">Average rating</span>
                <strong>
                    @if($salon->reviews->where('status', 'visible')->count())
                        {{ number_format($salon->reviews->where('status', 'visible')->avg('rating'), 1) }}
                        <span class="star">★</span>
                    @else
                        —
                    @endif
                </strong>
                <span class="muted small">Based on visible reviews</span>
            </article>
        </section>

        <div class="dashboard-columns">
            <section class="content-panel">
                <div class="panel-heading">
                    <div>
                        <h2>Salon details</h2>
                        <p class="muted">Your public listing information.</p>
                    </div>

                    <a href="{{ route('owner.salon.edit', $salon) }}"
                       class="button button-ghost button-small">
                        Edit details
                    </a>
                </div>

                <h3>{{ $salon->name }}</h3>
                <p class="muted">{{ $salon->address }}, {{ $salon->city }}</p>
                <p class="body-copy">
                    {{ $salon->description ?: 'No description added.' }}
                </p>

                <div class="contact-line">
                    <span class="contact-icon">☎</span>
                    <p>{{ $salon->phone }}</p>
                </div>

                @if($salon->status === 'approved')
                    <a class="text-link" href="{{ route('salons.show', $salon) }}">
                        View public listing →
                    </a>
                @endif

                <div class="danger-zone">
                    <div>
                        <strong>Delete salon</strong>
                        <p class="muted small">
                            Permanently deletes this listing, its reviews, and opening hours.
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('owner.salon.destroy', $salon) }}"
                        onsubmit="return confirm('Permanently delete this salon and all associated reviews and opening hours?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="button button-danger button-small">
                            Delete salon
                        </button>
                    </form>
                </div>
            </section>

            <section class="content-panel">
                <div class="panel-heading">
                    <div>
                        <h2>Opening hours</h2>
                        <p class="muted">Current schedule for your salon.</p>
                    </div>
                </div>

                @include('partials.opening-hours', ['salon' => $salon])

                <a href="{{ route('owner.salon.edit', $salon) }}#opening-hours"
                   class="text-link hours-edit-link">
                    Edit opening hours →
                </a>
            </section>
        </div>

        <section class="content-panel dashboard-reviews">
            <div class="panel-heading">
                <div>
                    <h2>Customer reviews</h2>
                    <p class="muted">
                        Review feedback and check its moderation status.
                    </p>
                </div>
            </div>

            @forelse($salon->reviews as $review)
                <article class="review-item">
                    <div class="review-header">
                        <div class="review-avatar">
                            {{ mb_strtoupper(mb_substr($review->user->name, 0, 1)) }}
                        </div>

                        <div class="review-author">
                            <strong>{{ $review->user->name }}</strong>
                            <span class="muted small">
                                {{ $review->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <span class="review-rating">
                            ★ {{ $review->rating }}/5
                        </span>
                    </div>

                    <p class="review-comment">
                        {{ $review->comment ?: 'No comment provided.' }}
                    </p>

                    <span class="status-badge status-{{ $review->status }}">
                        {{ str_replace('_', ' ', ucfirst($review->status)) }}
                    </span>
                </article>
            @empty
                <div class="empty-state compact">
                    <h3>No reviews yet</h3>
                    <p>Customer feedback will appear here.</p>
                </div>
            @endforelse
        </section>
    @endif
@endsection