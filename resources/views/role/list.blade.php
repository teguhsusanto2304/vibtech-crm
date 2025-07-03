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
            <a href="{{ route('v1.roles.create')}}" class="btn btn-primary">Add Role</a>
            <!-- Department Filter Box -->



        </div>
        <div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-roles-tab" data-bs-toggle="tab" data-bs-target="#active-roles" type="button" role="tab" aria-controls="active-roles" aria-selected="true">
                    Active Roles
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inactive-roles-tab" data-bs-toggle="tab" data-bs-target="#inactive-roles" type="button" role="tab" aria-controls="inactive-roles" aria-selected="false">
                    Inactive Roles
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            {{-- Active Roles Tab Pane --}}
            <div class="tab-pane fade show active" id="active-roles" role="tabpanel" aria-labelledby="active-roles-tab">
                <table class="table table-bordered table-striped nowrap w-100" id="active_roles_datatable">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Guard</th>
                            <th width="30px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DataTables will load data here --}}
                    </tbody>
                </table>
            </div>

            {{-- Inactive Roles Tab Pane --}}
            <div class="tab-pane fade" id="inactive-roles" role="tabpanel" aria-labelledby="inactive-roles-tab">
                <table class="table table-bordered table-striped nowrap w-100" id="inactive_roles_datatable">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Guard</th>
                            <th width="30px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DataTables will load data here --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

   <script type="text/javascript">
    $(document).ready(function () {
        // Initialize the Active Roles DataTable
        var activeRolesTable = $('#active_roles_datatable').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: "{{ route('v1.roles.data') }}",
                data: function (d) {
                    d.status = 1; // Pass a 'status' parameter to your backend
                }
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'guard_name', name: 'guard_name'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        // Initialize the Inactive Roles DataTable
        // It's good practice to only load the data when the tab is shown for the first time
        // to avoid loading unnecessary data on page load.
        var inactiveRolesTable = $('#inactive_roles_datatable').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: "{{ route('v1.roles.data') }}",
                data: function (d) {
                    d.status = 2; // Pass 'inactive' status
                }
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'guard_name', name: 'guard_name'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            // Defer loading until the tab is active
            deferLoading: true, // Prevents initial AJAX call
            // Set up a flag to check if data has been loaded for this table
            // This is crucial to prevent multiple loads
            initComplete: function(settings, json) {
                // Mark as loaded after the first successful draw
                $('#inactive_roles_datatable').data('datatable-loaded', true);
            }
        });

        // Handle tab clicks to load inactive roles data only when its tab is shown
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var targetTabPaneId = $(e.target).attr('data-bs-target'); // e.g., "#inactive-roles"
            var targetTableId = $(targetTabPaneId).find('table').attr('id'); // e.g., "inactive_roles_datatable"
            if (targetTabPaneId === '#inactive-roles') {
                $('#inactive_roles_datatable').DataTable().ajax.reload(); // Load data for the inactive roles table
            }
        });

    });
</script>

@endsection
