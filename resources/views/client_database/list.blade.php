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
    </div>

    <!-- DataTable Dependencies -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>

    <!-- Card -->
    <div class="card">
        <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
            <div>  </div>
            @can('create-client-database')
            <a href="{{ route('v1.client-database.create')}}" class="btn btn-primary">Create New Client</a>
            @endcan
            <!-- Department Filter Box -->
        </div>
        <div class="card-body" style="overflow-x: auto;">
            <div class="table-responsive">
                <table class="table table-bordered table-striped nowrap w-100" id="clients-table" >
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Position</th>
                        <th>Email</th>
                        <th>Office Number</th>
                        <th>Mobile Number</th>
                        <th>Job Title</th>
                        <th>Industry</th>
                        <th>Country</th>
                        <th>Sales Person</th>
                        <th>Image</th>
                        <th>Quotation</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
            </div>
            </div>
        </div>
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to <span id="actionText"></span> this user?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
<!-- Include jQuery + DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
<script>
$(function () {
    $('#clients-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true, // Enable horizontal scrolling
        responsive: false,
        ajax: '{{ route('v1.client-database.data') }}',
        columns: [
            { data: 'name' },
            { data: 'company' },
            { data: 'position' },
            { data: 'email' },
            { data: 'office_number' },
            { data: 'mobile_number' },
            { data: 'job_title' },
            { data: 'industry', name: 'industryCategory.name' },
            { data: 'country', name: 'country.name' },
            { data: 'sales_person', name: 'salesPerson.name' },
            {
                data: 'image_path',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return '<img src="' + data + '" alt="User Image" width="80" height="80" >'; // Data diasumsikan sudah berupa string HTML <img>
                    }
                    return data; // Untuk sorting, filtering, dll. tetap gunakan data asli
                }
            },
            { data: 'quotation', name: 'quotation' },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    $(document).on("click", ".confirm-action", function () {
        userId = $(this).data("id");
        actionType = $(this).data("action");

        // Set action text in modal
        $("#actionText").text(actionType === "deactivate" ? "deactivate" : "activate");

        // Show modal
        $("#confirmModal").modal("show");
    });
$("#confirmBtn").on("click", function () {
        $.ajax({
            url: "{{ route('v1.client-database.toggle-status') }}", // Your Laravel route
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: userId,
                action: actionType
            },
            success: function (response) {
                $("#confirmModal").modal("hide"); // Close modal
                if (response.success) {
                    //alert("User " + actionType + "d successfully!");
                    location.reload(); // Refresh DataTables
                } else {
                    alert("Failed to " + actionType + " user.");
                }
            },
            error: function () {
                alert("An error occurred.");
            }
        });
    });
});
</script>

@endsection

@push('scripts')

@endpush
