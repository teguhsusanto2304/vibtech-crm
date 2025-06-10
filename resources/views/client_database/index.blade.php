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
                    <div class="col-12 col-md-4 " style="display: none">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Customize</h5>
                                <a href="{{ route('v1.job-assignment-form.list') }}" class="btn btn_primary mt-auto">Customize Form</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4" style='display:none;'>
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">View</h5>
                                <a href="{{ route('v1.client-database.list')}}" class="btn btn_primary mt-auto">View Client Database &nbsp; @if($clientDataNotifications > 0)  <span class="badge bg-danger">{{ $clientDataNotifications }}</span>  @endif</a>
                            </div>
                        </div>
                    </div>
                    @can('view-client-database')
                    @if($viewClientDatabase==true)
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Your Personal Database</h5>
                                <a href="{{ route('v1.client-database.my-list')}}" class="btn btn_primary mt-auto">View Your Client's Database &nbsp; @if($clientDataNotifications > 0)  <span class="badge bg-danger">{{ $clientDataNotifications }}</span>  @endif</a>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endcan
                    @can('view-vibtech-database')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Vibtech Genesis Database</h5>
                                <a href="{{ route('v1.client-database.list')}}" class="btn btn_primary mt-auto">View Main Database &nbsp; @if($clientDataNotifications > 0)  <span class="badge bg-danger">{{ $clientDataNotifications }}</span>  @endif</a>
                            </div>
                        </div>
                    </div>
                    @endcan
                    @can('view-edit-request')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Manage Edit Request</h5>
                                <a href="{{ route('v1.client-database.request-list')}}" class="btn btn_primary mt-auto">View Request &nbsp; @if($requestNotifications > 0)  <span class="badge bg-danger">{{ $requestNotifications }}</span>  @endif</a>
                            </div>
                        </div>
                    </div>
                    @endcan
                     @can('view-salesperson-assignment')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Manage Salesperson Assignment</h5>
                                <a href="{{ route('v1.client-database.assignment-salesperson.list')}}" class="btn btn_primary mt-auto">View Request &nbsp; @if($salesPersonNotifications > 0)  <span class="badge bg-danger">{{ $salesPersonNotifications }}</span>  @endif </a>
                            </div>
                        </div>
                    </div>
                    @endcan
                    @can('view-client-recycle')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Recycle Bin</h5>
                                <a href="{{ route('v1.client-database.recycle-bin.list')}}" class="btn btn_primary mt-auto">View Request  &nbsp; @if($recycleBinNotification > 0)  <span class="badge bg-danger">{{ $recycleBinNotification }}</span>  @endif </a>
                            </div>
                        </div>
                    </div>
                    @endcan
                    @can('view-csv-pdf-download-request')
                    <div class="col-12 col-md-4">
                        <div class="card text-center">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">CSV/PDF Download Request</h5>
                                <a href="{{ route('v1.client-database.download-request.list')}}" class="btn btn_primary mt-auto">View Request  &nbsp; @if($PDFCSVDownloadRequestNotification > 0)  <span class="badge bg-danger">{{ $PDFCSVDownloadRequestNotification }}</span>  @endif </a>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
@endsection
