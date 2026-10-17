<article class="salon-card">
    <div class="salon-card-top">
        <div class="salon-placeholder">
            {{ mb_strtoupper(mb_substr($salon->name, 0, 1)) }}
        </div>

        <div class="salon-card-heading">
            <h2>
                <a href="{{ route('salons.show', $salon) }}">
                    {{ $salon->name }}
                </a>
            </h2>

            <p class="muted">
                {{ $salon->city }}
            </p>
        </div>
    </div>

    <p class="salon-card-description">
        {{ $salon->description ?: 'Discover this salon and learn more about its services.' }}
    </p>

    <div class="salon-card-meta">
        <span>
            <span class="star">★</span>
            @if(($salon->reviews_count ?? 0) > 0)
                {{ number_format((float) ($salon->reviews_avg_rating ?? 0), 1) }}
                <span class="muted">({{ $salon->reviews_count }} reviews)</span>
            @else
                <span class="muted">No reviews yet</span>
            @endif
        </span>

        <a href="{{ route('salons.show', $salon) }}" class="text-link">
            View salon →
        </a>
    </div>

    <p class="salon-address">
        <span aria-hidden="true">⌖</span>
        {{ $salon->address }}
    </p>
</article>