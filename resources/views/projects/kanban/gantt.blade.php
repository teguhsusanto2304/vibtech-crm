@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <!-- custom-icon Breadcrumb-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                @foreach ($breadcrumb as $item)
                <li class="breadcrumb-item">
                    @if($item == 'Job Assignment Form')
                    <a href="{{ route('v1.job-assignment-form')}}">{{ $item }}</a>
                    @else
                    <a href="javascript:void(0);">{{ $item }}</a>
                    @endif
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                @endforeach
            </ol>
        </nav>
    </div>
    <div class="p-6">
        <div class="col-md-7 mb-4">
                            <div class="table-responsive"> {{-- Optional: Makes table scrollable on small screens --}}
                                <table class="table table-borderless table-sm"> {{-- table-borderless for no borders, table-sm for compact --}}
                                    <tbody>
                                        <tr>
                                            <td class="detail-label" style="width: 40%;">Project Name:</td> {{-- Adjust width for label column --}}
                                            <td class="detail-value text-muted">{{ $project->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Project Description:</td>
                                            <td class="detail-value text-muted">{{ $project->description }}</td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Start Date:</td>
                                            <td class="detail-value text-muted">{{ $project->start_at->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">End Date:</td>
                                            <td class="detail-value text-muted">{{ $project->end_at->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Remaining Days:</td>
                                            <td class="detail-value text-muted">
                                                <div id="dayProgress"></div>
                                            </td>
                                        <tr>
                                        <tr>
                                            <td class="detail-label">Progress:</td>
                                            <td class="detail-value text-muted">
                                                <div id="workProgress"></div>
                                            </td>
                                        <tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <a href="{{ route('v1.project-management.gantt-daily',['projectId'=>$project->obfuscated_id]) }}" class="btn btn-sm bg-info text-white mb-2">Go to Detail Gantt Chart</a>
    <div id="gantt_here" style="width: 100%; height: 400px;"></div>
</div>
</div>

<link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>

    <script>
       gantt.config.columns = [
                {name: "text", label: "Task name", width: "*", tree: true},
                {name: "duration", label: "Duration", align: "center", width: 60},
            ];
        gantt.config.lightbox = false;
        gantt.attachEvent("onBeforeLightbox", function () {
            return false;
        });

        gantt.config.scale_unit = "week";
    gantt.config.date_scale = "%F %Y"; // e.g., August 2025

    // Disable subscales like week/day
    gantt.config.subscales = [
    {
        unit: "week",
        step: 1,
        date: function(date) {
            // Format as: Week 32 (or use your own format)
            const weekNum = gantt.date.getISOWeek(date);
            return "Week " + weekNum;
        }
    }
];

gantt.config.scale_height = 60;
    gantt.config.row_height = 35;

        gantt.init("gantt_here");

        gantt.parse(@json($ganttData));
    </script>
    @endsection
