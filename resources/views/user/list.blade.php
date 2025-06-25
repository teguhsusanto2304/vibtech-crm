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
        @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
            <!-- Department Filter -->
            <div class="mb-2 mb-md-0">
                <select class="form-control" id="departmentFilter" style="min-width: 180px;">
                    <option value="">All Departments</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Sales">Sales</option>
                    <option value="Operations">Operations</option>
                    <option value="IT Network">IT Network</option>
                    <option value="System Projects">System Projects</option>
                </select>
            </div>
            <!-- Add User Button -->
            <a href="{{ route('v1.users.create') }}" class="btn btn-primary">Add User</a>
        </div>


        <!-- Table -->
        <div class="card-body">
            <div class="table-responsive">
                <!-- Nav tabs for Active/Inactive users -->
                <ul class="nav nav-tabs mb-3" id="userStatusTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="active-users-tab" data-bs-toggle="tab" data-bs-target="#active-users-pane" type="button" role="tab" aria-controls="active-users-pane" aria-selected="true" data-status="1">Active Staffs</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="inactive-users-tab" data-bs-toggle="tab" data-bs-target="#inactive-users-pane" type="button" role="tab" aria-controls="inactive-users-pane" aria-selected="false" data-status="0">Inactive Staffs</button>
                    </li>
                </ul>

                <!-- Only one table shared for both tabs -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped nowrap w-100" id="user_datatable">
                        <thead>
                            <tr>
                                <th>Picture</th>
                                <th>Department</th>
                                <th>Name</th>
                                <th>Nick Name</th>
                                <th>Position</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate this tbody -->
                        </tbody>
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

        <!-- DataTable Script -->
        <script type="text/javascript">
            $(document).ready(function () {
                var table = $('#user_datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('v1.users.data') }}",
                    columns: [
                        {data: 'path_image', name: 'path_image', orderable: false, searchable: false },
                        {data: 'dept', name: 'dept'},
                        {data: 'name', name: 'name'},
                        {data: 'nick_name', name: 'nick_name'},
                        {data: 'position', name: 'position'},
                        {data: 'email', name: 'email'},
                        {data: 'user_status', name: 'user_status'},
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    ],
                    scrollX: true, // Enable horizontal scrolling
                    responsive: false, // Disable responsive mode to ensure scrolling works
                    fixedColumns: {
            leftColumns: 0, // Number of columns to fix on the left
            rightColumns: 1  // Fix the last column (action column) on the right
        }
                });
                table.column(6).search(1).draw();

                // --- Bootstrap Tab Event Listener ---
                $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                    const selectedStatus = $(e.target).data('status'); // Get 'active' or 'inactive' from data-status attribute
                    // Update DataTable's AJAX URL dynamically
                    // DataTables redraws the table and re-sends AJAX request with the new data parameter.
                    table.column(6).search(selectedStatus).draw();
                    //$('#user_datatable').DataTable().ajax.reload(null, false); // Reload data, don't reset pagination
                });

                // --- (Optional) Handle the highlighting of rows with checkboxes ---
                // Ensure your individual checkbox and "check-all" logic is set up as discussed before.
                // This part should already be generic and work with the new data.
                $(document).on('change', '#user_datatable .row-checkbox', function() {
                    if ($(this).is(':checked')) {
                        $(this).closest('tr').addClass('selected-row');
                    } else {
                        $(this).closest('tr').removeClass('selected-row');
                    }
                });
                // And if you have a "check-all" checkbox:
                $(document).on('change', '#check-all-rows', function() {
                    const isChecked = $(this).is(':checked');
                    $('#user_datatable .row-checkbox').prop('checked', isChecked).trigger('change');
                });

                // Filter by department
                $('#departmentFilter').on('change', function () {
                    const selectedDepartment = $(this).val();
                    table.column(1).search(selectedDepartment).draw();
                });
            });
            $(document).ready(function () {
    let userId, actionType;

    // Show confirmation modal when clicking the action button
    $(document).on("click", ".confirm-action", function () {
        userId = $(this).data("id");
        actionType = $(this).data("action");

        // Set action text in modal
        $("#actionText").text(actionType === "deactivate" ? "deactivate" : "activate");

        // Show modal
        $("#confirmModal").modal("show");
    });

    // Handle confirm button click
    $("#confirmBtn").on("click", function () {
        $.ajax({
            url: "{{ route('v1.users.toggle-status') }}", // Your Laravel route
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
