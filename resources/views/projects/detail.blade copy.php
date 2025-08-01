@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
        <style>

        .task-card {
            background-color: #e8e8e8; /* Light grey background similar to screenshot */
            border-radius: 12px; /* Rounded corners */
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* Subtle shadow for depth */
            max-width: 500px; /* Max width to mimic screenshot proportions */
            width: 100%;
            position: relative; /* For avatar positioning */
        }

        .task-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #333;
        }

        .task-description {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 20px;
        }

        .task-assignee-avatar {
            width: 50px; /* Larger avatar size */
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #fff; /* White border around avatar */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); /* Avatar shadow */
            position: absolute;
            top: 20px; /* Adjust as needed */
            right: 20px; /* Adjust as needed */
            z-index: 10;
        }

        .task-actions-left {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .task-actions-left li {
            margin-bottom: 5px;
        }

        .task-actions-left a {
            color: #0d6efd; /* Bootstrap primary blue for links */
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s ease-in-out;
        }

        .task-actions-left a:hover {
            color: #0a58ca; /* Darker blue on hover */
            text-decoration: underline;
        }

        .task-status-badge {
            background-color: #ffc107; /* Orange/Yellow background for 'Ongoing' */
            color: #212529; /* Dark text for contrast */
            padding: 8px 18px;
            border-radius: 20px; /* More rounded */
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block; /* Ensures padding works */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .download-files-link {
            font-size: 0.9rem;
            color: #6c757d; /* Bootstrap secondary color */
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }
        .download-files-link:hover {
            color: #495057;
            text-decoration: underline;
        }

        .scrollbar-thumb {
            height: 100%;
            width: 50px; /* Initial width, will be calculated by JS */
            background-color: rgba(0, 0, 0, 0.5); /* Darker thumb */
            border-radius: 5px; /* Rounded corners for the thumb */
            cursor: grab; /* Cursor indicates draggable */
            position: absolute;
            left: 0;
            top: 0;
            pointer-events: auto; /* Re-enable pointer events for the thumb itself */
            transition: transform 0.05s linear; /* Smooth horizontal movement */
        }

        .scrollbar-thumb:active {
            cursor: grabbing; /* Cursor indicates currently dragging */
        }

    </style>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
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
    </div>

    <h3>{{ $title }}</h3>
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
    @if (session()->has('error_import'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> {{ session('error_import') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="card">
        <div class="card-body">
            <!-- Header Section -->

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-dark invisible">Project Details</h4>
                <div class="btn-group" role="group" aria-label="Default button group">
                    
                    <a href="{{ route('v1.project-management.list') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i> Back to Projects
                    </a>
                    @can('edit-project-management-complete')
                    @if($project->data_status==2)
                    <button type="button"
                        class="btn btn-success" disabled>Completed</button>
                    @else
                    <button type="button"
                        class="btn btn-{{ $project->work_progress_percentage==100 ? 'success' : 'danger' }} complete-project-btn" {{-- Added complete-project-btn class --}}
                        @if($project->work_progress_percentage < 100) disabled @endif
                            data-project-id="{{ $project->obfuscated_id }}" {{-- Pass the project's obfuscated ID --}}
                            data-bs-toggle="tooltip" {{-- Add tooltip --}}
                            data-bs-placement="top"
                            title="{{ $project->work_progress_percentage==100 ? 'Mark this project as complete' : 'All stages must be completed first' }}" {{-- Dynamic tooltip --}}>
                            Complete Entire Project
                    </button>
                    @endif
                    @endcan
                </div>
            </div>


            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="projectDetailsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="details-tab" data-bs-toggle="tab" href="#project-details" type="button" role="tab" aria-controls="project-details" aria-selected="true">
                        Project Details
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="phases-tab" data-bs-toggle="tab" href="#phases-files" type="button" role="tab" aria-controls="phases-files" aria-selected="false">
                        Phases
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="files-tab" data-bs-toggle="tab" href="#project-files" type="button" role="tab" aria-controls="project-files" aria-selected="false">
                        Project Documentation
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="projectDetailsTabContent">
                {{-- Tab 1: Project Details Information --}}
                <div class="tab-pane fade show active" id="project-details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="row">
                        <!-- Left Column: Project Info -->
                        <div class="col-md-7 mb-4">
                            <div class="table-responsive"> {{-- Optional: Makes table scrollable on small screens --}}
                                <table class="table table-borderless table-sm"> {{-- table-borderless for no borders, table-sm for compact --}}
                                    <tbody>
                                        <tr>
                                            <td class="detail-label" style="width: 40%;">Project Name:</td> {{-- Adjust width for label column --}}
                                            <td class="detail-value text-muted">{{ $project->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Project Description:</td>
                                            <td class="detail-value text-muted">{{ $project->description }}</td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Start Date:</td>
                                            <td class="detail-value text-muted">{{ $project->start_at->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">End Date:</td>
                                            <td class="detail-value text-muted">{{ $project->end_at->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Remaining Days:</td>
                                            <td class="detail-value text-muted">
                                                <div id="dayProgress"></div>
                                            </td>
                                        <tr>
                                        <tr>
                                            <td class="detail-label">Progress:</td>
                                            <td class="detail-value text-muted">
                                                <div id="workProgress"></div>
                                            </td>
                                        <tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Right Column: Project Manager & Members -->
                        <div class="col-md-5 mb-4">
                            <!-- Project Manager -->
                            <div class="d-flex align-items-center mb-3">
                                <strong class="me-3">Project Manager:</strong>
                                <img src="{{ $project->projectManager->avatar_url }}" alt="{{ $project->projectManager->name }}'s avatar" {{-- Improved alt text --}}
                                    class="rounded-circle member-avatar-wrapper"
                                    data-bs-placement="top"
                                    width="40" height="40"
                                    data-member-id="{{ $project->projectManager->id }}"
                                    data-member-name="{{ $project->projectManager->name }}"
                                    data-member-email="{{ $project->projectManager->email }}" {{-- Add other details you want to show --}}
                                    data-avatar-url="{{ $project->projectManager->avatar_url }}"
                                    data-member-position="{{ $project->projectManager->position }}"
                                    data-project-id="{{ $project->obfuscated_id }}"
                                    title="{{ $project->projectManager->name }}" style="cursor: pointer;">
                            </div>

                            <!-- Project Members -->
                            <div>
                                <p class="mb-2"><strong>Project Members (Total: {{ $project->projectMembers()->count() }}) </strong></p>
                                <div class="d-flex align-items-center gap-2">
                                    @foreach($project->showProjectMembers as $projectMember)
                                    <img src="{{ $projectMember->member->avatar_url }}"
                                        alt="{{ $projectMember->member->name }}'s avatar" {{-- Improved alt text --}}
                                        class="rounded-circle member-avatar-wrapper"
                                        data-bs-placement="top"
                                        width="40" height="40"
                                        data-member-id="{{ $projectMember->member->id }}"
                                        data-member-name="{{ $projectMember->member->name }}"
                                        data-member-email="{{ $projectMember->member->email }}" {{-- Add other details you want to show --}}
                                        data-avatar-url="{{ $projectMember->member->avatar_url }}"
                                        data-member-position="{{ $projectMember->member->position }}"
                                        data-project-id="{{ $project->obfuscated_id }}"
                                        title="{{ $projectMember->member->name }}" style="cursor: pointer;">
                                    @endforeach
                                    @if($project->project_manager_id == auth()->user()->id)
                                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center me-2"
                                        style="width: 40px; height: 40px; font-size: 1.5em; border: 2px dashed #0d6efd;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addMemberModal" {{-- Target the modal for adding members --}}
                                        title="Add New Member">
                                        <i class="bi bi-plus-circle-fill"></i> {{-- Bootstrap Plus Circle Icon --}}
                                    </button>
                                    @endif
                                </div>
                                {{-- START: Search Input for Project Members --}}
                                <div class="mt-5"> {{-- Add margin-top for spacing --}}
                                    <label for="memberSearch" class="form-label" >Search Task Content</label> {{-- Visually hidden label for accessibility --}}
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="taskSearch" placeholder="Search tasks content...">
                                        <button class="btn btn-outline-secondary" type="button" id="taskSearchClear"><i class="bi bi-eraser"></i></button>
                                    </div>
                                </div>
                                {{-- END: Search Input for Project Members --}}
                            </div>
                        </div>
                    </div>
                </div> {{-- End tab-pane project-details --}}

                {{-- Tab 2: Project Files --}}
                <div class="tab-pane fade" id="project-files" role="tabpanel" aria-labelledby="files-tab">
                    @if($project->files->count() > 0)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="uploaded_by_filter" class="form-label">Uploaded By:</label>
                            <select class="form-select" id="uploaded_by_filter">
                                <option value="">All Uploaders</option>
                                @foreach($creators as $creator)
                                <option value="{{ $creator->id }}">{{  $creator->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="section_filter" class="form-label">Section:</label>
                            <select class="form-select" id="section_filter">
                                <option value="">All Sections</option>
                                <option value="project">Project</option>
                                <option value="task">Task</option>
                            </select>
                        </div>
                         <div class="col-md-2">
                           
                            <button class="btn bg-warning text-white mt-6" id="fileFilterClearButton">Clear</button>
                        </div>
                    </div>
                    <style>
                        .dt-body-nowrap {
                            white-space: nowrap;
                        }
                    </style>
                    <table class="table table-bordered table-striped nowrap w-100" id="project_files_datatable">
                        <thead>
                            <tr>
                                <th width="30%">File Name</th>
                                <th width="30%">Uploaded By</th>
                                <th width="30%">Section</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be loaded by DataTables --}}
                        </tbody>
                    </table>
       
                        <script>
                            
                    $(document).ready(function() {

                        $('#fileFilterClearButton').on('click',function() {
                            $('#uploaded_by_filter').val('');
                            $('#section_filter').val('');
                            $('#project_files_datatable').DataTable().ajax.reload();
                        });

                        const currentProjectId = "{{ $project->id ?? '' }}"; // Or $project->obfuscated_id if your backend uses that

                        if (!currentProjectId) {
                            console.error('Project ID is not available for loading files.');
                            return;
                        }
                        //$('#project_files_datatable').DataTable().columns.adjust().draw();

                        var projectFilesTable = $('#project_files_datatable').DataTable({
                            processing: true,
                            serverSide: true,
                            scrollX: true,
                            ajax: {
                                url: "{{ route('v1.project-management.project-files.data') }}",
                                data: function (d) {
                                    d.project_id = currentProjectId;
                                    d.uploaded_by_user_id = $('#uploaded_by_filter').val();
                                    d.section = $('#section_filter').val();
                                }
                            },
                            columns: [
                                {data: 'file_name_link', name: 'file_name', orderable: true, searchable: true, width: '30%'},
                                {data: 'uploaded_by', name: 'uploadedBy.name', orderable: true, searchable: true, width: '30%'}, // 'uploadedBy.name' for relationship
                                {data: 'associated_task',name: 'associated_task' ,orderable: true, width: '30%'}, // 'task.name' for relationship
                                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'dt-body-nowrap', width: '10%'},
                            ],
                            order: [[1, 'asc']] // Default sort by 'Uploaded At' descending
                        });
                        
                        $('#uploaded_by_filter,#section_filter').on('change', function () {
                            $('#project_files_datatable').DataTable().ajax.reload();
                        });

                        


                        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                            
                                $('#project_files_datatable').DataTable().columns.adjust().draw();

                        });

                        

                        // --- JavaScript for Delete Button (from previous discussion) ---
                        $(document).on('click', '.delete-project-file-btn', function() {
                            const fileId = $(this).data('file-id');
                            const fileName = $(this).data('file-name');

                            if (confirm(`Are you sure you want to delete the file "${fileName}"?`)) {
                                $.ajax({
                                    url: '/v1/project-management/' + fileId + '/file-destroy', // Use your API route for deletion
                                    type: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        alert(response.message);
                                        projectFilesTable.ajax.reload(); // Reload the DataTable after deletion
                                    },
                                    error: function(xhr) {
                                        alert('Error deleting file: ' + (xhr.responseJSON.message || 'Unknown error'));
                                        console.error('Error:', xhr);
                                    }
                                });
                            }
                        });
                    });
                </script>
                @endif

                </div> {{-- End tab-pane project-files --}}
                {{-- Tab 3: Phases --}}
                <div class="tab-pane fade" id="phases-files" role="tabpanel" aria-labelledby="phases-tab" style="max-height: 400px; overflow-y: auto;">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-3">Project Phases</h4>
                            <div class="list-group"> {{-- Using Bootstrap's list-group for a clean, structured list of items --}}
                                @forelse($project->phases as $index => $phase)
                                    <div class="list-group-item list-group-item-action py-3"> {{-- Each phase as a list item --}}
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <span class="badge bg-secondary rounded-pill me-3">{{ $index + 1 }}</span> {{-- Serial Number Badge --}}
                                                <h5 class="mb-1 text-primary">{{ $phase->name }}</h5> {{-- Phase Name --}}
                                                <span class="badge {{ match($phase->status) {
                                                    'completed' => 'bg-success',
                                                    'in_progress' => 'bg-info',
                                                    'pending' => 'bg-warning text-dark',
                                                    'on_hold' => 'bg-secondary',
                                                    default => 'bg-primary',
                                                } }} ms-3">{{ ucfirst(str_replace('_', ' ', $phase->status)) }}</span> {{-- Status Badge --}}
                                            </div>
                                            <div class="text-end text-muted">
                                                <small class="d-block"><i class="fas fa-calendar-alt me-1"></i>
                                                    {{ $phase->start_date ? $phase->start_date->format('d M Y') : 'N/A' }}
                                                    -
                                                    {{ $phase->end_date ? $phase->end_date->format('d M Y') : 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-9">
                                                <p class="mb-1 text-muted small">{{ \Illuminate\Support\Str::limit($phase->description, 150, '...') }}</p> {{-- Truncated Description --}}
                                            </div>
                                            <div class="col-md-3 text-end">
                                                {{-- Action Buttons --}}
                                                @php
                                                    $projectId = property_exists($project, 'obfuscated_id') ? $project->obfuscated_id : $project->id;
                                                    $phaseId = property_exists($phase, 'obfuscated_id') ? $phase->obfuscated_id : $phase->id;
                                                @endphp
                                                <a href="" class="btn btn-info btn-sm me-1" title="View Phase Details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="list-group-item">
                                        <p class="text-muted mb-0">No phases defined for this project yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <hr>                
                <div class="horizontal-scroll-wrapper" style="height: 500px;"> {{-- Custom wrapper for overflow --}}
                    <div class="row flex-nowrap">
                        @foreach($kanbanStages as $index => $kanbanStage)
                            @php
                                $ProjectStageStatus = 'Active';
                                $previousProjectStage = null;
                                $isFirstStage = ($index === 0);
                                $prevStageId = null; // Initialize prevStageId to null

                                // If it's not the first stage, get the ID of the previous kanbanStage
                                if (!$isFirstStage) {
                                    $previousKanbanStage = $kanbanStages[$index - 1]; // Access the previous KanbanStage object
                                    $prevStageId = $previousKanbanStage->id; // Get its ID
                                }

                                // --- Logic for card-disabled based on previous stage completion ---
                                $isDisabledByPreviousStage = false;
                                if (!$isFirstStage) {
                                    // Find the ProjectStage for the previous kanban stage related to THIS project
                                    $previousProjectStage = $project->projectStages
                                    ->where('kanban_stage_id', $prevStageId) // Use $prevStageId here
                                    ->first();

                                    // Check if the previous ProjectStage exists and its status is 'completed'
                                    if ($previousProjectStage && $previousProjectStage->data_status === 2) {
                                        $isDisabledByPreviousStage = false;
                                    } else {
                                        // If previous stage is NOT completed (or doesn't exist), this stage is disabled
                                        $isDisabledByPreviousStage = true;
                                        $ProjectStageStatus = 'Inactive';
                                    }
                                }
                                // For the first stage, $isDisabledByPreviousStage remains false (it's always enabled)

                                // Your original logic for disabling based on kanbanStage->id
                                $isDisabledByKanbanStatus = ($kanbanStage->id > 2); // Your original logic

                                // Combine all disabling conditions
                                $cardClasses = '';
                                if ($isDisabledByPreviousStage) {
                                    if($index>1){
                                        $cardClasses = 'card-disabled';
                                        $ProjectStageStatus = 'Inactive';
                                    } else {
                                        $ProjectStageStatus = 'Active';
                                    }
                                }
                                // Get the specific ProjectStage for *this project and this kanbanStage*
                                $currentProjectStage = $project->projectStages
                                ->where('kanban_stage_id', $kanbanStage->id)
                                ->first();

                                // Determine if the "Complete Stage" button should be enabled
                                $canCompleteStage = ($project->project_manager_id == auth()->user()->id &&
                                $currentProjectStage &&
                                $currentProjectStage->tasks->count() > 0 &&
                                $currentProjectStage->data_status !== 2 // &&
                                // !$isDisabledByPreviousStage && // Cannot complete if previous stage is not done
                                // !$isDisabledByKanbanStatus
                                );
                                if($project->can_create_task==0){
                                    $createButton = 'disabled';
                                } else {
                                    $createButton = '';
                                }
                                
                                $fontClass ='primary';
                                $labelStatus ='Active';
                                $createTask = '';
                                $completeStage = '';
                                
                                if(isset($currentProjectStage->data_status)){
                                    if($currentProjectStage->data_status==2){
                                        $fontClass ='success';
                                        $labelStatus ='Completed';
                                        $createTask = 'disabled';
                                        $completeStage = 'disabled';
                                    }
                                }

                        @endphp
                            <div class="col-sm-5 mb-3 mb-sm-0 {{ $cardClasses }}" > {{-- h-100 to make cards in a row have equal height --}}
                                <div class="kanban-stage">
                                    <div class="card mb-3" style="max-width: 540px; border: 1px solid #819A91;">
                                        <div class="card-body" style="height:100px;">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="card-title">{{ $kanbanStage->name }}</h5>
                                                    <p><small class="text-{{ $fontClass }}">{{ $labelStatus }}</small></p>
                                                </div>
                                                <button
                                                    class="btn btn-sm btn-outline-warning"
                                                    data-bs-toggle="modal"              {{-- Bootstrap 5: data-bs-toggle, Bootstrap 4: data-toggle --}}
                                                    data-bs-target="#bulletinModal"     {{-- Bootstrap 5: data-bs-target, Bootstrap 4: data-target --}}
                                                    data-bulletin-stage-id="{{ $kanbanStage->id }}" {{-- Custom attribute to pass stage ID --}}
                                                    data-bulletin-project-id="{{ $project->id }}"
                                                    data-bulletin-stage-disable="{{ $completeStage }}" 
                                                    id="show-bulletin-modal-btn-{{ $kanbanStage->id }}" {{-- Unique ID for individual buttons (optional but good) --}}
                                                >
                                                    Bulletin
                                                </button>
                                            </div>           
                                        </div>
                                        <div class="card-footer">
                                            <div class="text-end">
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Kanban Actions"> {{-- No vertical class --}}
                                                {{-- Create Task Button --}}
                                                    <button type="button" class="btn btn-outline-primary btn-sm create-task-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#createTaskModal"
                                                        data-project-id="{{ $project->obfuscated_id }}" {{-- Pass obfuscated project ID --}}
                                                        data-kanban-stage-id="{{ $kanbanStage->id }}" {{-- Pass actual kanban stage ID --}}
                                                        data-kanban-stage-name="{{ $kanbanStage->name }}" {{-- Pass stage name for modal title --}}
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Add a new task for this stage"
                                                        {{ $createTask }}>Create</button>
                                                    @if($project->project_manager_id == auth()->user()->id)
                                                        @if($canCompleteStage) {{-- Use the pre-calculated $canCompleteStage variable --}}
                                                            <button type="button" class="btn btn-outline-success btn-sm mark-stage-complete-btn"
                                                                data-project-id="{{ $project->obfuscated_id }}"
                                                                data-project-stage-id="{{ $currentProjectStage->obfuscated_id }}" {{-- Pass obfuscated project stage ID --}}
                                                                data-kanban-stage-id="{{ $kanbanStage->id }}" {{-- Also useful for UI updates --}}
                                                                {{ $completeStage }}>Complete</button>
                                                        @else
                                                            {{-- Optionally show a disabled button with a tooltip explaining why --}}
                                                            <button type="button" class="btn btn-outline-secondary btn-sm mark-stage-complete-btn"
                                                                data-project-id="{{ $project->obfuscated_id }}"
                                                                data-project-stage-id="{{ $currentProjectStage->obfuscated_id??'-' }}" {{-- Pass obfuscated project stage ID --}}
                                                                data-kanban-stage-id="{{ $kanbanStage->id }}" {{-- Also useful for UI updates --}}
                                                                {{ $completeStage }}>Complete</button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                    // Fetch tasks for this specific project's stage
                                    // Assuming you have $project->projectStages eager loaded with tasks
                                    $projectStage = $project->projectStages() // Access the relationship as a Query Builder
                                        ->where('kanban_stage_id', $kanbanStage->id)
                                        ->where('data_status', '!=', 0) // 'whereNot' for DB is 'whereColumn', or simple '!='
                                        ->first();

                                    $projectStageTasks = $projectStage ? $projectStage->tasks->where('data_status', '!=', 0) : collect();
                                    if($project->data_status===1){
                                        $bgColor = '#E67514';
                                    } else if($project->data_status===2){
                                        $bgColor = '#8aec6f';
                                    } else {
                                        $bgColor = '#06923E';
                                    }
                                    @endphp

                                    @foreach($projectStageTasks as $task)

                                    @php
                                    if($task->data_status==1){
                                        $bgColor = '#FFC107';
                                    } else if($task->data_status==2){
                                        $bgColor = '#8aec6f';
                                    } else {
                                        $bgColor = '#06923E';
                                    }
                                    @endphp
                                    
                                    <div class="card text-bg-light mb-3 stage-task-card" data-stage-assigned-member-id="{{ $task->assigned_to_user_id ?? '' }}" style="max-width: 540px; border: 1px solid {{ $bgColor }};">
                                        <div class="row ms-1 mt-4">
                                            <div class="col-9">
                                                <label class="detail-label stage-task-title">{{ $task->name }}</label>
                                                <p class="stage-task-description"><small>{{ $task->description }}</small></p>
                                            </div>
                                            <div class="col-3">
                                                <img src="{{ $task->assignedTo->avatar_url }}" alt="{{ $task->assignedTo->name }}'s avatar"
                                                    alt="{{ $task->assignedTo->name }}"
                                                            class="task-assignee-avatar rounded-circle"
                                                            width="20" height="20"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="{{ $task->assignedTo->name }}">
                                            </div>
                                        </div>
                                        <div class="row mt-3 mb-3">
                                            <div class="col-6">
                                                <div class="list-group">
                                                    <button type="button" class="py-1 list-group-item list-group-item-action btn-sm text-info view-task-details-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#taskDetailModal"
                                                            data-task-id="{{ $task->task_obfuscated_id }}" 
                                                    ><small>View</small></button>
                                                    @if($task->assigned_to_user_id==auth()->user()->id && $project->data_status==1  && $task->data_status!=4)                                                
                                                        <button type="button" class="py-1 list-group-item list-group-item-action btn-sm text-primary view-task-edit-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#taskEditModal"
                                                            data-task-id="{{ $task->task_obfuscated_id }}"
                                                            ><small>Edit</small></button>
                                                    @endif
                                                    @if($project->data_status==1 && $task->data_status!=4)
                                                        <button type="button" class="py-1 list-group-item list-group-item-action btn-sm text-warning add-logs-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#taskAddLogsModal"
                                                            data-task-id="{{ $task->task_obfuscated_id }}"
                                                            data-project-id="{{ $project->obfuscated_id }}"><small>Add Logs</small>
                                                        </button>
                                                    @endif
                                                    @if($project->project_manager_id==auth()->user()->id && $project->data_status==1 && $task->data_status!=4)  
                                                        <button type="button" class="py-1 list-group-item list-group-item-action btn-sm text-success view-task-update-status-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#taskUpdateStatusModal"
                                                            data-project-manager-id="{{ $project->project_manager_id }}"
                                                            data-assign-to="{{ $task->assigned_to_user_id }}"
                                                            data-user-logon="{{ auth()->user()->id }}"
                                                            data-task-id="{{ $task->task_obfuscated_id }}"
                                                            data-project-id="{{ $project->id }}"
                                                            data-task-log="{{ $task->update_log }}" 
                                                            data-task-status="{{ $task->task_status }}" 
                                                            data-task-status-badge="{{ $task->task_status_badge }}"><small>Update Status</small></button>
                                                            @if($task->assigned_to_user_id==$project->project_manager_id)
                                                            <button type="button" class="py-1 list-group-item list-group-item-action btn-sm text-danger view-task-delete-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#taskDeleteModal"
                                                            data-task-id="{{ $task->task_obfuscated_id }}"
                                                            data-task-name="{{ $task->name }}"><small>Delete</small></button>
                                                            @endif
                                                    @endif
                                                    @if($task->assigned_to_user_id==auth()->user()->id 
                                                    && $project->data_status==1 
                                                    && $project->project_manager_id!=auth()->user()->id 
                                                    && $project->data_status==1 
                                                    && ($task->data_status!=4 && $task->data_status!=2 ))  
                                                    <button type="button" class="py-1 list-group-item list-group-item-action btn-sm text-success view-task-update-status-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#taskUpdateStatusModal"
                                                            data-project-manager-id="{{ $project->project_manager_id }}"
                                                            data-assign-to="{{ $task->assigned_to_user_id }}"
                                                            data-user-logon="{{ auth()->user()->id }}"
                                                            data-task-id="{{ $task->task_obfuscated_id }}"
                                                            data-project-id="{{ $project->id }}"
                                                            data-task-log="{{ $task->update_log }}" 
                                                            data-task-status="{{ $task->task_status }}" 
                                                            data-task-status-badge="{{ $task->task_status_badge }}"><small>Update Status</small></button>
                                                    
                                                        <button type="button" class="py-1 list-group-item list-group-item-action btn-sm text-danger view-task-delete-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#taskDeleteModal"
                                                            data-task-id="{{ $task->task_obfuscated_id }}"
                                                            data-task-name="{{ $task->name }}"><small>Delete</small></button>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-6 ">
                                                <div class="d-flex flex-column align-items-end justify-content-end h-100 pe-4">
                                                    <p><small>Due Date : </small><small class="
                                                @if ($task->end_at->isPast())
                                                    text-danger
                                                @else
                                                    text-warning
                                            @endif
                                                ">{{ $task->end_at->format('d M Y') }}</small></p>
                                                    <a href="#" class="download-files-link mb-2 view-task-details-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#taskDetailModal"
                                                            data-task-id="{{ $task->task_obfuscated_id }}">Download Files</a>
                                                    <span class="badge {{ $task->task_status_badge }} rounded-pill">{{ $task->task_status }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <style>
                    .bulletins-scroll-area {
                        max-height: 200px; /* Adjust this value as needed */
                        overflow-y: auto;  /* Enables vertical scrollbar when content overflows */
                        margin-bottom: 15px; /* Add some space between bulletins and the form */
                        padding-right: 5px; /* Add a small padding to prevent scrollbar from touching content */
                    }
                </style>

                <div class="modal fade" id="bulletinModal" tabindex="-1" aria-labelledby="bulletinModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg"> {{-- Use modal-lg for a larger modal --}}
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="bulletinModalLabel">Stage Bulletins: <span id="modalStageName"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="bulletinsContainer" class="bulletins-scroll-area">
                                    <p class="text-muted">Loading bulletins...</p>
                                </div>
                                <hr>
                                <div id="addBulletinContainer">
                                    <h6>Add New Bulletin</h6>
                                    <form id="addBulletinForm">
                                        @csrf
                                        <input type="hidden" name="project_stage_id" id="modalProjectStageId">
                                        <input type="hidden" name="project_id" id="modalProjectId">
                                        <div class="mb-3">
                                            <textarea class="form-control" id="newBulletinDescription" name="description" rows="3" placeholder="Enter new bulletin here..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">Add Bulletin</button>
                                    </form>
                                </div>
                                <div id="formMessages" class="mt-2"></div> {{-- For success/error messages --}}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        const taskSearchInput = $('#taskSearch');
                        const taskCards = $('.stage-task-card'); // Select all elements that represent a task
                        const taskSearchClearButton = $('#taskSearchClear');
                        taskSearchInput.on('keyup', function() {
                            const searchTerm = $(this).val().toLowerCase(); // Get the search term and convert to lowercase
                            taskCards.each(function() {
                                const taskCard = $(this);
                                // Get the text content from the relevant parts of the task card
                                // You can choose to search in specific elements like title and description
                                const taskTitle = taskCard.find('.stage-task-title').text().toLowerCase();
                                const taskDescription = taskCard.find('.stage-task-description').text().toLowerCase();
                                // Or search the entire text content of the card
                                // const taskContent = taskCard.text().toLowerCase();

                                // Check if the search term is found in the task's content
                                if (taskTitle.includes(searchTerm) || taskDescription.includes(searchTerm)) {
                                    taskCard.show(); // Show the task card if it matches
                                } else {
                                    taskCard.hide(); // Hide the task card if it doesn't match
                                }
                            });
                        });
                        taskSearchClearButton.on('click', function() {
                            taskSearchInput.val(''); // Clear the input field
                            taskSearchInput.trigger('keyup'); // Trigger the keyup event to re-filter (show all tasks)
                            taskSearchInput.focus(); // Optionally, put focus back on the search input
                        });

                        function filterTasks(searchTerm = null, memberId = null) {
                            taskCards.each(function() {
                                const taskCard = $(this); // **Correctly refers to the current task card**
                                const assignedMemberId = taskCard.data('stage-assigned-member-id'); // **Get data from current card**
                                const taskTitle = taskCard.find('.task-title').text().toLowerCase();
                                const taskDescription = taskCard.find('.task-description').text().toLowerCase();

                                let showBySearch = true;
                                let showByMember = true;

                                // Apply text search filter
                                if (searchTerm && searchTerm.trim() !== '') {
                                    showBySearch = (taskTitle.includes(searchTerm) || taskDescription.includes(searchTerm));
                                }

                                // Apply member filter
                                if (memberId) {
                                    // **FIX:** Use loose equality (==) for comparison as IDs might be string or number
                                    showByMember = (assignedMemberId == memberId);
                                }

                                // Show task only if it passes ALL active filters
                                if (showBySearch && showByMember) {
                                    taskCard.show();
                                } else {
                                    taskCard.hide();
                                }
                            });
                        }

                        $('#findTaskButton').on('click', function() {
                            const memberId = $('#modalMemberId').val();// Get the member ID from the hidden input
                            // Clear the text search input when filtering by member
                            taskSearchInput.val('');
                            filterTasks(null, memberId); // Apply the member filter
                            $('#memberDetailsModal').modal('hide');
                        });
                    });
                </script>
                <script>
                    $(document).ready(function() {

                        const bulletinModal = $('#bulletinModal');
                        const bulletinsContainer = $('#bulletinsContainer');
                        const modalStageName = $('#modalStageName');
                        const modalProjectStageIdInput = $('#modalProjectStageId');
                        const modalProjectIdInput = $('#modalProjectId');
                        const addBulletinForm = $('#addBulletinForm');
                        const newBulletinDescription = $('#newBulletinDescription');
                        const formMessages = $('#formMessages');

                        let currentProjectId = null;
                        let currentStageId = null;
                        let currentStageName = null;

                        function loadBulletins() {
                            if (!currentProjectId || !currentStageId) {
                                bulletinsContainer.html('<p class="text-danger">Error: Missing Project or Stage ID.</p>');
                                return;
                            }

                            bulletinsContainer.html('<p class="text-muted">Loading bulletins...</p>'); // Show loading message

                            $.ajax({
                                // Ensure this URL matches your Laravel route, accommodating both IDs
                                // Example: /v1/project-management/project-stages/{projectId}/{stageId}/bulletins/data
                                url: `/v1/project-management/project-stages/${currentStageId}/${currentProjectId}/bulletins/data`,
                                method: 'GET',
                                success: function(response) {
                                    let bulletinsHtml = '';
                                    if (response.bulletins && response.bulletins.length > 0) {
                                        response.bulletins.forEach(function(bulletin) {
                                            const createdBy = bulletin.created_by_user ? bulletin.created_by_user.name : 'System';
                                            const createdAt = new Date(bulletin.created_at).toLocaleString('en-US', {
                                                year: 'numeric', month: 'short', day: 'numeric',
                                                hour: 'numeric', minute: 'numeric', hour12: true
                                            });
                                            bulletinsHtml += `
                                                <div class="card mb-2">
                                                    <div class="card-body py-2">
                                                        <h6 class="card-subtitle mb-1 text-muted d-flex justify-content-between align-items-center">
                                                            <span>${createdBy}</span>
                                                            <small>${createdAt}</small>
                                                        </h6>
                                                        <p class="card-text mb-0">${bulletin.description}</p>
                                                    </div>
                                                </div>
                                            `;
                                        });
                                    } else {
                                        bulletinsHtml = '<p class="text-muted">No bulletins found for this stage.</p>';
                                    }
                                    bulletinsContainer.html(bulletinsHtml);
                                },
                                error: function(xhr) {
                                    console.error('Error fetching bulletins:', xhr.responseText);
                                    bulletinsContainer.html('<p class="text-danger">Failed to load bulletins. Please try again.</p>');
                                }
                            });
                        }

                        // Listen for the modal to be shown
                        bulletinModal.on('show.bs.modal', function (event) {
                            const button = $(event.relatedTarget); // Button that triggered the modal
                            const bulletinProjectStageId = button.data('bulletin-stage-id');
                            const bulletinProjectId = button.data('bulletin-project-id');
                            const stageName = button.closest('.card-body').find('.card-title').text(); // Get stage name from the card

                            modalStageName.text(stageName); // Update modal title
                            modalProjectStageIdInput.val(bulletinProjectStageId); // Set hidden input for form submission
                            modalProjectIdInput.val(bulletinProjectId);
                            bulletinsContainer.html('<p class="text-muted">Loading bulletins...</p>'); // Show loading message
                            formMessages.empty(); // Clear previous form messages
                            newBulletinDescription.val(''); // Clear form field

                            if (button.length > 0) {
                                currentProjectId = button.data('bulletin-project-id');
                                currentStageId = button.data('bulletin-stage-id');
                                currentStageDisable = button.data('bulletin-stage-disable');
                                currentStageName = button.closest('.card-body').find('.card-title').text();
                            }

                            loadBulletins();
                            if(currentStageDisable=='disabled'){
                                $('#addBulletinContainer').hide();
                            } else {
                                 $('#addBulletinContainer').show();
                            }
                        });

                        // Handle the "Add Bulletin" form submission
                        addBulletinForm.on('submit', function(e) {
                            e.preventDefault(); // Prevent default form submission

                            const addBulletinProjectStageId = modalProjectStageIdInput.val();
                            const addBulletinProjectId = modalProjectIdInput.val();
                            const description = newBulletinDescription.val();

                            $.ajax({
                                url: `/v1/project-management/project-stages/${addBulletinProjectStageId}/${addBulletinProjectId}/bulletins/store`, // Route for storing bulletins
                                method: 'POST',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                                    description: description
                                },
                                success: function(response) {
                                    formMessages.html('<div class="alert alert-success">' + response.message + '</div>');
                                    newBulletinDescription.val(''); // Clear the textarea
                                    // Reload bulletins after successful addition
                                    loadBulletins(); // Re-trigger show to reload content
                                },
                                error: function(xhr) {
                                    const errors = xhr.responseJSON.errors;
                                    let errorHtml = '<div class="alert alert-danger"><ul>';
                                    if (errors) {
                                        for (const key in errors) {
                                            errorHtml += '<li>' + errors[key][0] + '</li>';
                                        }
                                    } else {
                                        errorHtml += '<li>' + (xhr.responseJSON.message || 'An error occurred.') + '</li>';
                                    }
                                    errorHtml += '</ul></div>';
                                    formMessages.html(errorHtml);
                                    console.error('Error adding bulletin:', xhr.responseText);
                                }
                            });
                        });
                    });
                </script>

                <!-- Project File Delete Confirmation Modal -->
                <div class="modal fade" id="deleteProjectFileModal" tabindex="-1" aria-labelledby="deleteProjectFileModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="deleteProjectFileModalLabel">Confirm File Deletion</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="deleteFileId">
                                <input type="hidden" id="deleteFileProjectId"> {{-- Add this for the project ID --}}
                                <p>Are you sure you want to delete file "<strong id="deleteFileName"></strong>"?</p>
                                <p class="text-muted"><small>This action cannot be undone and will permanently remove the file.</small></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteProjectFileButton">Delete File</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task Delete Confirmation Modal -->
                <div class="modal fade" id="taskDeleteModal" tabindex="-1" aria-labelledby="taskDeleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm modal-dialog-centered"> {{-- modal-sm for a smaller, focused modal --}}
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white"> {{-- Red header for danger --}}
                                <h5 class="modal-title" id="taskDeleteModalLabel">Confirm Deletion</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="deleteModalTaskId">
                                <input type="hidden" id="deleteModalProjectId"> {{-- Hidden input for project_id --}}
                                <p>Are you sure you want to delete the task named "<strong id="deleteModalTaskName"></strong>"?</p>
                                <p class="text-muted"><small>This action cannot be undone.</small></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteTaskButton">Delete Task</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task Edit Modal -->
                <div class="modal fade" id="taskEditModal" tabindex="-1" aria-labelledby="taskEditModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="taskEditModalLabel">Edit Task: <span id="editModalTaskName"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="editTaskForm" >
                                @csrf
                                <div class="modal-body">
                                    <ul class="nav nav-tabs" id="taskEditTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="task-detail-tab" data-bs-toggle="tab" data-bs-target="#task-detail-tab-pane" type="button" role="tab" aria-controls="task-detail-tab-pane" aria-selected="true">Task</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="task-files-tab" data-bs-toggle="tab" data-bs-target="#task-files-tab-pane" type="button" role="tab" aria-controls="task-files-tab-pane" aria-selected="false">Files</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="taksEditTabContent">
                                        <div class="tab-pane fade show active" id="task-detail-tab-pane" role="tabpanel" aria-labelledby="task-detail-tab" tabindex="0">
                                            <input type="hidden" name="project_id" id="editModalProjectId">
                                            <input type="hidden" name="task_id" id="editModalTaskId">

                                            <div class="mb-3">
                                                <label for="taskName" class="form-label">Task Name</label>
                                                <input type="text" class="form-control" id="editTaskName" name="name" required placeholder="e.g., Design UI Mockups">
                                            </div>

                                            <div class="mb-3">
                                                <label for="taskDescription" class="form-label">Description</label>
                                                <textarea class="form-control" id="editTaskDescription" name="description" rows="3" placeholder="Detailed description of the task..."></textarea>
                                            </div>

                                            <div class="row mb-3">
                                                
                                                <div class="col-md-6">
                                                    <label for="editTaskEndDate" class="form-label">Due Date</label>
                                                    <input type="date" class="form-control" id="editTaskEndDate" name="end_date">
                                                </div>                                    
                                                
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="task-files-tab-pane" role="tabpanel" aria-labelledby="task-files-tab" tabindex="0">
                                            <div class="col-md-12 mt-3">
                                                    <label class="form-label">Upload New Project Files (PDF, DOC/DOCX)</label>
                                                    <div id="new-file-upload-container">
                                                            <div class="file-upload-item">
                                                                <div class="input-group mb-2">
                                                                    <input type="file" name="project_files[]" class="form-control" accept=".pdf,.doc,.docx">
                                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-file-input"><i class="fas fa-trash"></i></button>
                                                                </div>
                                                                <input type="text" name="project_file_descriptions[]" class="form-control mt-1 mb-1" placeholder="Please enter file description">
                                                            </div>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" id="update-add-more-files-btn"><i class="fas fa-plus"></i> Add Another File</button>
                                                    <small class="form-text text-muted d-block mt-2">Max file size: 3MB per file. Allowed types: PDF, DOC, DOCX.</small>
                                            </div>
                                            <div class="col-sm-12" id="editTaskStageFiles"></div>
                                        </div>
                                    </div>
                                    
                                    
                                        
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="saveEditTaskButton">Update Task</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Add/View Task Logs Modal -->
                <div class="modal fade" id="taskAddLogsModal" tabindex="-1" aria-labelledby="taskAddLogsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="taskAddLogsModalLabel">Task Logs: <span id="addLogsModalTaskName"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="addTaskLogForm">
                                <div class="modal-body">
                                    <input type="hidden" name="task_id" id="addLogsModalTaskId">
                                    <input type="hidden" name="project_id" id="addLogsModalProjectId"> {{-- For routing --}}

                                    <div class="mb-3">
                                        <label for="newLogEntryTextarea" class="form-label">Update Log Entry</label>
                                        <textarea class="form-control" id="newLogEntryTextarea" name="new_log_entry" rows="3" placeholder="Enter new update or comment here..."></textarea>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="saveLogButton">Update Log</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Task Update Status Modal -->
                <div class="modal fade" id="taskUpdateStatusModal" tabindex="-1" aria-labelledby="taskUpdateStatusModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="taskUpdateStatusModalLabel">Update Task Status: <span id="updateModalTaskName"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="updateTaskStatusForm">
                                <div class="modal-body">
                                    <div id="taskMessage"></div>
                                    <input type="hidden" name="task_id" id="updateModalTaskId">
                                    <input type="hidden" name="project_id" id="updateModalProjectId"> {{-- Add project_id for routing --}}

                                    <div class="mb-3">
                                        <p class="mb-1"><strong>Current Status: </strong> <span id="currentTaskStatus" class="badge bg-warning rounded-pill">On Going</span></p>
                                        <label for="newStatus" class="form-label">New Status</label>
                                        <select class="form-select" id="newStatus" name="status" required>
                                        </select>
                                    </div>

                                    <div id="previousUpdateLogs">
                                        <h6 class="mb-2">Previous Logs:</h6>
                                        <label id="taskUpdateLog"></label>
                                        <ul class="list-group list-group-flush" id="logsList">
                                            {{-- Logs will be loaded here by JavaScript --}}
                                            <li class="list-group-item text-muted">No previous logs.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-update-status">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Task Detail Viewer Modal -->
                <div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"> {{-- modal-lg for larger content, scrollable if content overflows --}}
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="taskDetailModalLabel">Task Details: <span id="modalTaskName"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Task Detail</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Update Logs</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Files</button>
                                    </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                            <div class="container-fluid">
                                                                            <div class="row mb-2">
                                                                                <div class="col-sm-4 fw-bold">Task Name:</div>
                                                                                <div class="col-sm-8" id="taskName"></div>
                                                                            </div>
                                                                            <div class="row mb-2">
                                                                                <div class="col-sm-4 fw-bold">Description:</div>
                                                                                <div class="col-sm-8" id="taskDescription"></div>
                                                                            </div>
                                                                            <div class="row mb-2">
                                                                                <div class="col-sm-4 fw-bold">Assigned To:</div>
                                                                                <div class="col-sm-8" id="taskAssignedTo"></div>
                                                                            </div>
                                                                            <div class="row mb-2">
                                                                                <div class="col-sm-4 fw-bold">Created By:</div>
                                                                                <div class="col-sm-8" id="taskCreatedby"></div>
                                                                            </div>
                                                                            <div class="row mb-2">
                                                                                <div class="col-sm-4 fw-bold">Created Date:</div>
                                                                                <div class="col-sm-8" id="taskStartAt"></div>
                                                                            </div>
                                                                            <div class="row mb-2">
                                                                                <div class="col-sm-4 fw-bold">Due Date:</div>
                                                                                <div class="col-sm-8" id="taskEndAt"></div>
                                                                            </div>
                                                                            <div class="row mb-2">
                                                                                <div class="col-sm-4 fw-bold">Stage:</div>
                                                                                <div class="col-sm-8" id="taskStage"></div>
                                                                            </div>
                                                                            <div class="row mb-2">
                                                                                <div class="col-sm-4 fw-bold">Stage Description:</div>
                                                                                <div class="col-sm-8" id="taskStageDescription"></div>
                                                                            </div>                                        
                                                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                                                    <div class="col-sm-8" id="taskUpdateLogView"></div>
                                    </div>
                                    <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                                                                    <div class="col-sm-8" id="taskStageFiles"></div>
                                    </div>
                                </div>
                        </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {



                    // --- Project File Delete Modal Logic ---
                    $(document).on('click', '.delete-file-btn', function() {
                        const fileId = $(this).data('file-id');
                        const tableFileId = $(this).closest('tr').data('table-file-id');
                        const projectId = $(this).data('project-id'); // Get project ID
                        const fileName = $(this).data('file-name'); // Get file name from the row

                        const $modal = $('#deleteProjectFileModal');
                        $modal.find('#deleteFileId').val(fileId);
                        $modal.find('#deleteFileProjectId').val(projectId); // Set project ID
                        $modal.find('#deleteFileName').text(fileName);

                        $modal.modal('show'); // Show the modal
                    });

                    // --- Handle confirmation of project file deletion ---
                    $('#confirmDeleteProjectFileButton').off('click').on('click', function() {
                        const $button = $(this);
                        $button.prop('disabled', true).text('Deleting...');

                        const fileIdToDelete = $('#deleteFileId').val();
                        const projectIdForFile = $('#deleteFileProjectId').val(); // Get project ID
                        //const tableFileId = $(this).closest('tr').data('table-file-id');

                        $.ajax({
                            url: `/v1/project-management/${fileIdToDelete}/file-destroy`, // API endpoint for file deletion
                            type: 'DELETE', // Use DELETE HTTP method
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert(response.message || 'File deleted successfully!');
                                    $('#deleteProjectFileModal').modal('hide');
                                    //location.reload(true); // Refresh the page to reflect changes
                                    // Or, for a smoother UX, remove the row from the DOM:
                                    $(`tr[data-table-file-id="${fileIdToDelete}"]`).remove();
                                    const filesTabButton = document.getElementById('project-files');
                                    if (filesTabButton) { // Check if the element exists
                                        const bsTab = new bootstrap.Tab(filesTabButton);
                                        bsTab.show();
                                    }
                                    //window.location.href = window.location.origin + window.location.pathname + window.location.search + '#project-files';
                                } else {
                                    alert(response.message || 'Failed to delete file.');
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'An error occurred while deleting the file.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                alert(errorMessage);
                                console.error('Error deleting file:', xhr.responseText);
                            }
                        }).always(function() {
                            $button.prop('disabled', false).text('Delete File');
                        });
                    });

                    // Reset modal content when hidden
                    $('#deleteProjectFileModal').on('hidden.bs.modal', function () {
                        $(this).find('#deleteFileId').val('');
                        $(this).find('#deleteFileProjectId').val('');
                        $(this).find('#deleteFileName').text('');
                        $(this).find('#confirmDeleteProjectFileButton').off('click'); // Remove the handler
                    });

                    // --- Task Delete Confirmation Modal Logic ---
                    $('#taskDeleteModal').on('show.bs.modal', function (event) {
                        const button = $(event.relatedTarget); // Button that triggered the modal
                        const taskId = button.data('task-id'); // Task obfuscated ID
                        const taskName = button.data('task-name'); // Project obfuscated ID

                        const $modal = $(this);
                        const $deleteModalTaskId = $modal.find('#deleteModalTaskId');
                        const $deleteModalTaskName = $modal.find('#deleteModalTaskName');
                        const $confirmDeleteTaskButton = $modal.find('#confirmDeleteTaskButton');

                        // Reset UI
                        $deleteModalTaskName.text(taskName);

                        // Set hidden IDs
                        $deleteModalTaskId.val(taskId);

                        
                        // --- Attach DELETE action to the confirm button ---
                        // IMPORTANT: Use .off('click') to prevent multiple event listeners
                        // if the modal is opened multiple times without page refresh.
                        $confirmDeleteTaskButton.off('click').on('click', function() {
                            $(this).prop('disabled', true).text('Deleting...');

                            const taskIdToDelete = $deleteModalTaskId.val();

                            $.ajax({
                                url: `/v1/project-management/stages/${taskIdToDelete}/tasks-delete`, // API endpoint for deletion
                                type: 'DELETE', // Use DELETE HTTP method
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure you have CSRF token in meta tag
                                },
                                success: function(response) {
                                    if (response.success) {
                                        alert(response.message || 'Task deleted successfully!');
                                        $('#taskDeleteModal').modal('hide');
                                        location.reload(true); // Refresh page to reflect deletion
                                    } else {
                                        alert(response.message || 'Failed to delete task.');
                                    }
                                },
                                error: function(xhr) {
                                    let errorMessage = 'An error occurred while deleting task.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    alert(errorMessage);
                                    console.error('Error deleting task:', xhr.responseText);
                                }
                            }).always(function() {
                                // Re-enable and reset button text in case of error
                                $confirmDeleteTaskButton.prop('disabled', false).text('Delete Task');
                            });
                        });
                    });

                    // Reset modal content when hidden
                    $('#taskDeleteModal').on('hidden.bs.modal', function () {
                        const $modal = $(this);
                        $modal.find('#deleteModalTaskName').text('');
                        $modal.find('#deleteModalTaskId').val('');
                        $modal.find('#deleteModalProjectId').val('');
                        $modal.find('#confirmDeleteTaskButton').off('click'); // Remove the click handler
                    });


                    // --- Task Edit Modal Logic ---
                    $('#taskEditModal').on('show.bs.modal', function (event) {
                        const button = $(event.relatedTarget); // Button that triggered the modal
                        const taskId = button.data('task-id'); // Task obfuscated ID
                        const projectId = button.data('project-id'); // Project obfuscated ID
                        //const editTaskFiles = $('#editTaskStageFiles');

                        const $modal = $(this);
                        const $form = $modal.find('#editTaskForm');
                        const $editModalTaskId = $modal.find('#editModalTaskId');
                        const $editModalProjectId = $modal.find('#editModalProjectId');
                        const $editTaskName = $modal.find('#editTaskName');
                        const $editTaskDescription = $modal.find('#editTaskDescription');
                        const $editTaskEndDate = $modal.find('#editTaskEndDate');
                        const $editModalTaskName = $modal.find('#editModalTaskName');
                        const $saveEditTaskButton = $modal.find('#saveEditTaskButton');

                        
                        
                        // Clear previous validation feedback
                        $form.find('.is-invalid').removeClass('is-invalid');
                        $form.find('.invalid-feedback').text('');                  

                        // Set hidden IDs
                        $editModalTaskId.val(taskId);
                        $editModalProjectId.val(projectId);

                        // Fetch task details for editing
                        $.ajax({
                            url: `/v1/project-management/stage/${taskId}/tasks`, // Use your task detail API endpoint
                            type: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    const task = response.task;
                                    $editModalTaskName.text(task.name);

                                    // Populate form fields
                                    $editTaskName.val(task.name);
                                    $editTaskDescription.val(task.description);
                                    $editTaskEndDate.val(task.end_at ? new Date(task.end_at).toISOString().split('T')[0] : '');
                                    if (task.files && task.files.length > 0) {
                                            htmlContent = `<table class="table table-striped" width="100%">
                                            `;
                                            task.files.forEach(file => {
                                                htmlContent += `
                                                    <tr>
                                                        <td>
                                                            <i class="fas fa-file-alt me-2"></i> <a href="/storage/${file.file_path}" target="_blank">${file.short_file_name}
                                                            (${ (file.file_size / (1024 * 1024)).toFixed(2) } MB)</a>
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-danger delete-file-btn" {{-- Changed class name --}}
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteProjectFileModal" {{-- Target the confirmation modal --}}
                                                                    data-file-id="${file.id}"
                                                                    data-project-id="${task.project_id}" {{-- Pass task's project ID --}}
                                                                    data-file-name="${file.file_name}" {{-- Pass file name for confirmation text --}}
                                                                    >
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                `;
                                            });
                                            htmlContent += `</tablel>`;
                                        } else {
                                            htmlContent = `<p class="text-muted mt-4">No files attached to this task.</p>`;
                                        }
                                        document.getElementById('editTaskStageFiles').innerHTML = htmlContent; 
                                    

                                } else {
                                    alert('Failed to load task details for edit: ' + (response.message || 'Unknown error'));
                                    $modal.modal('hide');
                                }
                            },
                            error: function(xhr) {
                                alert('Error loading task details for edit: ' + (xhr.responseJSON && xhr.responseJSON.message || 'Server error'));
                                $modal.modal('hide');
                                console.error('Error fetching task details for edit:', xhr.responseText);
                            }
                        });
                    });

                    // --- Handle form submission for updating task ---
                    $('#editTaskForm').on('submit', async function(e) {
                        e.preventDefault();

                        const $form = $(this);
                        const taskId = $form.find('#editModalTaskId').val();
                        const projectId = $form.find('#editModalProjectId').val();
                        const $saveButton = $form.find('#saveEditTaskButton');

                        // Reset validation feedback
                        $form.find('.is-invalid').removeClass('is-invalid');
                        $form.find('.invalid-feedback').text('');

                        $saveButton.prop('disabled', true).text('Saving...');

                        const formData = new FormData(this);
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                        // If the Select2 is disabled, its value won't be sent. Use the hidden input.
                        if ($('#editTaskAssignee').prop('disabled') && $('#editAssignedToUserIdHidden').val()) {
                            formData.set('assigned_to_user_id', $('#editAssignedToUserIdHidden').val());
                        }

                        try {
                            const response = await fetch(`/v1/project-management/stages/${taskId}/tasks-update`, {
                                method: 'POST', // Always POST for FormData with _method spoofing
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                body: formData
                            });

                            const result = await response.json();

                            if (response.ok && result.success) { // Check both HTTP status and custom 'success' flag
                                alert(result.message || 'Task updated successfully!');
                                $('#taskEditModal').modal('hide');
                                location.reload(true); // Refresh page to reflect changes
                            } else {
                                // Handle validation errors or other API errors
                                let errorMessage = result.message || 'Failed to update task.';
                                if (result.errors) {
                                    errorMessage = 'Validation Errors:\n';
                                    for (const field in result.errors) {
                                        const inputField = $form.find(`[name="${field}"]`);
                                        inputField.addClass('is-invalid');
                                        inputField.next('.invalid-feedback').text(result.errors[field][0]);
                                        errorMessage += `- ${result.errors[field][0]}\n`;
                                    }
                                }
                                alert(errorMessage);
                            }
                        } catch (error) {
                            console.error('Error updating task:', error);
                            alert('An unexpected error occurred. Please try again.');
                        } finally {
                            $saveButton.prop('disabled', false).text('Save Changes');
                        }
                    });

                    // Reset modal content when hidden
                    $('#taskEditModal').on('hidden.bs.modal', function () {
                        const $modal = $(this);
                        //$modal.find('#editTaskForm')[0].reset();
                        //$modal.find('#editModalTaskName').text('');
                        //$modal.find('#saveEditTaskButton').prop('disabled', false).text('Save Changes');
                        
                    });

                    // --- Task Add/View Logs Modal Logic ---
                    $('#taskAddLogsModal').on('show.bs.modal', function (event) {
                        const button = $(event.relatedTarget); // Button that triggered the modal
                        const taskId = button.data('task-id'); // Task obfuscated ID
                        const projectId = button.data('project-id'); // Project obfuscated ID

                        const $modal = $(this);
                        const $form = $modal.find('#addTaskLogForm');
                        const $addLogsModalTaskId = $modal.find('#addLogsModalTaskId');
                        const $addLogsModalProjectId = $modal.find('#addLogsModalProjectId');
                        const $addLogsModalTaskName = $modal.find('#addLogsModalTaskName');
                        const $newLogEntryTextarea = $modal.find('#newLogEntryTextarea');
                        const $taskLogsList = $modal.find('#taskLogsList');
                        const $saveLogButton = $modal.find('#saveLogButton');

                        
                        // Set hidden IDs
                        $addLogsModalTaskId.val(taskId);
                        $addLogsModalProjectId.val(projectId);
    
                    });

                    // --- Handle form submission for adding a new log entry ---
                    $('#addTaskLogForm').on('submit', function(e) {
                        e.preventDefault();

                        const $form = $(this);
                        const formData = new FormData(this); // Collects all form data
                        const taskId = formData.get('task_id');
                        const projectId = formData.get('project_id');
                        const newLogEntry = formData.get('new_log_entry');

                        if (!newLogEntry || newLogEntry.trim() === '') {
                            alert('Log entry cannot be empty.');
                            return;
                        }

                        const $submitButton = $form.find('#saveLogButton');
                        $submitButton.prop('disabled', true).text('Adding...');

                        // Append CSRF token
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                        $.ajax({
                            url: `/v1/project-management/stages/${taskId}/tasks-add-log`, // New API endpoint for adding logs
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    alert(response.message || 'Log entry added successfully!');
                                    $('#taskAddLogsModal').modal('hide');
                                    location.reload(true); // Refresh page to reflect changes
                                } else {
                                    alert(response.message || 'Failed to add log entry.');
                                    if (response.errors) {
                                        let errorMessages = Object.values(response.errors).flat().join('\n');
                                        alert('Validation Errors:\n' + errorMessages);
                                    }
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'An error occurred while adding log entry.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                                }
                                alert(errorMessage);
                                console.error('Error adding log entry:', xhr.responseText);
                            }
                        }).always(function() { // Always re-enable button whether success or error
                            $submitButton.prop('disabled', false).text('Add Log');
                        });
                    });

                    // Reset modal content on hide
                    $('#taskAddLogsModal').on('hidden.bs.modal', function () {
                        const $modal = $(this);
                        $modal.find('#addTaskLogForm')[0].reset();
                        $modal.find('#addLogsModalTaskName').text('');
                        $modal.find('#taskLogsList').html('');
                    });

                        // --- Task Update Status Modal Logic ---
                    $('#taskUpdateStatusModal').on('show.bs.modal', function (event) {
                        $('#currentTaskStatus').text('');
                        $('#taskMessage').empty();
                        const statusOptions = [];
                        $('#currentTaskStatus').removeClass();
                        const button = $(event.relatedTarget); // Button that triggered the modal
                        const taskId = button.data('task-id'); // Task obfuscated ID
                        const projectId = button.data('project-id'); // Project obfuscated ID (assuming it's passed)
                        const taskStatus = button.data('task-status');
                        const taskLog = button.data('task-log');
                        const taskStatusBadge = button.data('task-status-badge');
                        const taskAssignTo = button.data('assign-to');
                        const taskProjectManagerId = button.data('project-manager-id');
                        const taskUserLogon = button.data('user-logon');
                        $('#updateModalTaskId').val(taskId);
                        $('#updateModalProjectId').val(projectId);
                        $('#currentTaskStatus').text(taskStatus);
                        $('#currentTaskStatus').removeClass().addClass('badge '+taskStatusBadge+' rounded-pill' );
                        $('#taskUpdateLog').text(taskLog);

                        const $newStatusSelect = $('#newStatus'); 
                        //$newStatusSelect.find('option:not(:first)').remove();
                        $newStatusSelect.find('option[value!=""]').remove();
                        if(taskUserLogon===taskProjectManagerId && taskAssignTo===taskProjectManagerId){
                            statusOptions.push(
                            { value: '4', text: 'Task Completed' }
                            );
                        }

                        if(taskUserLogon===taskProjectManagerId && taskAssignTo!==taskProjectManagerId && taskStatus!=='Task Ongoing'){
                            statusOptions.push(
                            { value: '4', text: 'Task Completed' }
                            );
                        } else {
                            if(taskUserLogon===taskProjectManagerId && taskAssignTo!==taskProjectManagerId && taskStatus==='Task Ongoing'){
                                $newStatusSelect.remove();
                                $('.btn-update-status').prop('disabled', true);
                                $('#taskMessage').html('<div class="alert alert-danger" role="alert">The task status is <strong>On Going</strong>, task assignee must move to <strong>Task Pending Review</strong> first !</div>');
                            }
                        }

                        if(taskUserLogon=== taskAssignTo && taskAssignTo!==taskProjectManagerId){
                            statusOptions.push(
                            { value: '2', text: 'Task Pending Review' }
                            );
                        }

                        statusOptions.forEach(option => {
                            const optionHtml = `
                                <option value="${option.value}" >
                                    ${option.text}
                                </option>
                            `;
                            $newStatusSelect.append(optionHtml);
                        });
                        
                    });

                    // --- Handle form submission for updating task status ---
                    $('#updateTaskStatusForm').on('submit', function(e) {
                        e.preventDefault();

                        const $form = $(this);
                        const formData = new FormData(this); // Collects all form data, including textarea
                        const taskId = formData.get('task_id');
                        const projectId = formData.get('project_id'); // Ensure project_id is captured

                        // Append CSRF token
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                        // Send AJAX request
                        $.ajax({
                            url: `/v1/project-management/stages/${taskId}/tasks-update-status`, // Your new API endpoint
                            type: 'POST', // Use POST for form submission, Laravel handles _method PUT
                            data: formData,
                            processData: false, // Essential for FormData
                            contentType: false, // Essential for FormData
                            success: function(response) {
                                if (response.success) {
                                    alert(response.message || 'Task status updated successfully!');
                                    $('#taskUpdateStatusModal').modal('hide');
                                    location.reload(true); // Refresh page to reflect changes
                                } else {
                                    alert(response.message || 'Failed to update task status.');
                                    if (response.errors) {
                                        let errorMessages = Object.values(response.errors).flat().join('\n');
                                        alert('Validation Errors:\n' + errorMessages);
                                    }
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'An error occurred while updating task status.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                                }
                                alert(errorMessage);
                                console.error('Error updating task status:', xhr.responseText);
                            }
                        });
                    });

                    // Reset modal content on hide
                    $('#taskUpdateStatusModal').on('hidden.bs.modal', function () {
                        const $modal = $(this);
                        $modal.find('#updateTaskStatusForm')[0].reset(); // Reset the form
                        $modal.find('#updateModalTaskName').text('');
                        //$modal.find('#currentTaskStatus').attr('class', 'badge').text('');
                        $modal.find('#logsList').html(''); // Clear logs
                    });
                });

                        $('#taskDetailModal').on('show.bs.modal', function (event) {
                            const button = $(event.relatedTarget); // Button that triggered the modal
                            const taskId = button.data('task-id');
                            const projectId = button.data('project-id');

                            const $modal = $(this);
                            const $modalTitleSpan = $modal.find('#modalTaskName');
                            const $taskDetailContent = $modal.find('#taskDetailContent');
                            const $editTaskButton = $modal.find('#editTaskButton');                            

                            // Fetch task details via AJAX
                            $.ajax({
                                url: `/v1/project-management/stage/${taskId}/tasks`, // API endpoint to fetch task
                                type: 'GET',
                                success: function(response) {
                                    if (response.success) {
                                        const task = response.task;
                                        $modalTitleSpan.text(task.name); // Set actual task name in title

                                        document.getElementById('taskName').innerText = task.name;
                                        document.getElementById('taskDescription').innerText = task.description;
                                        document.getElementById('taskAssignedTo').innerText = task.assigned_to?.name || 'N/A';
                                        document.getElementById('taskCreatedby').innerText = task.created_by?.name || 'N/A';
                                        document.getElementById('taskStartAt').innerText = new Date(task.created_at).toLocaleString('en-GB', { // Using 'en-GB' for dd/mm/yyyy hh:mm format
                                            day: '2-digit',
                                            month: '2-digit',
                                            year: 'numeric', // Use 'numeric' for 4 digits (e.g., 2025)
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            hour12: false // Force 24-hour format if preferred, otherwise remove for AM/PM
                                        });
                                        document.getElementById('taskEndAt').innerText = new Date(task.end_at).toLocaleString('en-GB', {
                                            day: '2-digit',
                                            month: '2-digit',
                                            year: 'numeric'
                                        });
                                        //document.getElementById('taskUpdateLogView').innerText = task.update_log;
                                        document.getElementById('taskStage').innerText = task.project_stage?.kanban_stage?.name || 'N/A';
                                        document.getElementById('taskStageDescription').innerText = task.project_stage?.kanban_stage?.description || 'N/A';
                                        if (task.files && task.files.length > 0) {
                                            htmlContent = `
                                                <ul class="list-group list-group-flush">
                                            `;
                                            task.files.forEach(file => {
                                                htmlContent += `
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>
                                                            <h6>${file.description}</h6>
                                                            <i class="fas fa-file-alt me-2"></i> <a href="/storage/${file.file_path}" target="_blank">${file.short_file_name}
                                                            (${ (file.file_size / (1024 * 1024)).toFixed(2) } MB)</a>
                                                        </span>
                                                    </li>
                                                `;
                                            });
                                            htmlContent += `</ul>`;
                                        } else {
                                            htmlContent = `<p class="text-muted mt-4">No files attached to this task.</p>`;
                                        }
                                        document.getElementById('taskStageFiles').innerHTML = htmlContent; 

                                        if (task.logs && task.logs.length > 0) {
                                            htmlLogContents = `
                                                <ul class="list-group list-group-flush">
                                            `;
                                            task.logs.forEach(log => {
                                                const description = log.description || 'No description.';
                                                const userName = log.user ? log.user.name : 'System/Deleted User'; // Access log.user.name
                                                const createdAt = log.created_at ? new Date(log.created_at).toLocaleString() : 'N/A'; // Format timestamp
                                                htmlLogContents += `
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>
                                                            ${description}
                                                            <p><span class="text-muted text-sm">
                                                                <small>${createdAt} by ${userName}</small>
                                                            </span></p>
                                                        </span>
                                                    </li>
                                                `;
                                            });
                                            htmlLogContents += `</ul>`;
                                        } else {
                                            htmlLogContents = `<p class="text-muted mt-4">No task update logs.</p>`;
                                        }
                                        document.getElementById('taskUpdateLogView').innerHTML = htmlLogContents;                                       

                                    } else {
                                        $taskDetailContent.html('<p class="text-danger">Failed to load task details: ' + (response.message || 'Unknown error') + '</p>');
                                        $modalTitleSpan.text('Error');
                                    }
                                },
                                error: function(xhr) {
                                    let errorMessage = 'An error occurred while fetching task details.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    $taskDetailContent.html('<p class="text-danger">' + errorMessage + '</p>');
                                    $modalTitleSpan.text('Error');
                                    console.error('Error fetching task details:', xhr.responseText);
                                }
                            });
                        });

                        // Reset modal content when closed
                        $('#taskDetailModal').on('hidden.bs.modal', function () {
                            $(this).find('#taskDetailContent').html(''); // Clear content
                            $(this).find('#modalTaskName').text(''); // Clear title
                            $(this).find('#editTaskButton').hide(); // Hide edit button
                        });
                    </script>

                <!-- Create Task Modal -->
                <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="createTaskModalLabel">Create New Task for <span id="modalStageName"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="createTaskForm" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" name="project_id" id="createModalProjectId">
                                    <input type="hidden" name="kanban_stage_id" id="createModalKanbanStageId">

                                    <div class="mb-3">
                                        <label for="taskName" class="form-label">Task Name</label>
                                        <input type="text" class="form-control" id="taskName" name="name" required placeholder="e.g., Design UI Mockups">
                                    </div>

                                    <div class="mb-3">
                                        <label for="taskDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="taskDescription" name="description" rows="3" required placeholder="Detailed description of the task..."></textarea>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6" style="display:none">
                                            <label for="taskStartDate" class="form-label">Start Date</label>
                                            <input type="date" class="form-control" id="taskStartDate" name="start_date">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="taskEndDate" class="form-label">Due Date</label>
                                            <input type="date" class="form-control" id="taskEndDate" required name="end_date">
                                        </div>                                    
                                        <div class="col-md-6">
                                            <label for="taskEndDate" class="form-label">Task Status</label>
                                            <select class="form-control" id="taskStatus" name="task_status">
                                                <option value="1">Ongoing</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="taskAssignee" class="form-label">Person-In-Charges</label>
                                            <select class="form-select" id="taskAssignee" name="assigned_to_user_id"
                                            >
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="taskEndDate" class="form-label">Update Log</label>
                                            <input type="text" class="form-control" id="updateLog" name="update_log" placeholder="e.g., Update your activity">
                                        </div>
                                    </div>
                                         <div class="col-md-12 mt-3">
                                            <label class="form-label">Upload New Project Files (PDF, DOC/DOCX)</label>
                                            <div id="create-new-file-upload-container">
                                                <div class="file-upload-item">
                                                    <div class="input-group mb-2">
                                                        <input type="file" name="project_files[]" class="form-control" accept=".pdf,.doc,.docx">
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-file-input"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                    <input type="text" name="project_file_descriptions[]" class="form-control mt-1 mb-1" placeholder="Please enter file description">
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="create-add-more-files-btn"><i class="fas fa-plus"></i> Add Another File</button>
                                            <small class="form-text text-muted d-block mt-2">Max file size: 3MB per file. Allowed types: PDF, DOC, DOCX.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Task</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                                const $newFileUploadContainer = $('#create-new-file-upload-container');
                                const $addMoreFilesBtn = $('#create-add-more-files-btn');
                                const $updateAddMoreFilesBtn = $('#update-add-more-files-btn');
                                let indexNewFileUpload = $newFileUploadContainer.children('.file-upload-item').length;

                                // Function to update the visibility of "Remove" buttons
                                function updateRemoveButtonVisibility() {
                                    const currentFileInputCount = $newFileUploadContainer.children('.file-upload-item').length;
                                    // Show remove button if there's more than one file input field in the NEW upload section
                                    if ($newFileUploadContainer.children('.file-upload-item').length > 1) {
                                        $newFileUploadContainer.find('.remove-file-input').show();
                                    } else {
                                        $newFileUploadContainer.find('.remove-file-input').hide(); // Hide if only one remains
                                    }
                                }

                                // Function to add a new file input field group
                                function addFileInputField() {
                                    const currentFileInputCount = $newFileUploadContainer.children('.file-upload-item').length;
                                    if(indexNewFileUpload<=3){
                                        const newFileInputHtml = `
                                            <div class="file-upload-item">
                                                <div class="input-group mb-2">
                                                    <input type="file" name="project_files[]" class="form-control" accept=".pdf,.doc,.docx">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-file-input"><i class="fas fa-trash"></i></button>
                                                </div>
                                                <input type="text" name="project_file_descriptions[]" class="form-control mt-1 mb-1" placeholder="Please enter file description">
                                            </div>
                                        `;
                                        $newFileUploadContainer.append(newFileInputHtml);
                                        updateRemoveButtonVisibility();
                                    } else {
                                        alert('you can not add more than 3 files !');
                                    }
                                    
                                    
                                }

                                // Initial setup on page load:
                                // If no file input fields are present (e.g., fresh create form), add one.
                                // Otherwise, just adjust visibility of existing remove buttons (e.g., after validation error).
                                if ($newFileUploadContainer.children('.file-upload-item').length === 0) {
                                    addFileInputField();
                                } else {
                                    updateRemoveButtonVisibility();
                                }

                                // Event listener for "Add Another File" button
                                $addMoreFilesBtn.on('click', function() {
                                    indexNewFileUpload = $newFileUploadContainer.children('.file-upload-item').length;
                                    if(indexNewFileUpload>2){
                                        alert('you can not add more than 3 files !')
                                    } else {                                        
                                        addFileInputField();
                                    }
                                });

                                $updateAddMoreFilesBtn.on('click', function() {
                                    indexNewFileUpload = $newFileUploadContainer.children('.file-upload-item').length;
                                    if(indexNewFileUpload>2){
                                        alert('you can not add more than 3 files !')
                                    } else {                                        
                                        addFileInputField();
                                    }
                                });

                                // Event listener for "Remove" button on dynamically added file inputs
                                // Using event delegation because buttons are added dynamically
                                $newFileUploadContainer.on('click', '.remove-file-input', function() {
                                    indexNewFileUpload = $newFileUploadContainer.children('.file-upload-item').length;
                                    $(this).closest('.file-upload-item').remove(); // Remove the entire parent .input-group
                                    updateRemoveButtonVisibility();
                                });
                    </script>

                

                <style>
                    /* Custom CSS for the horizontal scroll wrapper */
                    .horizontal-scroll-wrapper {
                        overflow-x: auto;
                        /* Enable horizontal scrolling */
                        overflow-y: hidden;
                        /* Hide vertical scrollbar if it appears (optional) */
                        padding-bottom: 1rem;
                    }

                    .row-flex-nowrap-scroller {
                        display: flex;
                        flex-wrap: nowrap; /* Prevent wrapping */
                        gap: 1rem; /* g-4 equivalent for spacing between cards */
                        padding: 0 1rem; /* Padding for visual space around cards */
                        overflow-x: auto; /* Enable horizontal scrolling for this inner container */
                        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
                        scrollbar-width: none; /* Hide native scrollbar for Firefox */
                        -ms-overflow-style: none; /* Hide native scrollbar for IE/Edge */
                        max-width: 100%; /* Ensure it doesn't overflow its fixed parent */
                        height: 100%; /* Take full height of its parent */
                    }


                    /* Optional: Hide scrollbar in webkit browsers (Chrome, Safari) */
                    .horizontal-scroll-wrapper::-webkit-scrollbar {
                        height: 8px;
                    }

                    .horizontal-scroll-wrapper::-webkit-scrollbar-thumb {
                        background-color: rgba(0, 0, 0, 0.2);
                        border-radius: 4px;
                    }

                    .horizontal-scroll-wrapper::-webkit-scrollbar-track {
                        background-color: transparent;
                    }

                    .kanban-stage {
                        display: flex;
                        flex-direction: column;
                        max-height: 500px; /* Set your desired stage height */
                        overflow-y: auto; /* Enable vertical scroll per stage */
                        padding-right: 20px; /* Optional: to prevent content cutoff */
                    }
                    .kanban-stage::-webkit-scrollbar {
                        width: 8px;
                    }
                    .kanban-stage::-webkit-scrollbar-thumb {
                        background-color: rgba(0, 0, 0, 0.2);
                        border-radius: 4px;
                    }

                    /* Your custom card-disabled styles from previous example */
                    .card-disabled {
                        opacity: 0.6;
                        pointer-events: none;
                        cursor: not-allowed;
                        filter: grayscale(100%);
                        background-image: linear-gradient(45deg, rgba(255, 255, 255, .1) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .1) 50%, rgba(255, 255, 255, .1) 75%, transparent 75%, transparent);
                        background-size: 0.5rem 0.5rem;
                    }

                    .card-disabled .card-text,
                    .card-disabled .small,
                    .card-disabled .badge {
                        color: #999 !important;
                    }

                    .card-disabled .btn {
                        pointer-events: none;
                        opacity: 0.7;
                    }
                </style>
                <script>
                    function progressBar(percentage, aClass, aSymbol, aValue) {
                        if (percentage < 30) {
                            progressBarClass = 'bg-danger'; // Red for low progress
                        } else if (percentage < 70) {
                            progressBarClass = 'bg-warning'; // Yellow for medium progress
                        } else {
                            progressBarClass = 'bg-success'; // Green for high progress
                        }

                        $(aClass).append(`
                                <div class="progress" style="height: 15px; border-radius: 5px;">
                                    <div class="progress-bar ${progressBarClass} progress-bar-striped progress-bar-animated"
                                         role="progressbar"
                                         style="width: ${percentage}%;"
                                         aria-valuenow="${percentage}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        <span class="text-white small fw-bold">${aValue} ${aSymbol}</span>
                                    </div>
                                </div>
                            `);
                    }
                    let remainingDays = '{{ $project->remaining_days }}';
                    let procentagerRminingDays = '{{ round(($project->remaining_days / $project->start_at->diffInDays($project->end_at)) * 100) }}'; 
                    progressBar( procentagerRminingDays, '#dayProgress', 'd', remainingDays)
                    progressBar( {{
                            $project->work_progress_percentage
                        }}, '#workProgress', '%', {{
                            $project->work_progress_percentage
                        }}
                    )
                </script>
            </div>

        </div>
    </div>

    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMemberModalLabel">Add New Project Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addMemberForm"> {{-- Form for adding member --}}
                    <div class="modal-body">
                        <input type="hidden" name="project_id" value="{{ $project->obfuscated_id }}">
                        <div class="mb-3">
                            <label for="newMemberSelect" class="form-label">Select User to Add</label>
                            {{-- This is where your Select2 for users would go --}}
                            <select class="form-select" id="newMemberSelect" name="member_id" required>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                        data-avatar-url="{{ $user->avatar_url }}" {{-- IMPORTANT: Pass avatar URL --}}
                                        data-initials="{{ $user->name }}"> {{-- Optional: Initials for fallback --}}
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // --- Select2 Formatting Functions ---

            // Function to format each option in the dropdown list
            function formatUserResult(user) {
                if (!user.id) {
                    return user.text; // Return original text for placeholder
                }

                const avatarUrl = $(user.element).data('avatar-url');
                const initials = $(user.element).data('initials');
                
                let avatarHtml = '';
                if (avatarUrl) {
                    avatarHtml = `<img src="${avatarUrl}" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">`;
                } else if (initials) {
                    // Fallback for initials if no avatar URL
                    avatarHtml = `<div class="rounded-circle d-inline-flex align-items-center justify-content-center me-2 text-white bg-primary" style="width: 30px; height: 30px; font-size: 0.7em;">${initials.substring(0,2)}</div>`;
                }

                // Return a jQuery object or a DOM element
                const $container = $(
                    `<span class="d-flex align-items-center">${avatarHtml} ${user.text}</span>`
                );
                return $container;
            }

            // Function to format the selected option in the input field
            function formatUserSelection(user) {
                if (!user.id) {
                    return user.text; // Return original text for placeholder
                }

                const avatarUrl = $(user.element).data('avatar-url');
                const initials = $(user.element).data('initials');

                let avatarHtml = '';
                if (avatarUrl) {
                    avatarHtml = `<img src="${avatarUrl}" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">`;
                } else if (initials) {
                    avatarHtml = `<div class="rounded-circle d-inline-flex align-items-center justify-content-center me-2 text-white bg-secondary" style="width: 24px; height: 24px; font-size: 0.7em;">${initials.substring(0,2)}</div>`;
                }

                const $container = $(
                    `<span class="d-flex align-items-center">${avatarHtml} ${user.text}</span>`
                );
                return $container;
            }

            // --- Initialize Select2 when the modal is shown ---
            $('#addMemberModal').on('shown.bs.modal', function () {
                $('#newMemberSelect').select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#addMemberModal'), // IMPORTANT: This ensures the dropdown is within the modal's z-index context
                    placeholder: "Select a user", // Placeholder text
                    allowClear: true, // Allows clearing the selection
                    templateResult: formatUserResult, // Function to render each option in the dropdown
                    templateSelection: formatUserSelection // Function to render the selected option
                });
            });

            // --- Destroy Select2 when the modal is hidden to prevent duplicates ---
            $('#addMemberModal').on('hidden.bs.modal', function () {
                $('#newMemberSelect').select2('destroy');
            });

            // ... (Your other JavaScript code) ...
        });
        </script>

    <div class="modal fade" id="memberDetailsModal" tabindex="-1" aria-labelledby="memberDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-1">
                    <img id="modalMemberAvatar" src="" alt="Member Avatar" class="modal-member-avatar rounded-circle mb-3">
                    <div class="modal-member-avatar-placeholder mb-3 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold d-none"></div>
                    <h5 id="modalMemberName" class="mb-1"></h5>
                    <p id="modalMemberEmail" class="text-muted small"></p>
                    <p id="modalMemberPosition" class="text-muted small"></p>

                    <hr class="my-3">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        @if($project->project_manager_id == auth()->user()->id) {{-- Only project manager can remove --}}
                        <button type="button" class="btn btn-danger btn-sm" id="removeMemberButton"><small>Remove from Project</small></button>
                        <input type="hidden" id="modalMemberId">
                        <input type="hidden" id="modalProjectId">
                        @endif
                        <button type="button" class="btn btn-success btn-sm" id="findTaskButton"><small>Find the tasks</small></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <script>
        $(document).ready(function() {

            $('.complete-project-btn').on('click', function() {
                const $button = $(this);
                const projectId = $button.data('project-id'); // Get the obfuscated project ID

                // Check if the button is currently disabled
                if ($button.prop('disabled')) {
                    // If it's disabled, the tooltip should explain why, so just return
                    return;
                }

                if (!confirm('Are you sure you want to mark this ENTIRE project as COMPLETE? This action cannot be undone.')) {
                    return; // User cancelled
                }

                $button.prop('disabled', true).text('Completing Project...'); // Disable button and show loading text

                $.ajax({
                    url: `/v1/project-management/${projectId}/complete`, // Your API endpoint
                    type: 'PUT', // Use PUT method
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message || 'Project completed successfully!');
                            // Refresh the page to reflect the new overall project status
                            location.reload(true); // Force reload from server
                        } else {
                            alert(response.message || 'Failed to complete project.');
                            $button.prop('disabled', false).text('Complete Entire Project'); // Re-enable on failure
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 403) {
                            errorMessage = 'You are not authorized to complete this project.';
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        alert(errorMessage);
                        console.error('Error completing project:', xhr.responseText);
                        $button.prop('disabled', false).text('Complete Entire Project'); // Re-enable on error
                    }
                });
            });

            $('.mark-stage-complete-btn').on('click', function() {
                const $button = $(this);
                const projectId = $button.data('project-id');
                const projectStageId = $button.data('project-stage-id');
                const kanbanStageId = $button.data('kanban-stage-id'); // Useful for UI updates

                if (!confirm('Are you sure you want to mark this stage as COMPLETE? This action cannot be undone.')) {
                    return; // User cancelled
                }

                $button.prop('disabled', true).text('Completing...'); // Disable button and show loading text

                $.ajax({
                    url: `/v1/project-management/${projectId}/stages/${projectStageId}/complete`, // Your API endpoint
                    type: 'PUT', // Use PUT method
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    data: {
                        'kanban_stage_id': kanbanStageId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message || 'Stage completed successfully!');
                            // Refresh the page to reflect the new status and enable subsequent stages
                            location.reload(true); // Force reload from server
                        } else {
                            alert(response.message || 'Failed to complete stage.');
                            $button.prop('disabled', false).text('Complete Stage'); // Re-enable on failure
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 403) {
                            errorMessage = 'You are not authorized to complete this stage.';
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        alert(errorMessage);
                        console.error('Error completing stage:', xhr.responseText);
                        $button.prop('disabled', false).text('Complete'); // Re-enable on error
                    }
                });
            });

            function formatAssigneeResult(user) {
                if (!user.id) {
                    return user.text; // Return original text for placeholder
                }

                const avatarUrl = $(user.element).data('avatar-url');
                const initials = $(user.element).data('initials');
                
                let avatarHtml = '';
                if (avatarUrl) {
                    avatarHtml = `<img src="${avatarUrl}" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">`;
                } else if (initials) {
                    // Fallback for initials if no avatar URL
                    avatarHtml = `<div class="rounded-circle d-inline-flex align-items-center justify-content-center me-2 text-white bg-primary" style="width: 30px; height: 30px; font-size: 0.7em;">${initials.substring(0,2)}</div>`;
                }

                // Return a jQuery object or a DOM element
                const $container = $(
                    `<span class="d-flex align-items-center">${avatarHtml} ${user.text}</span>`
                );
                return $container;
            }

            // Function to format the selected option in the input field
            function formatAssigneeSelection(user) {
                if (!user.id) {
                    return user.text; // Return original text for placeholder
                }

                const avatarUrl = $(user.element).data('avatar-url');
                const initials = $(user.element).data('initials');

                let avatarHtml = '';
                if (avatarUrl) {
                    avatarHtml = `<img src="${avatarUrl}" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">`;
                } else if (initials) {
                    avatarHtml = `<div class="rounded-circle d-inline-flex align-items-center justify-content-center me-2 text-white bg-secondary" style="width: 24px; height: 24px; font-size: 0.7em;">${initials.substring(0,2)}</div>`;
                }

                const $container = $(
                    `<span class="d-flex align-items-center">${avatarHtml} ${user.text}</span>`
                );
                return $container;
            }

            $('#createTaskModal').on('hidden.bs.modal', function () {
                $('#taskAssignee').select2('destroy');
            });


            // Event listener for when the "Create Task" modal is about to be shown
            $('#createTaskModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget); // Button that triggered the modal
                const projectId = button.data('project-id');
                const kanbanStageId = button.data('kanban-stage-id');
                const kanbanStageName = button.data('kanban-stage-name');

                const $modal = $(this);
                const $assigneeSelect = $modal.find('#taskAssignee');
                const $modalProjectIdInput = $modal.find('#createModalProjectId');
                const $modalKanbanStageIdInput = $modal.find('#createModalKanbanStageId');
                const $assignedToUserIdHidden = $modal.find('#assignedToUserIdHidden'); // The hidden input

                // Set hidden project and kanban stage IDs in the form
                $modalProjectIdInput.val(projectId);
                $modalKanbanStageIdInput.val(kanbanStageId);

                // Clear previous options (keep the first "Select User (Optional)" option)
                $assigneeSelect.find('option:not(:first)').remove();
                
                // Disable the select by default, and update based on logged-in user
                $assigneeSelect.prop('disabled', true); // Start disabled for auto-selection logic

                // --- Fetch assignable users via AJAX ---
                $.ajax({
                    url: `/v1/project-management/${projectId}/assignable-users`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.users) {
                            const assignableUsers = response.users;
                            const currentUserId = {{ auth()->check() ? auth()->id() : "null" }}; // Get logged-in user ID
                            const projectManagerid = {{ $project->project_manager_id }};
                            const isProjectManager = (currentUserId  === projectManagerid);
                            let loggedInUserFound = false;

                            // Populate the select dropdown
                            assignableUsers.forEach(user => {
                                const isSelected = (currentUserId && user.id === currentUserId);
                                if (isSelected) {
                                    loggedInUserFound = true;
                                }
                                const optionHtml = `
                                    <option value="${user.id}"
                                            data-avatar-url="${user.avatar_url}"
                                            data-initials="${user.initials}"
                                            ${isSelected ? 'selected' : ''}
                                            >
                                        ${user.name}
                                    </option>
                                `;
                                $assigneeSelect.append(optionHtml);
                            });

                            $assigneeSelect.select2({
                                        theme: "bootstrap-5",
                                        dropdownParent: $('#createTaskModal'), // Make sure this is the correct ID of the modal container
                                        placeholder: "Select user (Optional)",
                                        allowClear: true,
                                        templateResult: formatAssigneeResult,
                                        templateSelection: formatAssigneeSelection,
                            });
                            // Logic for disabling/enabling and setting value:
                            if (loggedInUserFound) {
                                if(isProjectManager){
                                    $assigneeSelect.prop('disabled', false);
                                } else {
                                    // If logged-in user is in the list and auto-selected, keep the select disabled
                                    $assigneeSelect.prop('disabled', true);                                    
                                }
                                
                            } else {
                                // If logged-in user is NOT in the assignable list, enable the select
                                // so they can manually choose (or leave unassigned)
                                $assigneeSelect.prop('disabled', false);
                                // Make sure the hidden input is removed or its value cleared if select is enabled
                                if ($assignedToUserIdHidden.length) {
                                    $assignedToUserIdHidden.val('');
                                }
                            }
                            
                            // If you are using Select2 for styling, re-initialize it
                            
                            
                            // Trigger change to ensure Select2 updates its display
                            $assigneeSelect.trigger('change');

                        } else {
                            console.error('Failed to load assignable users:', response.message);
                            $assigneeSelect.append('<option value="" disabled>Error loading users</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX error loading assignable users:', xhr.responseText);
                        $assigneeSelect.append('<option value="" disabled>Error loading users</option>');
                    }
                });
            });

            // Handle the "Create Task" form submission via AJAX
            $('#createTaskForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const $form = $(this);
                // *** THIS IS THE KEY FIX: Remove $.param() ***
                const formData = new FormData(this); // Correctly creates FormData from the form

                
                const createProjectId = $('#createModalProjectId').val();
                const createKanbanStageId = $('#createModalKanbanStageId').val();
                
                @if($project->project_manager_id!=auth()->user()->id)
                    const selectElement = document.querySelector('select[name="assigned_to_user_id"]');
                    formData.append('assigned_to_user_id', selectElement.value);
                @endif

                // Send AJAX request
                $.ajax({
                    url: `/v1/project-management/${createProjectId}/stages/${createKanbanStageId}/tasks`, // API endpoint to create task
                    type: 'POST',
                    data: formData, // Pass FormData object directly
                    processData: false, // IMPORTANT: Tells jQuery not to process the data (essential for files)
                    contentType: false, // IMPORTANT: Tells jQuery not to set the Content-Type header (essential for files)
                    success: function(response) {
                        if (response.success) {
                            alert(response.message || 'Task created successfully!');
                            $('#createTaskModal').modal('hide'); // Close modal
                            location.reload(true); // Refresh page to see new task
                        } else {
                            alert(response.message || 'Failed to create task.');
                            // Display validation errors if available
                            if (response.errors) {
                                let errorMessages = Object.values(response.errors).flat().join('\n');
                                alert('Validation Errors:\n' + errorMessages);
                            }
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        alert(errorMessage);
                        console.error('Error creating task:', xhr.responseText);
                    }
                });
            });


            $('#addMemberForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const $form = $(this);
                const projectId = $form.find('input[name="project_id"]').val();
                const memberId = $form.find('#newMemberSelect').val();

                if (!memberId) {
                    alert('Please select a member to add.');
                    return;
                }

                $.ajax({
                    url: `/v1/project-management/${projectId}/add-member`, // Your API endpoint
                    type: 'POST',
                    data: {
                        member_id: memberId,
                        _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                            $('#addMemberModal').modal('hide'); // Hide modal on success

                            // Clear the selected value in Select2
                            $('#newMemberSelect').val(null).trigger('change');

                            // --- Dynamically update the UI with the new member ---
                            const newMemberData = response.new_member; // Get new member data from response
                            const totalMembersCount = response.total_members_count; // Get updated count

                            const avatarSrc = newMemberData.avatar_url ||
                                `https://placehold.co/40x40/d0c5f3/333333?text=${encodeURIComponent(newMemberData.initials || newMemberData.name.split(' ').map(n => n[0]).join('').toUpperCase())}`;

                            const $newAvatarElement = $(`
                            <div class="member-avatar-wrapper me-2 mb-2"
                                data-member-id="${newMemberData.id}"
                                data-member-name="${newMemberData.name}"
                                data-member-email="${newMemberData.email || ''}"
                                data-avatar-url="${newMemberData.avatar_url}"
                                data-initials="${newMemberData.initials}"
                                data-project-id="${projectId}"
                                style="cursor: pointer;">
                                ${newMemberData.avatar_url ?
                                    `<img src="${avatarSrc}" alt="${newMemberData.name}" class="member-avatar rounded-circle">` :
                                    `<div class="member-avatar member-avatar-placeholder rounded-circle d-flex align-items-center justify-content-center text-white fw-bold">${newMemberData.initials}</div>`
                                }
                            </div>
                        `);

                            $('.project-members-avatars').append($newAvatarElement); // Append to the avatars list
                            $('.project-members-section h4').text(`Project Members (Total: ${totalMembersCount})`); // Update total count

                            // Re-attach modal hover event listener for the newly added avatar
                            // This assumes the hover logic is robust enough to re-attach or uses event delegation
                            // If you use direct binding (like previously), you need to re-bind it:
                            attachAvatarHoverEvents($newAvatarElement); // Call a function to re-attach events

                        } else {
                            alert(response.message || 'Failed to add member.');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Display validation errors
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        alert(errorMessage);
                        console.error('Error adding member:', xhr.responseText);
                    }
                });
            });

            // --- Initial attachment of events to existing avatars ---
            attachAvatarHoverEvents($('.member-avatar-wrapper'));

            // --- Keep Modal Open if Mouse Enters Modal (Existing Logic) ---
            $('#memberDetailsModal').on('mouseenter', function() {
                clearTimeout(hideTimeout); // Clear any pending hide
            });

            $('#memberDetailsModal').on('mouseleave', function() {
                // If mouse leaves modal, hide it after a short delay
                hideTimeout = setTimeout(function() {
                    $('#memberDetailsModal').modal('hide');
                }, 200);
            });

            function attachAvatarHoverEvents($elements) {
                let hoverTimeout;
                let hideTimeout;
                const modalId = '#memberDetailsModal';
                const $modal = $(modalId);

                $elements.off('mouseenter mouseleave'); // Remove existing handlers to prevent duplicates

                $elements.on('mouseenter', function() {
                    clearTimeout(hideTimeout);
                    $('#removeMemberButton').prop('disabled', false);
                    const $this = $(this);
                    let pmId = '{{ auth()->user()->id }}';
                    const memberData = {
                        id: $this.data('member-id'),
                        name: $this.data('member-name'),
                        email: $this.data('member-email'),
                        avatarUrl: $this.data('avatar-url'),
                        initials: $this.data('initials'),
                        projectId: $this.data('project-id')
                    };
                    hoverTimeout = setTimeout(function() {
                        // Populate modal content
                        if (memberData.avatarUrl && memberData.avatarUrl !== 'null') { // Check for 'null' string if PHP renders it that way
                            $('#modalMemberAvatar').attr('src', memberData.avatarUrl).removeClass('d-none');
                            $('.modal-member-avatar-placeholder').addClass('d-none');
                        } else {
                            $('#modalMemberAvatar').addClass('d-none');
                            $('.modal-member-avatar-placeholder').text(memberData.initials).removeClass('d-none');
                        }
                        $('#modalMemberName').text(memberData.name);
                        $('#modalMemberEmail').text(memberData.email);
                        $('#modalMemberId').val(memberData.id);
                        $('#modalProjectId').val(memberData.projectId);
                        if (pmId === memberData.id) {
                            $('#removeMemberButton').prop('disabled', true);
                        }

                        $modal.modal('show');
                    }, 300);
                });

                $elements.on('mouseleave', function() {
                    clearTimeout(hoverTimeout);
                    hideTimeout = setTimeout(function() {
                        if (!$modal.is(':hover')) {
                            $modal.modal('hide');
                        }
                    }, 200);
                });
            }

            $('#removeMemberButton').on('click', function() {
                const memberId = $('#modalMemberId').val();
                const projectId = $('#modalProjectId').val();

                if (!confirm('Are you sure you want to remove this member from the project?')) {
                    return;
                }

                $.ajax({
                    url: `/v1/project-management/${projectId}/remove-member/${memberId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message || 'Member removed successfully!');
                            location.reload();
                            $('#memberDetailsModal').modal('hide');

                            $(`.member-avatar-wrapper[data-member-id="${memberId}"]`).remove();
                            const currentTotal = $('.project-members-avatars .member-avatar-wrapper').length;
                            $('.project-members-section h4').text(`Project Members (Total: ${currentTotal})`);

                        } else {
                            alert(response.message || 'Failed to remove member.');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                        console.error('Error removing member:', xhr.responseText);
                    }
                });
            });


            let hoverTimeout;
            let hideTimeout;
            const modalId = '#memberDetailsModal';
            const $modal = $(modalId);

            // Function to show the modal with a delay
            function showMemberModal(memberData) {
                // Populate modal content
                if (memberData.avatarUrl) {
                    $('#modalMemberAvatar').attr('src', memberData.avatarUrl).removeClass('d-none');
                    $('.modal-member-avatar-placeholder').addClass('d-none');
                } else {
                    $('#modalMemberAvatar').addClass('d-none');
                    $('.modal-member-avatar-placeholder').text(memberData.initials).removeClass('d-none');
                }
                $('#modalMemberName').text(memberData.name);
                $('#modalMemberEmail').text(memberData.email);
                $('#modalMemberPosition').text(memberData.position);
                $('#modalMemberId').val(memberData.id);
                $('#modalProjectId').val(memberData.projectId);

                // Show the modal
                $modal.modal('show');
            }

            // Function to hide the modal with a delay
            function hideMemberModal() {
                $modal.modal('hide');
            }

            // --- Hover Event Handlers for Avatars ---
            $('.member-avatar-wrapper').on('mouseenter', function() {
                clearTimeout(hideTimeout); // Clear any pending hide
                const $this = $(this);
                const memberData = {
                    id: $this.data('member-id'),
                    name: $this.data('member-name'),
                    email: $this.data('member-email'),
                    position: $this.data('member-position'),
                    avatarUrl: $this.data('avatar-url'),
                    initials: $this.data('initials'),
                    projectId: $this.data('project-id')
                };

                // Set a timeout to show the modal (e.g., after 300ms)
                hoverTimeout = setTimeout(function() {
                    showMemberModal(memberData);
                }, 300); // Adjust delay as needed
            });

            $('.member-avatar-wrapper').on('mouseleave', function() {
                clearTimeout(hoverTimeout); // Clear any pending show
                // Set a timeout to hide the modal (e.g., after 200ms)
                // This is crucial to allow moving the mouse from avatar to modal without it disappearing
                hideTimeout = setTimeout(function() {
                    // Only hide if the mouse is not over the modal itself
                    if (!$modal.is(':hover')) {
                        hideMemberModal();
                    }
                }, 200); // Adjust delay as needed
            });

            // --- Keep Modal Open if Mouse Enters Modal ---
            $modal.on('mouseenter', function() {
                clearTimeout(hideTimeout); // If mouse enters modal, prevent it from hiding
            });

            $modal.on('mouseleave', function() {
                // If mouse leaves modal, hide it after a short delay
                hideTimeout = setTimeout(function() {
                    hideMemberModal();
                }, 200); // Adjust delay as needed
            });



        });
    </script>
    @endsection