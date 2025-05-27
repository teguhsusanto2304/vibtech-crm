@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- custom-icon Breadcrumb-->
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

        <h3>{{ $title }}</h3>
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>

    <!-- Card -->
    <div class="card">
        <div class="card-header text-white d-flex flex-wrap justify-content-between align-items-center">
            <!-- Department Filter -->
            <div class="mb-2 mb-md-0">
                <label for="departmentFilter" style="color: black">Department:</label>
                        <select id="departmentFilter" class="form-control">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
            </div>
            <div class="mb-2 mb-md-0">
                <!-- Date Range Picker CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- Date Range Picker JS -->
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
                <label for="createdDateFilter" style="color: black">Created Date:</label>
                        <input type="text" id="createdDateFilter" class="form-control">
            </div>
            <div class="mb-2 mb-md-0">
                <label for="personFilter" style="color: black">Person:</label>
                        <select id="personFilter" class="form-control">
                            <option value="">All Persons</option>
                            @foreach($persons as $person)
                                <option value="{{ $person->id }}">{{ $person->name }}</option>
                            @endforeach
                        </select>
            </div>
            <!-- Add User Button -->
            <div class="mb-2 mb-md-0">
                <label for="personFilter" style="color: black">Status:</label>
                <select id="statusFilter" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="1">Accepted</option>
                    <option value="2">Rejected</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="job_datatable">
                    <thead>
                        <tr>
                            <th>Job Record ID</th>
                            <th>Job Created Date</th>
                            <th>Department</th>
                            <th>Job Created By</th>
                            <th>Type of Job</th>
                            <th>Business Name</th>
                            <th>Date of Job</th>
                            <th style="background-color: #f7eeee">Status</th>
                            <th style="background-color: #f7eeee">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!-- DataTable Script -->
        <script type="text/javascript">
            $(document).ready(function () {
                $('#createdDateFilter').daterangepicker({
        autoUpdateInput: false, // Prevents automatic date filling
        locale: {
            cancelLabel: 'Clear' // Adds a clear button
        }
    });

    $('#createdDateFilter').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));

        // Fetch filtered data from backend
        //fetchFilteredData(picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
    });

    $('#createdDateFilter').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val(''); // Clear input when cancel is clicked
    });

    $('input[name="createdDateFilter"]').daterangepicker();

                var table = $('#job_datatable').DataTable({
                    scrollX: true, // Enable horizontal scrolling
                    responsive: false, // Disable responsive mode to ensure scrolling works
                    fixedColumns: {
            leftColumns: 0, // Number of columns to fix on the left
            rightColumns: 2 },
                    ajax: "{{ route('v1.job-assignment-form.history.data') }}",
                    columns: [
                        { data: 'job_record_id', name: 'job_record_id' },
                        { data: 'created_date', name: 'created_at' },
                        { data: 'department', name: 'departments.id' },
                        { data: 'user_name', name: 'user_id' },
                        { data: 'job_type_name', name: 'job_type_name' },
                        { data: 'business_name', name: 'business_name' },
                        { data: 'date_range', name: 'date_range' },
                        { data: 'status', name: 'status' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'start_at', name: 'job_status', visible: false },

                    ],
                    columnDefs: [
                        {
                            targets: 'action', // Target the 'action' column by name
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).css('background-color', '#f7eeee'); // Set the background color
                            }
                        }
                    ]
                });
                $('#departmentFilter').on('change', function () {
                    table.column(2).search($(this).val()).draw();
                });
                $('#personFilter').on('change', function () {
                    table.column(3).search($(this).val()).draw();
                });

                $('#statusFilter').on('change', function () {
                    table.column(9).search($(this).val()).draw();
                });

                $('#createdDateFilter').on('apply.daterangepicker', function (ev, picker) {
        let startDate = picker.startDate.format('YYYY-MM-DD');
        let endDate = picker.endDate.format('YYYY-MM-DD');

        $(this).val(startDate + ' to ' + endDate);

        // Custom client-side filter
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            let createdDate = data[1]; // Column index of created_date
            if (!createdDate) return false;

            return createdDate >= startDate && createdDate <= endDate;
        });

        table.draw();
    });


            });




        </script>

@endsection
