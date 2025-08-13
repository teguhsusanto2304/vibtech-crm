@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $project->name }} – Gantt Overview</h1>

    <div class="mb-2 small text-muted">
        Timeline: {{ $minDate->format('M d, Y') }} → {{ $maxDate->format('M d, Y') }} ({{ $totalDays }} days)
    </div>

    <div class="border p-3 rounded">
        @foreach ($project->projectKanban as $kanban)
            <h5 class="mt-4">{{ $kanban->name }}</h5>

            @foreach ($kanban->tasks as $task)
                @php
                    $taskStart = \Carbon\Carbon::parse($task->start_at);
                    $taskEnd = \Carbon\Carbon::parse($task->end_at);
                    $offset = $minDate->diffInDays($taskStart);
                    $duration = $taskStart->diffInDays($taskEnd) + 1;

                    $offsetPercent = ($offset / $totalDays) * 100;
                    $widthPercent = ($duration / $totalDays) * 100;
                @endphp

                <div class="mb-2">
                    <div class="d-flex justify-content-between small">
                        <span>{{ $task->name }}</span>
                        <span>{{ $taskStart->format('M d') }} → {{ $taskEnd->format('M d') }}</span>
                    </div>

                    <div class="position-relative" style="height: 24px; background: #f1f1f1;">
                        <div class="position-absolute bg-primary text-white text-center small"
                             style="left: {{ $offsetPercent }}%; width: {{ $widthPercent }}%; height: 100%;">
                            {{ $task->progress_percentage }}%
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</div>
@endsection
