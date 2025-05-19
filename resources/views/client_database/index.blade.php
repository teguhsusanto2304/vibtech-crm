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
                                <h5 class="card-title">Create/Upload</h5>
                                @can('create-client-database')
                                    <a href="{{ route('v1.client-database.create') }}" class="btn btn_primary mt-auto">
                                        Create/Upload New</a>
                                @else
                                    <a href="#" class="btn btn_primary mt-auto">You Can't Create New</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Customize</h5>
                                <a href="{{ route('v1.job-assignment-form.list') }}" class="btn btn_primary mt-auto">Customize Form</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">View</h5>
                                <a href="{{ route('v1.client-database.list')}}" class="btn btn_primary mt-auto">View Client Database</a>
                            </div>
                        </div>
                    </div>
                    @can('edit-client-database')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Manage Edit/Delete Request</h5>
                                <a href="{{ route('v1.client-database.request-list')}}" class="btn btn_primary mt-auto">View Request</a>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
@endsection
