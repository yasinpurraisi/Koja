@extends('layouts.app')

@section('title', 'Register Your Salon')

@section('content')
<div class="container page-section">

    <div class="page-heading">
        <div>
            <p class="eyebrow">For salon owners</p>
            <h1>List your salon on Koja</h1>
            <p>
                Help customers discover your salon. Submit your details below
                and an administrator will review your listing before it goes public.
            </p>
        </div>
    </div>

    <div class="form-card">
        <div class="alert alert-info">
            <strong>How it works</strong>
            <p class="mb-0">
                Your salon will initially have pending status. It won't appear
                in public search results until an administrator approves it.
            </p>
        </div>

        @if ($errors->any())
            <div class="form-errors" role="alert">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('salons.owner-register.store') }}"
            class="form-stack"
        >
            @csrf

            <div class="form-group">
                <label for="name">Salon name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    maxlength="255"
                    required
                    autocomplete="organization"
                    placeholder="e.g. Green Leaf Salon"
                >
                @error('name')
                    <span class="text-danger text-small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea
                    id="description"
                    name="description"
                    maxlength="5000"
                    rows="5"
                    placeholder="Tell customers about your salon and the services you offer."
                >{{ old('description') }}</textarea>
                <p class="form-help">Optional. Maximum 5,000 characters.</p>
                @error('description')
                    <span class="text-danger text-small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">Salon phone number</label>
                <input
                    id="phone"
                    name="phone"
                    type="tel"
                    value="{{ old('phone') }}"
                    maxlength="30"
                    required
                    autocomplete="tel"
                    placeholder="Salon contact number"
                >
                @error('phone')
                    <span class="text-danger text-small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="city">City</label>
                <input
                    id="city"
                    name="city"
                    type="text"
                    value="{{ old('city') }}"
                    maxlength="120"
                    required
                    placeholder="e.g. Kerman"
                >
                @error('city')
                    <span class="text-danger text-small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Full address</label>
                <textarea
                    id="address"
                    name="address"
                    maxlength="1000"
                    rows="3"
                    required
                    autocomplete="street-address"
                    placeholder="Street, building, and other address details"
                >{{ old('address') }}</textarea>
                @error('address')
                    <span class="text-danger text-small">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <h3>Map coordinates</h3>
                <p class="form-help">
                    Optional for now. You can leave these blank if you don't
                    know your salon's coordinates.
                </p>
            </div>

            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input
                    id="latitude"
                    name="latitude"
                    type="number"
                    step="any"
                    min="-90"
                    max="90"
                    value="{{ old('latitude') }}"
                    placeholder="e.g. 30.2839"
                >
                @error('latitude')
                    <span class="text-danger text-small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input
                    id="longitude"
                    name="longitude"
                    type="number"
                    step="any"
                    min="-180"
                    max="180"
                    value="{{ old('longitude') }}"
                    placeholder="e.g. 57.0834"
                >
                @error('longitude')
                    <span class="text-danger text-small">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">
                    Submit salon for approval
                </button>

                <a
                    href="{{ route('salons.index') }}"
                    class="button button-secondary"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection