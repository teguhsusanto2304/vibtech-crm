@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
            <x-breadcrumb :breadcrumb="$breadcrumb" :title="$title" />
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

                .equal-height-cards .card {
                    min-height: 100%;
                    /* Ensure all cards have the same height */
                }
            </style>

            <!-- Responsive Cards -->
            <div class="tab-pane fade show active" id="btn-text-alignment-preview" role="tabpanel"
                aria-labelledby="btn-text-alignment-preview-tab">
                <div class="row gy-4 {{ $title == 'Job Requisition Form' ? 'equal-height-cards' : '' }}">
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Create</h5>
                                <p class="card-text">Create a New {{ $title }}</p>
                                @can('create-job-requisition')
                                    <a href="{{ route('v1.job-assignment-form.create') }}" class="btn btn_primary mt-auto">
                                        Create New</a>
                                @else
                                    <a href="#" class="btn btn_primary mt-auto">You Can't Create New</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">View</h5>
                                <p class="card-text">Track Existing Job Requisition</p>
                                <a href="{{ route('v1.job-assignment-form.list') }}" class="btn btn_primary mt-auto">View
                                    Existing</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">History</h5>
                                <p class="card-text">View Job Requisition History</p>
                                <a href="{{ route('v1.job-assignment-form.history')}}" class="btn btn_primary mt-auto">View History</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
