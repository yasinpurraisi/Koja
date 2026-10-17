@extends('layouts.app')

@section('title', $salon->name)

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('salons.index') }}">Explore salons</a>
        <span>/</span>
        <span>{{ $salon->name }}</span>
    </div>

    <section class="salon-detail-hero">
        <div class="salon-detail-avatar">
            {{ mb_strtoupper(mb_substr($salon->name, 0, 1)) }}
        </div>

        <div class="salon-detail-info">
            <span class="eyebrow">{{ $salon->city }}</span>

            <h1>{{ $salon->name }}</h1>

            <p class="salon-detail-address">
                {{ $salon->address }}
            </p>

            <div class="detail-rating">
                <span class="star">★</span>

                @if($salon->reviews->count())
                    <strong>{{ number_format($salon->reviews->avg('rating'), 1) }}</strong>
                    <span class="muted">
                        from {{ $salon->reviews->count() }}
                        {{ \Illuminate\Support\Str::plural('review', $salon->reviews->count()) }}
                    </span>
                @else
                    <span class="muted">No reviews yet</span>
                @endif
            </div>
        </div>

        @auth
            @if(auth()->user()->role === 'owner' && auth()->user()->id === $salon->owner_id)
                <a href="{{ route('owner.salon.edit', $salon) }}"
                   class="button button-primary">
                    Edit salon
                </a>
            @endif
        @endauth
    </section>

    <div class="detail-grid">
        <div class="detail-main">
            <section class="content-panel">
                <div class="panel-heading">
                    <h2>About this salon</h2>
                </div>

                <p class="body-copy">
                    {{ $salon->description ?: 'This salon has not added a description yet.' }}
                </p>

                <div class="contact-line">
                    <span class="contact-icon">☎</span>
                    <div>
                        <span class="muted small">Phone</span>
                        <p>{{ $salon->phone }}</p>
                    </div>
                </div>

                <div class="contact-line">
                    <span class="contact-icon">⌖</span>
                    <div>
                        <span class="muted small">Address</span>
                        <p>{{ $salon->address }}, {{ $salon->city }}</p>
                    </div>
                </div>

                @if($salon->latitude !== null && $salon->longitude !== null)
                    <a
                        class="text-link"
                        href="https://www.openstreetmap.org/?mlat={{ $salon->latitude }}&mlon={{ $salon->longitude }}#map=16/{{ $salon->latitude }}/{{ $salon->longitude }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        View on map ↗
                    </a>
                @endif
            </section>

            <section class="content-panel" id="reviews">
                <div class="panel-heading">
                    <div>
                        <h2>Customer reviews</h2>
                        <p class="muted">Real feedback from customers.</p>
                    </div>
                </div>

                @auth
                    @if(auth()->user()->status === 'active'
                        && auth()->user()->role !== 'admin'
                        && auth()->user()->id !== $salon->owner_id)
                        @php
                            $myReview = auth()->user()
                                ->reviews()
                                ->where('salon_id', $salon->id)
                                ->first();
                        @endphp

                        @if($myReview)
                            <div class="review-form-box">
                                <h3>Your review</h3>

                                <form
                                    method="POST"
                                    action="{{ route('reviews.update', $myReview) }}"
                                    class="stack-form"
                                >
                                    @csrf
                                    @method('PUT')

                                    <div class="form-field">
                                        <label for="my-rating">Your rating</label>
                                        <select id="my-rating" name="rating" required>
                                            @foreach(range(5, 1) as $rating)
                                                <option
                                                    value="{{ $rating }}"
                                                    @selected(old('rating', $myReview->rating) == $rating)
                                                >
                                                    {{ $rating }} / 5
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-field">
                                        <label for="my-comment">Comment</label>
                                        <textarea
                                            id="my-comment"
                                            name="comment"
                                            rows="3"
                                            maxlength="3000"
                                            placeholder="Share your experience..."
                                        >{{ old('comment', $myReview->comment) }}</textarea>
                                    </div>

                                    <div class="form-actions">
                                        <button class="button button-primary" type="submit">
                                            Update review
                                        </button>
                                    </div>
                                </form>

                                <form
                                    method="POST"
                                    action="{{ route('reviews.destroy', $myReview) }}"
                                    onsubmit="return confirm('Permanently delete your review?')"
                                    class="inline-form"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="text-danger">
                                        Delete my review
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="review-form-box">
                                <h3>Write a review</h3>

                                <form
                                    method="POST"
                                    action="{{ route('reviews.store', $salon) }}"
                                    class="stack-form"
                                >
                                    @csrf

                                    <div class="form-field">
                                        <label for="rating">Your rating</label>
                                        <select id="rating" name="rating" required>
                                            <option value="">Choose a rating</option>
                                            @foreach(range(5, 1) as $rating)
                                                <option
                                                    value="{{ $rating }}"
                                                    @selected(old('rating') == $rating)
                                                >
                                                    {{ $rating }} / 5
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-field">
                                        <label for="comment">Comment (optional)</label>
                                        <textarea
                                            id="comment"
                                            name="comment"
                                            rows="3"
                                            maxlength="3000"
                                            placeholder="How was your experience?"
                                        >{{ old('comment') }}</textarea>
                                    </div>

                                    <button class="button button-primary" type="submit">
                                        Submit review
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                @else
                    <p class="muted">
                        <a href="{{ route('login') }}" class="text-link">Log in</a>
                        to share your experience.
                    </p>
                @endauth

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

                        @if($review->comment)
                            <p class="review-comment">{{ $review->comment }}</p>
                        @endif

                        @auth
                            @if(auth()->user()->isAdmin())
                                <div class="review-moderation">
                                    <form
                                        method="POST"
                                        action="{{ route('admin.reviews.hide', $review) }}"
                                    >
                                        @csrf
                                        <button class="text-link" type="submit">
                                            Hide review
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </article>
                @empty
                    <div class="empty-state compact">
                        <h3>Be the first to review</h3>
                        <p>This salon has no visible reviews yet.</p>
                    </div>
                @endforelse
            </section>
        </div>

        <aside class="detail-sidebar">
            <section class="content-panel">
                <div class="panel-heading">
                    <h2>Opening hours</h2>
                </div>

                @include('partials.opening-hours', ['salon' => $salon])
            </section>

            <section class="content-panel owner-card">
                <span class="eyebrow">SALON INFORMATION</span>
                <h3>Need more details?</h3>
                <p class="muted">
                    Contact the salon directly to ask about services and appointments.
                </p>

                <a href="tel:{{ $salon->phone }}" class="button button-primary button-full">
                    Call salon
                </a>
            </section>
        </aside>
    </div>
@endsection