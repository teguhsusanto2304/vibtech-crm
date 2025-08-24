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
                    @can('create-inventory-management')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">New Inventory</h5>
                                
                                    <a href="{{ route('v1.inventory-management.create') }}" class="btn btn_primary mt-auto">
                                        Create</a>
                                
                            </div>
                        </div>
                    </div>                    
                    @endcan
                    @can('view-inventory-management')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Product Inventory</h5>
                                <a href="{{ route('v1.inventory-management.list')}}" class="btn btn_primary mt-auto">View</a>
                            </div>
                        </div>
                    </div>
                   @endcan

                   @can('view-receiving-order')
                    <div class="col-12 col-md-4" style="display: none;">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Receiving Order</h5>
                                <a href="{{ route('v1.receiving-order.list')}}" class="btn btn_primary mt-auto">View</a>
                            </div>
                        </div>
                    </div>
                   @endcan
                   
                </div>
            </div>
        </div>
@endsection
