@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- custom-icon Breadcrumb-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                @foreach ($breadcrumb as $item)
                    <li class="breadcrumb-item">
                        @if($item == 'Vehicle Booking')
                            <a href="{{ route('v1.vehicle-bookings')}}">{{ $item }}</a>
                        @else
                            <a href="javascript:void(0);">{{ $item }}</a>
                        @endif
                        <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                    </li>
                @endforeach
            </ol>
        </nav>

        <h3>{{ $title }}</h3>

        <div class="card">
            <div class="card-body text-center">
                <!-- Display Vehicle Image -->
                @if($booking->vehicle->path_image)
                    <img src="{{ asset( $booking->vehicle->path_image) }}"
                         alt="Vehicle Image"
                         class="img-fluid rounded"
                         style="max-width: 300px; height: auto;">
                @else
                    <p>No image available</p>
                @endif

                <h5 class="card-title mt-3">Vehicle: {{ $booking->vehicle->name ?? 'N/A' }}</h5>
                <p><strong>Start Date:</strong> {{ $booking->start_at }}</p>
                <p><strong>End Date:</strong> {{ $booking->end_at }}</p>
                <p><strong>Purposes:</strong> {{ $booking->purposes }}</p>
                <p><strong>Job Assignment:</strong> {{ $booking->jobAssignment->scope_of_work ?? 'N/A' }}</p>
                <p><strong>Created By:</strong> {{ $booking->creator->name ?? 'Unknown' }}</p>
                <p><strong>Booker Status:</strong> {{ $booking->is_booker ? 'Yes' : 'No' }}</p>

                <a href="{{ route('v1.vehicle-bookings.list') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

    </div>
@endsection
