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
                    @can('create-submit-claim')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Submit New Claim</h5>
                                
                                    <a href="{{ route('v1.submit-claim.create') }}" class="btn btn_primary mt-auto">
                                        Create</a>
                                
                            </div>
                        </div>
                    </div>                    
                    @endcan
                    @can('view-submit-claim')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Your Submitted Claims</h5>
                                <a href="{{ route('v1.submit-claim.list')}}" class="btn btn_primary mt-auto">View</a>
                            </div>
                        </div>
                    </div>
                   @endcan
                   @can('view-all-submit-claim')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">All Submitted Claims</h5>
                                <a href="{{ route('v1.submit-claim.all')}}" class="btn btn_primary mt-auto">View</a>
                            </div>
                        </div>
                    </div>
                   @endcan
                </div>
            </div>
        </div>
@endsection
