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

            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('errors'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('errors') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div id="msg"></div>
        <!-- DataTable Dependencies -->


        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

        <!-- Card -->
        <div class="card">
    <div class="card-header bg-default text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"></h5>
            <a href="{{ route('v1.submit-claim.list') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Claims List
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="row gy-4">
            <!-- Left Column -->
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Serial Number:</strong>
                    <div>{{ $claim->serial_number }}</div>
                </div>
                <div class="mb-3">
                    <strong>Claim Date:</strong>
                    <div>{{ $claim->claim_date->format('d M Y') }}</div>
                </div>
                <div class="mb-3">
                    <strong>Staff Name:</strong>
                    <div>{{ $claim->staff->name ?? 'N/A' }}</div>
                </div>
                {{-- Uncomment if needed
                <div class="mb-3">
                    <strong>Claim Type:</strong>
                    <div>{{ $claim->claimType->name ?? 'N/A' }}</div>
                </div>
                --}}
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <div class="mb-3">
                    <strong>Total Amount:</strong>
                    <div>{{ $claim->currency }} {{ number_format($claim->total_amount, 2) }}</div>
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    <div>
                        @if ($claim->data_status == 1)
                            <span class="badge bg-warning">Ongoing</span>
                        @elseif ($claim->data_status == 2)
                            <span class="badge bg-success">Completed</span>
                        @else
                            <span class="badge bg-secondary">Unknown</span>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Created At:</strong>
                    <div>{{ $claim->created_at->format('d M Y H:i:s') }}</div>
                </div>
                <div class="mb-3">
                    <strong>Last Updated:</strong>
                    <div>{{ $claim->updated_at->format('d M Y H:i:s') }}</div>
                </div>
            </div>
        </div>
    </div>



        
        

    </div>

            </div>
        </div>

                            
@endsection
