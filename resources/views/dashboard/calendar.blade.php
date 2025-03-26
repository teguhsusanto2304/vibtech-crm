@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        display: flex;
        justify-content: center;
        padding: 20px;
    }
    .calendar-container {
        width: 700px;
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
        padding: 10px;
    }
    .day {
        padding: 15px;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    .day:hover {
        background-color: #007bff;
        color: white;
    }
    .current-month {
        background-color: #e4e6eb;
    }
    .other-month {
        color: #ccc;
    }
    .calendar-navigation a {
        text-decoration: none;
        font-size: 20px;
        font-weight: bold;
        color: #007bff;
    }

    .day {
    position: relative; /* Enable positioning inside the cell */
    padding: 30px 10px; /* More padding to avoid overlap */
    border-radius: 5px;
    text-align: left; /* Align text content */
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.day:hover {
    background-color: #007bff;
    color: white;
}

.day-number {
    position: absolute;
    top: 5px; /* Position at the top */
    right: 8px; /* Align to the right */
    font-size: 14px; /* Adjust size */
    color: black; /* Default color */
}

.other-month .day-number {
    color: #ccc; /* Gray out numbers of other months */
}

.event {
    background-color: #28a745;
    color: white;
    font-size: 9px;
    padding: 5px;
    border-radius: 5px;
    margin-top: 5px;
    text-align: center;
}

.calendar-cell {
    position: relative;
    padding: 15px;
    border-radius: 5px;
    text-align: left; /* Align content normally */
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    min-height: 60px; /* Ensure enough space */
}

.calendar-cell .day-number {
    position: absolute;
    top: 5px;
    right: 5px;
    font-size: 14px;
    color: #666;
}

.event-bar {
        position: absolute;
        left: 5px;
        right: 5px;
        height: 2px; /* Fixed height for bars */
        border-radius: 3px;
        font-size: 10px;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding: 2px 4px;
    }

</style>
<div class="calendar-container">
    <!-- Calendar Header -->
    <div class="calendar-header">
        <a href="{{ url('/calendar?month=' . ($month - 1) . '&year=' . $year) }}">&lt; Prev</a>
        <h2>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h2>
        <a href="{{ url('/calendar?month=' . ($month + 1) . '&year=' . $year) }}">Next &gt;</a>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-grid">
        <!-- Days of the Week -->
        @php
            $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        @endphp
        @foreach ($daysOfWeek as $day)
            <div class="day" style="font-weight: bold; background: #007bff; color: white;">{{ $day }}</div>
        @endforeach

        <!-- Days in Calendar -->
        @foreach ($calendar as $day)
        <div class="calendar-cell {{ $day['isCurrentMonth'] ? 'current-month' : 'other-month' }}">
            <span class="day-number">{{ $day['date']->format('j') }}</span>

            @if (!empty($day['events']))
                @php
                    $eventCount = count($day['events']);
                    $idx = 0;
                    $memEvent = "";
                @endphp
                @foreach ($day['events'] as $index => $event)
                    @php
                        if($memEvent <> $event){
                            //$idx++;
                            $idx = (int) $day['date']->format('j')+10;
                            $memEvent = $event;
                        }

                        $barWidth = 100 / $eventCount; // Calculate width based on event count
                        $barLeft = $index * $barWidth;  // Calculate left offset
                    @endphp
                    <div class="event-bar" style="
                    background-color: {{ $day['colors'][$index] }};
                    color: grey;
                    font-size: 8px;
                    width: {{ $barWidth }}%;
                    left: {{ $barLeft }}%;
                    top: {{ $index * 15 }}px; /* Adjusted spacing */
                ">
                    {{ $event }}
                </div>
            @endforeach
            @endif
        </div>
@endforeach
    </div>
</div>
@endsection
