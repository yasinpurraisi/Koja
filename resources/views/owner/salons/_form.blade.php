@php
    $editing = isset($salon);
@endphp

<form
    method="POST"
    action="{{ $editing
        ? route('owner.salon.update', $salon)
        : route('owner.salon.store') }}"
    class="content-panel stack-form"
>
    @csrf

    @if($editing)
        @method('PUT')
    @endif

    <div class="form-field">
        <label for="name">Salon name *</label>
        <input
            id="name"
            name="name"
            type="text"
            maxlength="255"
            value="{{ old('name', $salon->name ?? '') }}"
            required
        >
    </div>

    <div class="form-field">
        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            rows="5"
            maxlength="5000"
            placeholder="Tell customers what makes your salon special..."
        >{{ old('description', $salon->description ?? '') }}</textarea>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="phone">Phone number *</label>
            <input
                id="phone"
                name="phone"
                type="text"
                maxlength="30"
                value="{{ old('phone', $salon->phone ?? '') }}"
                required
            >
        </div>

        <div class="form-field">
            <label for="city">City *</label>
            <input
                id="city"
                name="city"
                type="text"
                maxlength="100"
                value="{{ old('city', $salon->city ?? '') }}"
                required
            >
        </div>
    </div>

    <div class="form-field">
        <label for="address">Full address *</label>
        <input
            id="address"
            name="address"
            type="text"
            maxlength="255"
            value="{{ old('address', $salon->address ?? '') }}"
            required
        >
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="latitude">Latitude (optional)</label>
            <input
                id="latitude"
                name="latitude"
                type="number"
                step="any"
                min="-90"
                max="90"
                value="{{ old('latitude', $salon->latitude ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="longitude">Longitude (optional)</label>
            <input
                id="longitude"
                name="longitude"
                type="number"
                step="any"
                min="-180"
                max="180"
                value="{{ old('longitude', $salon->longitude ?? '') }}"
            >
        </div>
    </div>

    <div class="form-actions">
        <button class="button button-primary" type="submit">
            {{ $editing ? 'Save changes' : 'Submit for approval' }}
        </button>

        <a href="{{ route('owner.dashboard') }}" class="button button-ghost">
            Cancel
        </a>
    </div>
</form>