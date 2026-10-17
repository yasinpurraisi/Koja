@extends('layouts.app')

@section('title', 'Create salon listing')

@section('content')
    <div class="page-heading">
        <a href="{{ route('owner.dashboard') }}" class="back-link">
            ← Back to dashboard
        </a>

        <span class="eyebrow">OWNER WORKSPACE</span>
        <h1>Add your salon</h1>
        <p class="muted">
            Fill in the details. Your listing will be reviewed before publication.
        </p>
    </div>

    @include('owner.salons._form')
@endsection