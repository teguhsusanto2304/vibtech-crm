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
                
                <ul class="nav nav-tabs" id="projectTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="others-tab" data-bs-toggle="tab" href="#others" role="tab">Ongoing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="my-tab" data-bs-toggle="tab" href="#my" role="tab">Complete</a>
                    </li>
                </ul>

                <div class="tab-content mt-3">
                    <!-- Tab 1: Projects with other managers -->
                    <div class="tab-pane fade show active" id="others" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped nowrap w-100" id="others-projects-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Project Manager</th>
                                        <th>Members</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Projects where user is PM -->
                    <div class="tab-pane fade" id="my" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped nowrap w-100" id="my-projects-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Project Manager</th>
                                        <th>Members</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <!-- Generic Confirmation Modal -->
            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="confirmationModalBody">
                            Are you sure you want to proceed with this action?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmActionBtn">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
            $(document).ready(function() {
                // ... (Your other existing JavaScript code) ...

                let formToSubmit = null; // Variable to hold the form that needs to be submitted after confirmation

                // --- Event listener for when a delete button is clicked ---
                $(document).on('click', '.delete-project-btn', function() {
                    const projectId = $(this).data('project-id');
                    const confirmMessage = $(this).data('confirm-message') || 'Are you sure you want to delete this item?';

                    // Set the dynamic message in the modal body
                    $('#confirmationModalBody').text(confirmMessage);

                    // Prepare the form that will be submitted
                    // We'll dynamically create a form for submission
                    formToSubmit = $('<form>', {
                        'action': `/v1/project-management/${projectId}/destroy`, // Your delete route
                        'method': 'POST',
                        'style': 'display:none;'
                    });
                    
                    formToSubmit.append($('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'DELETE' // Spoof DELETE method
                    }));
                    
                    formToSubmit.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': $('meta[name="csrf-token"]').attr('content') // CSRF token
                    }));

                    // Append the form to the body so it's part of the DOM
                    $('body').append(formToSubmit);
                });

                // --- Event listener for when the "Confirm" button in the modal is clicked ---
                $('#confirmActionBtn').on('click', function() {
                    if (formToSubmit) {
                        formToSubmit.submit(); // Submit the prepared form
                    }
                    $('#confirmationModal').modal('hide'); // Hide the modal
                });

                // --- Clean up the dynamically created form when modal is hidden ---
                $('#confirmationModal').on('hidden.bs.modal', function () {
                    if (formToSubmit) {
                        formToSubmit.remove(); // Remove the form from the DOM
                        formToSubmit = null; // Clear the reference
                    }
                });
            });
            </script>
        <script>
            function getProjectColumns() {
                return [
                    { data: 'name' },
                    { data: 'description' },
                    { data: 'start_at' },
                    { data: 'end_at' }, 
                    { data: 'project_manager', name: 'projectManager.name' },
                    { data: 'total_project_members' },   
                    {
                        data: 'progress_percentage',
                        name: 'progress',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const percentage = Math.max(0, Math.min(100, parseInt(data) || 0));
                            let progressBarClass = 'bg-primary';

                            if (percentage < 30) progressBarClass = 'bg-danger';
                            else if (percentage < 70) progressBarClass = 'bg-warning';
                            else progressBarClass = 'bg-success';

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
                    },
                    { data: 'project_status', name: 'project_status', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ];
            }
            $(function () {
                $('#others-projects-table').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    responsive: false, 
                    ajax: {
                        url: '{{ route("v1.project-management.all-data") }}',
                        data: { type: 'others' }
                    },
                    columns: getProjectColumns(),
                    order: [[ 0, 'asc' ]]
                });

                $('#my-projects-table').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    responsive: false,
                    ajax: {
                        url: '{{ route("v1.project-management.all-data") }}',
                        data: { type: 'my' }
                    },
                    columns: getProjectColumns(),
                    order: [[ 0, 'asc' ]]
                });
            });
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                // Check if the shown tab contains your DataTable
                if ($(e.target).attr('href') === '#my' || $(e.target).attr('href') === '#others') {
                    // Adjust column widths and redraw the table
                    $('#my-projects-table').DataTable().columns.adjust().draw();
                    $('#others-projects-table').DataTable().columns.adjust().draw();
                }
            });
        </script>
@endsection
