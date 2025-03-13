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
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Oops! Something went wrong:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <form class="row g-3"
    action="{{ isset($booking) ? route('v1.vehicle-bookings.update', $booking->id) : route('v1.vehicle-bookings.store') }}"
    method="post">
    @csrf
    @if(isset($booking))
        @method('PUT') {{-- Required for updating records --}}
    @endif

    <style>
        .car-image {
            width: 400px;
            height: auto;
            margin-bottom: 20px;
            border-radius: 10px;
        }
    </style>

    <div class="col-md-12">
        <label for="claimType" style="margin-bottom: 10px;">Choose Vehicle:</label>
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <img src="{{ isset($booking) && $booking->vehicle ? asset($booking->vehicle->path_image) : asset('assets/img/cars/default.svg') }}"
                         alt="Selected Car" class="car-image" id="selectedCar">
                </div>
                <div class="col-md-6">
                    <div class="radio-group">
                        @foreach ($vehicles as $vehicle)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="vehicle_id"
                                       id="vehicle_{{ $vehicle->id }}" value="{{ $vehicle->id }}"
                                       {{ isset($booking) && $booking->vehicle_id == $vehicle->id ? 'checked' : '' }}>
                                <label class="form-check-label" for="vehicle_{{ $vehicle->id }}">
                                    <span class="car-code">{{ $vehicle->name }}</span>
                                </label>
                            </div>
                        @endforeach

                        <script>
                            $(document).ready(function () {
                                var carImages = {}; // Object to store car images from the database

                                // Fetch car images from the server
                                $.ajax({
                                    url: "{{ route('v1.vehicles.car-image') }}", // Laravel route
                                    type: "GET",
                                    dataType: "json",
                                    success: function (response) {
                                        response.forEach(car => {
                                            carImages[car.id] = car.path_image; // Store image path using car ID
                                        });
                                    },
                                    error: function () {
                                        console.error("Error fetching car images.");
                                    }
                                });

                                // Listen for radio button selection
                                $('input[type="radio"]').change(function () {
                                    var carId = $(this).val();
                                    var carImage = $('#selectedCar');

                                    // Set the image src dynamically from the fetched data
                                    carImage.attr('src', "{{ asset('') }}" + carImages[carId] );
                                });
                            });
                        </script>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="datetime-local" class="form-control" name="start_at"
                           value="{{ old('start_at', isset($booking) ? $booking->start_at : '') }}" />
                </div>

                <div class="col-md-6">
                    <label for="startDate" class="form-label">End Date</label>
                    <input type="datetime-local" class="form-control" name="end_at"
                           value="{{ old('end_at', isset($booking) ? $booking->end_at : '') }}" />
                </div>

                <div class="col-md-6 mt-5">
                    <label for="startDate" class="form-label">Purposes</label>
                    <textarea class="form-control" name="purposes" cols="5" rows="5">{{ old('purposes', isset($booking) ? $booking->purposes : '') }}</textarea>
                </div>

                <div class="col-md-6 mt-5">
                    <label for="startDate" class="form-label">Job Requisition Form</label>
                    <select class="form-control" name="job_assignment_id">
                        <option value="">Choose a job</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job->id }}"
                                    {{ isset($booking) && $booking->job_assignment_id == $job->id ? 'selected' : '' }}>
                                {{ $job->scope_of_work }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-12 mt-5">
                    <button type="submit" class="btn btn-primary">{{ isset($booking) ? 'Update' : 'Submit' }}</button>
                    <a href="{{ route('v1.vehicle-bookings.list') }}" class="btn btn-warning">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>



            </div>
        </div>
    </div>
    </div>
@endsection
