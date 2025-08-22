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
                <div class="table-responsive">
                    <table class="table table-bordered table-striped nowrap w-100" id="meeting-minutes-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Topic</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Attendees</th>
                                <th>Saved By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate this tbody via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#meeting-minutes-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true, // Enable horizontal scrolling if needed
                responsive: false, // Disable responsive mode if scrollX is used
                ajax: {
                    url: '{{ route("v1.meeting-minutes.list.data") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'meeting_topic', name: 'topic' },
                    { data: 'meeting_date_formatted', name: 'meeting_date' },
                    { data: 'meeting_time_range', name: 'start_time', orderable: false, searchable: false }, // Sort by start_time, but display range
                    { data: 'attendees_list', name: 'attendees_list', orderable: false, searchable: false }, // Not sortable/searchable by DB directly
                    { data: 'saved_by_user', name: 'savedBy.name' }, // Use dot notation for relationship column
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[2, 'desc'], [3, 'desc']], // Default order by date then time (descending)
                columnDefs: [
                    {
                        targets: [0, 4, 6], // Center No., Attendees, Actions columns
                        className: 'dt-body-center dt-head-center'
                    }
                ]
            });
        });
    </script>
@endsection