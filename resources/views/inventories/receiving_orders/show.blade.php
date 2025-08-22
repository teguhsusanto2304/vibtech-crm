@extends('layouts.app')

@section('title', 'Detail Receiving Order')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom-icon">
            @foreach ($breadcrumb as $item)
                <li class="breadcrumb-item">
                    @if($item == 'Job Assignment Form')
                        <a href="{{ route('v1.job-assignment-form')}}">{{ $item }}</a>
                    @else
                        <a href="javascript:void(0);">{{ $item }}</a>
                    @endif
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
            @endforeach
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-12">
            <h3>{{ $title }}</h3>
        </div>
    </div>

    <!-- Alert Messages -->
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
    
    <div class="col-lg-12">
        <div class="card p-4 shadow-sm">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('v1.receiving-order.list') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
                <a href="" class="btn btn-danger invisible" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i> Download PDF
                </a>
            </div>

            <!-- Order Information Card -->
            <div class="card mb-4 border-light shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-dark">
                        <i class="fas fa-info-circle me-2 text-info"></i> Order Information
                    </h5>
                    <hr>
                    <div class="row g-3">
    <div class="col-md-6">
        <div class="row">
            <div class="col-12 mb-2">
                <div class="row">
                    <div class="col-md-6">
                         <strong>PO Number:</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">{{ $receivingOrder->po_number ?? 'N/A' }}</span>
                    </div>
                </div> 
                
            </div>
            <div class="col-12 mb-2">
                <div class="row">
                    <div class="col-md-6">
                         <strong>Supplier Name:</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">{{ $receivingOrder->supplier_name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-2">
                <div class="row">
                    <div class="col-md-6">
                         <strong>Purchase Date:</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">{{ \Carbon\Carbon::parse($receivingOrder->purchase_date)->format('d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-12 mb-2">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Received Date:</strong>
                    </div>
                    <div class="col-md-6">
                       <span class="text-muted">{{ \Carbon\Carbon::parse($receivingOrder->received_date)->format('d F Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-2">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Received By:</strong>
                    </div>
                    <div class="col-md-6">
                       <span class="text-muted">{{ $receivingOrder->createdBy->name }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-2">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Remarks:</strong>
                    </div>
                    <div class="col-md-6">
                       <span class="text-muted">{{ $receivingOrder->remarks ?? 'N/A' }}</span>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>
                </div>
            </div>

            <!-- List of Items Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-dark">
                        <i class="fas fa-list-ul me-2 text-success"></i> List of Receiving Order Items
                    </h5>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receivingOrder->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('custom-scripts')
<style>
    .card {
        border-radius: 1rem;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .btn-outline-secondary {
        border-radius: 50px;
    }
    .btn-danger {
        border-radius: 50px;
    }
    .text-primary {
        color: #0d6efd !important;
    }
    .text-info {
        color: #0dcaf0 !important;
    }
    .text-success {
        color: #198754 !important;
    }
    .list-unstyled li strong {
        display: inline-block;
        min-width: 120px;
    }
    .table thead th {
        background-color: #f8f9fa;
    }
</style>
@endsection
