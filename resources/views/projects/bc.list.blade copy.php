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
                    <table class="table table-bordered table-striped nowrap w-100" id="projects-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Project Manager</th>
                                <th>Members</th>
                                <th>Progress</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>                    
                </div>
            </div>
        </div>
        <script>
            $(function () {
                let table = $('#projects-table').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true, // Enable horizontal scrolling
                    responsive: false,
                    ajax: {
                        url: '{{ route('v1.project-management.data') }}'
                    },

                    columns: [
                        { data: 'name' },
                        { data: 'description' },
                        { data: 'start_at' },
                        { data: 'end_at' }, 
                        { data: 'project_manager',name:'projectManager.name' },
                        { data: 'total_project_members' },   
                        {
                    data: 'progress_percentage', // Use the data field from your backend
                    name: 'progress', // Name for server-side processing/ordering
                    orderable: false, // Progress bars are usually not orderable
                    searchable: false, // Progress bars are usually not searchable
                    // THIS IS THE KEY PART: The render function
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            // Ensure data is a number and within 0-100 range
                            const percentage = Math.max(0, Math.min(100, parseInt(data) || 0));
                            let progressBarClass = 'bg-primary'; // Default color

                            // Optional: Change color based on progress
                            if (percentage < 30) {
                                progressBarClass = 'bg-danger'; // Red for low progress
                            } else if (percentage < 70) {
                                progressBarClass = 'bg-warning'; // Yellow for medium progress
                            } else {
                                progressBarClass = 'bg-success'; // Green for high progress
                            }

                            return `
                                <div class="progress" style="height: 20px; border-radius: 5px;">
                                    <div class="progress-bar ${progressBarClass} progress-bar-striped progress-bar-animated"
                                         role="progressbar"
                                         style="width: ${percentage}%;"
                                         aria-valuenow="${percentage}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        <span class="text-white small fw-bold">${percentage}%</span>
                                    </div>
                                </div>
                            `;
                        }
                        return data; // Return raw data for other types (e.g., 'sort', 'type')
                    }
                },                   
                        { data: 'action', name: 'action', orderable: false, searchable: false },

                    ]
                });
            });
        </script>
@endsection
