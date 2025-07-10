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
            <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
                <div></div>
               
            </div>
            <div class="card-body" style="overflow-x: auto;">

                            <table class="table table-bordered table-striped nowrap w-100" id="submit-claim-table">
                                <thead>
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>Claim Date</th>
                                        <th>Staff Name</th>
                                        <th>Status</th>
                                        <th>Items Count</th>
                                        <th>Items Summary</th> <!-- ← 8th column -->
                                        <th>Actions</th>       <!-- ← 9th column -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables will populate this tbody via AJAX -->
                                </tbody>
                            </table>
                        
            </div>
        
        <script>
            
            $(function () {
                $('#submit-claim-table').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    responsive: false, 
                    ajax: {
                        url: '{{ route("v1.submit-claim.list.data") }}',
                        type: 'GET',
                        @if(request()->routeIs('v1.submit-claim.all'))
                        data: function (d) {
                            d.status_filter =2;
                        }
                        @endif
                    },
                columns: [
                    { data: 'serial_number', name: 'serial_number' },
                    { data: 'claim_date', name: 'claim_date' },
                    { data: 'staff', name: 'staff' },
                    { data: 'claim_status', name: 'claim_status' },
                    { data: 'submit_claim_item_count', name: 'submit_claim_item_count', orderable: false, searchable: false },
                    { data: 'total_amount_currency', name: 'total_amount_currency' }, // ← Add this
                    { data: 'action', name: 'action', orderable: false, searchable: false }
],
                    order: [[ 0, 'asc' ]]
                });

                
            });
            
        </script>
@endsection
