@extends('layouts.app')

@section('title', 'Edit salon')

@section('content')
    <div class="page-heading">
        <a href="{{ route('owner.dashboard') }}" class="back-link">
            ← Back to dashboard
        </a>

        <span class="eyebrow">OWNER WORKSPACE</span>
        <h1>Edit {{ $salon->name }}</h1>
        <p class="muted">
            Updating an existing listing does not require a new approval.
        </p>
    </div>

    @include('owner.salons._form', ['salon' => $salon])

    <section class="content-panel hours-editor" id="opening-hours">
        <div class="panel-heading">
            <div>
                <h2>Opening hours</h2>
                <p class="muted">Set opening and closing times for each day.</p>
            </div>
        </div>

        @php
            $hoursByDay = $salon->openingHours->keyBy('day_of_week');

            $dayNames = [
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                7 => 'Sunday',
            ];
        @endphp

        <form
            method="POST"
            action="{{ route('owner.salon.opening-hours.update', $salon) }}"
            class="stack-form"
        >
            @csrf
            @method('PUT')

            @foreach($dayNames as $dayNumber => $dayName)
                @php
                    $hours = $hoursByDay->get($dayNumber);

                    $opensAt = $hours?->opens_at
                        ? substr((string) $hours->opens_at, 0, 5)
                        : '09:00';

                    $closesAt = $hours?->closes_at
                        ? substr((string) $hours->closes_at, 0, 5)
                        : '18:00';
                @endphp

                <div class="opening-editor-row">
                    <input
                        type="hidden"
                        name="opening_hours[{{ $dayNumber }}][day_of_week]"
                        value="{{ $dayNumber }}"
                    >

                    <strong>{{ $dayName }}</strong>

                    <input
                        type="hidden"
                        name="opening_hours[{{ $dayNumber }}][is_closed]"
                        value="0"
                    >

                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            name="opening_hours[{{ $dayNumber }}][is_closed]"
                            value="1"
                            @checked(old("opening_hours.$dayNumber.is_closed", $hours?->is_closed ?? false))
                            onchange="toggleHours(this, {{ $dayNumber }})"
                        >
                        Closed
                    </label>

                    <div class="time-inputs" id="hours-times-{{ $dayNumber }}">
                        <label class="visually-hidden" for="open-{{ $dayNumber }}">
                            Opening time for {{ $dayName }}
                        </label>

                        <input
                            id="open-{{ $dayNumber }}"
                            type="time"
                            name="opening_hours[{{ $dayNumber }}][opens_at]"
                            value="{{ old("opening_hours.$dayNumber.opens_at", $opensAt) }}"
                        >

                        <span class="muted">to</span>

                        <label class="visually-hidden" for="close-{{ $dayNumber }}">
                            Closing time for {{ $dayName }}
                        </label>

                        <input
                            id="close-{{ $dayNumber }}"
                            type="time"
                            name="opening_hours[{{ $dayNumber }}][closes_at]"
                            value="{{ old("opening_hours.$dayNumber.closes_at", $closesAt) }}"
                        >
                    </div>
                </div>
            @endforeach

            <div class="form-actions">
                <button type="submit" class="button button-primary">
                    Save opening hours
                </button>
            </div>
        </form>
    </section>

    <script>
        function toggleHours(checkbox, day) {
            const fields = document.getElementById(`hours-times-${day}`);
            const inputs = fields.querySelectorAll('input[type="time"]');

            fields.classList.toggle('hours-disabled', checkbox.checked);

            inputs.forEach(input => {
                input.disabled = checkbox.checked;
            });
        }

        document.querySelectorAll('.opening-editor-row input[type="checkbox"]')
            .forEach(checkbox => {
                const day = checkbox.name.match(/\[(\d+)\]\[is_closed\]/)?.[1];

                if (day) {
                    toggleHours(checkbox, day);
                }
            });
    </script>
@endsection