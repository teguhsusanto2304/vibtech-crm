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
                  @method('PUT') <!-- Use PUT for updating -->
              @endif

              <style>
                  .car-image {
                      width: 100px;
                      height: auto;
                      margin-right: 10px;
                      border-radius: 10px;
                  }
              </style>

              <div class="col-md-3">
                  <label for="startDate" class="form-label">Start Date</label>
                  <input type="datetime-local" class="form-control" id="start_at" name="start_at" value="{{ old('start_at', isset($booking) ? \Carbon\Carbon::parse($booking->start_at)->format('Y-m-d') : '') }}" required />
              </div>
              <div class="col-md-3">
                  <label for="endDate" class="form-label">End Date</label>
                  <input type="datetime-local" class="form-control" id="end_at" name="end_at" value="{{ old('end_at', isset($booking) ? \Carbon\Carbon::parse($booking->end_at)->format('Y-m-d') : '') }}" required />
              </div>

              <div class="col-md-6 mt-3">
                  <button type="button" class="btn btn-info mt-6" onclick="fetchAvailableVehicles()">Find Available Vehicles</button>
              </div>

              <!-- Radio Button List -->
              <div class="col-md-12 mt-4">
                  <label class="form-label">Select a Vehicle</label>
                  <div class="row" id="vehicleList"></div>
              </div>

              <div class="col-md-6 mt-5">
                  <label for="purposes" class="form-label">Purposes</label>
                  <textarea class="form-control" name="purposes" cols="5" rows="5">{{ old('purposes', isset($booking) ? $booking->purposes : '') }}</textarea>
              </div>

              <div class="col-md-6 mt-5">
                  <label for="job_assignment_id" class="form-label">Job Requisition Form</label>
                  <select class="form-control" name="job_assignment_id">
                      <option value="">Choose a job</option>
                      @foreach($jobs as $job)
                          <option value="{{ $job->id }}"
                              {{ isset($booking) && $booking->job_assignment_id == $job->id ? 'selected' : '' }}>
                              {{ $job->job_type }} | {{ $job->job_record_id }}
                          </option>
                      @endforeach
                  </select>
              </div>

              <div class="col-md-12 mt-5">
                  <button type="submit" class="btn btn-primary">
                      {{ isset($booking) ? 'Update' : 'Submit' }}
                  </button>
                  <a href="{{ route('v1.vehicle-bookings.list') }}" class="btn btn-warning">Cancel</a>
              </div>
          </form>

          <script>
            async function fetchAvailableVehicles() {
                let start_at = document.getElementById("start_at").value;
                let end_at = document.getElementById("end_at").value;
                let selectedVehicleId = "{{ isset($booking) ? $booking->vehicle_id : '' }}"; // Get the selected vehicle if editing

                if (!start_at || !end_at) {
                    alert("Please select both start and end date.");
                    return;
                }

                try {
                    let response = await fetch(`/v1/vehicle-bookings/available-vehicles?start_at=${start_at}&end_at=${end_at}&id={{ $booking->id }}`);
                    let vehicles = await response.json();

                    let vehicleList = document.getElementById("vehicleList");
                    vehicleList.innerHTML = ''; // Clear existing radios

                    if (vehicles.length === 0) {
                        vehicleList.innerHTML = "<p>No available vehicles found.</p>";
                        return;
                    }

                    vehicles.forEach(vehicle => {
                        let div = document.createElement("div");
                        div.classList.add("form-check", "mb-2", "col-sm-6");

                        let radio = document.createElement("input");
                        radio.type = "radio";
                        radio.classList.add("form-check-input");
                        radio.name = "vehicle_id";
                        radio.value = vehicle.id;
                        radio.id = "vehicle_" + vehicle.id;

                        // Pre-select if it's the current booked vehicle
                        if (vehicle.id == selectedVehicleId) {
                            radio.checked = true;
                        }

                        let label = document.createElement("label");
                        label.classList.add("form-check-label");
                        label.setAttribute("for", "vehicle_" + vehicle.id);
                        label.innerHTML = `<img src="${vehicle.image_url}" class="car-image" alt="${vehicle.name}"> ${vehicle.name} - ${vehicle.plate_number}`;

                        div.appendChild(radio);
                        div.appendChild(label);
                        vehicleList.appendChild(div);
                    });
                } catch (error) {
                    console.error("Error fetching vehicles:", error);
                    alert("Failed to fetch available vehicles.");
                }
            }

            // Automatically load available vehicles if editing
            @if(isset($booking))
                window.onload = fetchAvailableVehicles;
            @endif
        </script>



            </div>
        </div>
    </div>
    </div>
@endsection
