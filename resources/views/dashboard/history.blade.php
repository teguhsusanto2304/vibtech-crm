@extends('layouts.app')

@section('title', 'Event History')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom-icon">
            @foreach ($breadcrumb as $item)
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">{{ $item }}</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
            @endforeach
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>{{ $title }}</h3>
        <a href="{{ route('v1.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-calendar me-1"></i> Back to Calendar
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('v1.dashboard.event-history') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        @foreach(range(date('Y')-2, date('Y')+2) as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter-alt me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

<x-booking-modal />
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Event Name</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
    @forelse($events as $event)
        <tr>
            <td>
                <span class="fw-bold">
                    {{ \Carbon\Carbon::parse($event['start'])->format('d M Y') }}
                </span>
            </td>
            <td>{{ $event['title'] }}</td>
            <td>
                <span class="badge {{ $event['badge_color'] }}">
                    {{ $event['type'] }}
                </span>
            </td>
            <td>
                @if($event['type'] == 'VB')
                    <button type="button" 
        class="btn btn-sm btn-icon btn-outline-primary view-booking" 
        data-bs-toggle="modal" 
        data-bs-target="#bookingModal" 
        data-id="{{ $event['id'] }}"><i class="bx bx-show"></i></button>
                @else
                <a href="{{ $event['url'] }}" class="btn btn-sm btn-icon btn-outline-primary">
                    <i class="bx bx-show"></i>
                </a>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center py-5">
                <div class="text-muted">No history found for {{ date('F', mktime(0,0,0,$selectedMonth)) }} {{ $selectedYear }}</div>
            </td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>
</div>
@endsection