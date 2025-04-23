@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Breadcrumb -->
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

            <h3>{{ $title }}</h3>

            <!-- Custom Styles -->
            <style>
                .btn_primary {
                    background-color: #003366;
                    color: #fff;
                }

                .btn_primary:hover {
                    background-color: #f0cf27;
                    color: #fff;
                }
            </style>

            <!-- Responsive Cards -->
            <div class="tab-pane fade show active" id="btn-text-alignment-preview" role="tabpanel"
                aria-labelledby="btn-text-alignment-preview-tab">
                <div class="row gy-4">
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Book A Vehicle</h5>
                                @can('create-job-requisition')
                                <a href="{{ route('v1.vehicle-bookings.create') }}" class="btn btn_primary">Create New Vehicle Booking</a>
                                @else
                                <a href="#" class="btn btn_primary">You Can't Create New</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Current Vehicle Booking</h5>
                                <a href="{{ route('v1.vehicle-bookings.list') }}" class="btn btn_primary">View Current Vehicle Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Vehicle Booking History </h5>
                                <a href="{{ route('v1.vehicle-bookings.histories') }}" class="btn btn_primary">View Vehicle Booking History</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
@endsection
