@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container dashboard">

    {{-- Header --}}
    <header class="dashboard-header">
        <div>
            <p class="eyebrow">Koja administration</p>
            <h1>Admin dashboard</h1>
            <p>Review salon submissions and manage the Koja community.</p>
        </div>

        <a href="{{ route('salons.index') }}" class="button button-secondary">
            View public site
        </a>
    </header>

    {{-- Statistics --}}
    <section class="dashboard-stats" aria-label="Platform statistics">
        <article class="stat-card">
            <p class="stat-label">Total users</p>
            <strong class="stat-value">
                {{ $stats['users'] ?? $users->total() }}
            </strong>
        </article>

        <article class="stat-card">
            <p class="stat-label">Total salons</p>
            <strong class="stat-value">
                {{ $stats['salons'] ?? $salons->total() }}
            </strong>
        </article>

        <article class="stat-card">
            <p class="stat-label">Awaiting approval</p>
            <strong class="stat-value">
                {{ $stats['pending_salons'] ?? $pendingSalons->count() }}
            </strong>
        </article>

        <article class="stat-card">
            <p class="stat-label">Hidden reviews</p>
            <strong class="stat-value">
                {{ $stats['hidden_reviews'] ?? $reviews->total() }}
            </strong>
        </article>
    </section>

    {{-- Pending salon approvals --}}
    <section class="dashboard-section" id="pending-salons">
        <div class="section-heading">
            <div>
                <h2>Pending salon approvals</h2>
                <p class="text-small mb-0">
                    These salons are not yet visible in the public directory.
                </p>
            </div>

            <span class="status status-pending">
                {{ $pendingSalons->count() }} pending
            </span>
        </div>

        @if ($pendingSalons->isEmpty())
            <div class="empty-state">
                <h3>You're all caught up</h3>
                <p>There are no salon submissions waiting for approval.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Salon</th>
                            <th>Owner</th>
                            <th>Location</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pendingSalons as $salon)
                            <tr>
                                <td>
                                    <span class="table-primary">
                                        {{ $salon->name }}
                                    </span>
                                    <span class="table-secondary">
                                        {{ $salon->phone }}
                                    </span>
                                </td>

                                <td>
                                    {{ $salon->owner?->name ?? 'Unknown' }}
                                    @if ($salon->owner?->phone)
                                        <span class="table-secondary">
                                            {{ $salon->owner->phone }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $salon->city }}
                                    <span class="table-secondary">
                                        {{ $salon->address }}
                                    </span>
                                </td>

                                <td>
                                    {{ $salon->created_at?->diffForHumans() ?? '—' }}
                                </td>

                                <td>
                                    <div class="table-actions">
                                        <form
                                            method="POST"
                                            action="{{ route('admin.salons.approve', $salon) }}"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="button button-primary button-small"
                                            >
                                                Approve
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.salons.reject', $salon) }}"
                                            onsubmit="return confirm('Reject this salon submission?')"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="button button-danger button-small"
                                            >
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- All salons --}}
    <section class="dashboard-section" id="all-salons">
        <div class="section-heading">
            <div>
                <h2>Manage salons</h2>
                <p class="text-small mb-0">
                    Review salon status and suspend or restore listings.
                </p>
            </div>
        </div>

        @if ($salons->isEmpty())
            <div class="empty-state">
                <h3>No salons found</h3>
                <p>Salon listings will appear here when they are submitted.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Salon</th>
                            <th>Owner</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($salons as $salon)
                            <tr>
                                <td>
                                    <span class="table-primary">
                                        {{ $salon->name }}
                                    </span>
                                    <span class="table-secondary">
                                        {{ $salon->phone }}
                                    </span>
                                </td>

                                <td>
                                    {{ $salon->owner?->name ?? 'Unknown' }}
                                </td>

                                <td>{{ $salon->city }}</td>

                                <td>
                                    <span class="status status-{{ $salon->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $salon->status)) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="table-actions">
                                        @if ($salon->status === 'approved')
                                            <form
                                                method="POST"
                                                action="{{ route('admin.salons.suspend', $salon) }}"
                                                onsubmit="return confirm('Suspend this salon? It will no longer appear publicly.')"
                                            >
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="button button-secondary button-small"
                                                >
                                                    Suspend
                                                </button>
                                            </form>
                                        @elseif ($salon->status === 'suspended')
                                            <form
                                                method="POST"
                                                action="{{ route('admin.salons.restore', $salon) }}"
                                                onsubmit="return confirm('Restore this salon?')"
                                            >
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="button button-primary button-small"
                                                >
                                                    Restore
                                                </button>
                                            </form>
                                        @elseif ($salon->status === 'pending')
                                            <form
                                                method="POST"
                                                action="{{ route('admin.salons.approve', $salon) }}"
                                            >
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="button button-primary button-small"
                                                >
                                                    Approve
                                                </button>
                                            </form>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.salons.reject', $salon) }}"
                                            >
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="button button-danger button-small"
                                                >
                                                    Reject
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-small muted">
                                                No actions
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Salon pagination --}}
            @if ($salons->hasPages())
                <div class="pagination-wrap">
                    <div class="pagination">
                        @if ($salons->onFirstPage())
                            <span class="disabled" aria-disabled="true">Previous</span>
                        @else
                            <a href="{{ $salons->previousPageUrl() }}" rel="prev">
                                Previous
                            </a>
                        @endif

                        @php
                            $salonStart = max(1, $salons->currentPage() - 2);
                            $salonEnd = min($salons->lastPage(), $salons->currentPage() + 2);
                        @endphp

                        @foreach ($salons->getUrlRange($salonStart, $salonEnd) as $page => $url)
                            @if ($page === $salons->currentPage())
                                <span class="active" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($salons->hasMorePages())
                            <a href="{{ $salons->nextPageUrl() }}" rel="next">
                                Next
                            </a>
                        @else
                            <span class="disabled" aria-disabled="true">Next</span>
                        @endif
                    </div>
                </div>

                <p class="pagination-summary">
                    Showing {{ $salons->firstItem() }}–{{ $salons->lastItem() }}
                    of {{ $salons->total() }} salons
                </p>
            @endif
        @endif
    </section>

    {{-- User management --}}
    <section class="dashboard-section" id="users">
        <div class="section-heading">
            <div>
                <h2>Manage users</h2>
                <p class="text-small mb-0">
                    Suspend or restore customer and owner accounts.
                </p>
            </div>
        </div>

        @if ($users->isEmpty())
            <div class="empty-state">
                <h3>No users found</h3>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <span class="table-primary">
                                        {{ $user->name }}
                                    </span>
                                    <span class="table-secondary">
                                        User #{{ $user->id }}
                                    </span>
                                </td>

                                <td>{{ $user->phone }}</td>

                                <td>
                                    <span class="status status-{{ $user->role }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="status status-{{ $user->status }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $user->created_at?->format('M j, Y') ?? '—' }}
                                </td>

                                <td>
                                    <div class="table-actions">
                                        @if ($user->role === 'admin')
                                            <span class="text-small muted">
                                                Admin protected
                                            </span>
                                        @elseif ($user->status === 'active')
                                            <form
                                                method="POST"
                                                action="{{ route('admin.users.suspend', $user) }}"
                                                onsubmit="return confirm('Suspend this account?')"
                                            >
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="button button-danger button-small"
                                                >
                                                    Suspend
                                                </button>
                                            </form>
                                        @elseif ($user->status === 'suspended')
                                            <form
                                                method="POST"
                                                action="{{ route('admin.users.restore', $user) }}"
                                                onsubmit="return confirm('Restore this account?')"
                                            >
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="button button-primary button-small"
                                                >
                                                    Restore
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- User pagination --}}
            @if ($users->hasPages())
                <div class="pagination-wrap">
                    <div class="pagination">
                        @if ($users->onFirstPage())
                            <span class="disabled" aria-disabled="true">Previous</span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" rel="prev">
                                Previous
                            </a>
                        @endif

                        @php
                            $userStart = max(1, $users->currentPage() - 2);
                            $userEnd = min($users->lastPage(), $users->currentPage() + 2);
                        @endphp

                        @foreach ($users->getUrlRange($userStart, $userEnd) as $page => $url)
                            @if ($page === $users->currentPage())
                                <span class="active" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" rel="next">Next</a>
                        @else
                            <span class="disabled" aria-disabled="true">Next</span>
                        @endif
                    </div>
                </div>

                <p class="pagination-summary">
                    Showing {{ $users->firstItem() }}–{{ $users->lastItem() }}
                    of {{ $users->total() }} users
                </p>
            @endif
        @endif
    </section>

    {{-- Hidden reviews --}}
    <section class="dashboard-section" id="hidden-reviews">
        <div class="section-heading">
            <div>
                <h2>Hidden reviews</h2>
                <p class="text-small mb-0">
                    Restore a review or permanently delete it.
                </p>
            </div>
        </div>

        @if ($reviews->isEmpty())
            <div class="empty-state">
                <h3>No hidden reviews</h3>
                <p>There are no hidden reviews requiring moderation.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Salon</th>
                            <th>Author</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($reviews as $review)
                            <tr>
                                <td>
                                    {{ $review->salon?->name ?? 'Deleted salon' }}
                                </td>

                                <td>
                                    {{ $review->user?->name ?? 'Deleted user' }}
                                </td>

                                <td>
                                    <span class="rating">
                                        {{ $review->rating }}/5
                                    </span>
                                </td>

                                <td>
                                    {{ \Illuminate\Support\Str::limit($review->comment ?? 'No comment', 100) }}
                                </td>

                                <td>
                                    <span class="status status-{{ $review->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $review->status)) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="table-actions">
                                        <form
                                            method="POST"
                                            action="{{ route('admin.reviews.restore', $review) }}"
                                            onsubmit="return confirm('Restore this review?')"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="button button-primary button-small"
                                            >
                                                Restore
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.reviews.destroy', $review) }}"
                                            onsubmit="return confirm('Permanently delete this review? This cannot be undone.')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="button button-danger button-small"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Hidden review pagination --}}
            @if ($reviews->hasPages())
                <div class="pagination-wrap">
                    <div class="pagination">
                        @if ($reviews->onFirstPage())
                            <span class="disabled" aria-disabled="true">Previous</span>
                        @else
                            <a href="{{ $reviews->previousPageUrl() }}" rel="prev">
                                Previous
                            </a>
                        @endif

                        @php
                            $reviewStart = max(1, $reviews->currentPage() - 2);
                            $reviewEnd = min($reviews->lastPage(), $reviews->currentPage() + 2);
                        @endphp

                        @foreach ($reviews->getUrlRange($reviewStart, $reviewEnd) as $page => $url)
                            @if ($page === $reviews->currentPage())
                                <span class="active" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($reviews->hasMorePages())
                            <a href="{{ $reviews->nextPageUrl() }}" rel="next">Next</a>
                        @else
                            <span class="disabled" aria-disabled="true">Next</span>
                        @endif
                    </div>
                </div>

                <p class="pagination-summary">
                    Showing {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }}
                    of {{ $reviews->total() }} hidden reviews
                </p>
            @endif
        @endif
    </section>

</div>
@endsection