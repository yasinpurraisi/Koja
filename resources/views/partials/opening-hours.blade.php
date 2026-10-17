@php
    $dayNames = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];

    $hoursByDay = $salon->openingHours->keyBy('day_of_week');
@endphp

<div class="hours-list">
    @foreach($dayNames as $dayNumber => $dayName)
        @php($hours = $hoursByDay->get($dayNumber))

        <div class="hours-row">
            <span>{{ $dayName }}</span>

            @if(!$hours)
                <span class="muted">Not set</span>
            @elseif($hours->is_closed)
                <span class="status-text status-danger">Closed</span>
            @elseif($hours->opens_at && $hours->closes_at)
                <span>
                    {{ \Illuminate\Support\Carbon::parse($hours->opens_at)->format('g:i A') }}
                    –
                    {{ \Illuminate\Support\Carbon::parse($hours->closes_at)->format('g:i A') }}
                </span>
            @else
                <span class="muted">Not set</span>
            @endif
        </div>
    @endforeach
</div>