@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                    title="{{ $project->work_progress_percentage==100 ? 'Mark this project as complete' : 'All stages must be completed first' }}" {{-- Dynamic tooltip --}}
                    >
                Complete Entire Project
            </button>
            @endif
        @endcan 
        </div>
    </div>
    

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
                    <td class="detail-value text-muted"><div id="dayProgress"></div></td>
                <tr>
                    <tr>
                    <td class="detail-label">Progress:</td>
                    <td class="detail-value text-muted"><div id="workProgress"></div></td>
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
        </div>
      </div>
    </div>

    <hr>

    <div class="horizontal-scroll-wrapper"> {{-- Custom wrapper for overflow --}}
    <div class="row flex-nowrap g-4">
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
                $labelStatus ='Complete';
                $createTask = 'disabled';
                $completeStage = 'disabled';
            } 
        }
        
        @endphp
        <div class="col">
            <div class="card h-100y position-relative border border-dark {{ $cardClasses }}"> {{-- h-100 to make cards in a row have equal height --}}
                <div class="card-body d-flex flex-column"> {{-- d-flex flex-column to make content inside vertical --}}
                    <div class="d-flex justify-content-between align-items-center mb-3"> {{-- Flex container for text and buttons --}}
                        <p class="card-text mb-0 flex-grow-1 me-3">{{ $kanbanStage->name }}</p> {{-- Text takes available space --}}
                        <div class="btn-group btn-group-sm btn-group-vertical" role="group" aria-label="Kanban Actions"> {{-- No vertical class --}}
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
                                {{ $createTask }}
                                >Create</button>
                           @if($project->project_manager_id == auth()->user()->id)
                            @if($canCompleteStage) {{-- Use the pre-calculated $canCompleteStage variable --}}
                                <button type="button" class="btn btn-outline-success btn-sm mark-stage-complete-btn"
                                        data-project-id="{{ $project->obfuscated_id }}"
                                        data-project-stage-id="{{ $currentProjectStage->obfuscated_id }}" {{-- Pass obfuscated project stage ID --}}
                                        data-kanban-stage-id="{{ $kanbanStage->id }}" {{-- Also useful for UI updates --}}
                                        {{ $completeStage }}
                                        >Complete</button>
                            @else
                                {{-- Optionally show a disabled button with a tooltip explaining why --}}
                                <button type="button" class="btn btn-outline-secondary btn-sm mark-stage-complete-btn"
                                        data-project-id="{{ $project->obfuscated_id }}"
                                        data-project-stage-id="{{ $currentProjectStage->obfuscated_id??'-' }}" {{-- Pass obfuscated project stage ID --}}
                                        data-kanban-stage-id="{{ $kanbanStage->id }}" {{-- Also useful for UI updates --}}
                                        {{ $completeStage }}
                                        >Complete</button>
                            @endif
                        @endif
                        </div>
                    </div>
                    <small class="text-{{ $fontClass }}">{{ $labelStatus }}</small>
                </div>
                    @php
                        // Fetch tasks for this specific project's stage
                        // Assuming you have $project->projectStages eager loaded with tasks
                        $projectStageTasks = $project->projectStages
                            ->where('kanban_stage_id', $kanbanStage->id)
                            ->first()
                            ?->tasks ?? collect();
                    @endphp
                    <ul class="list-group list-group-flush">
                    @foreach($projectStageTasks as $task) 
                    <li class="list-group-item">             
                        <div class="card-body  d-flex flex-column" data-task-id="{{ $task->id }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="task-name mb-1">{{ $task->name }}</p>
                            </div>
                            
                            @if($task->description)
                                <p class="task-description text-muted small mb-1">{{ Str::limit($task->description, 50) }}</p>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                @if($task->start_date)
                                    <small class="text-info me-auto">Due: {{ $task->start_date->format('d M Y') }}</small>
                                @endif
                                
                                @if($task->assignedTo)
                                <span class="task-status-badge badge rounded-pill badge-sm {{ 
                                    $task->data_status == 2 ? 'bg-success' : 
                                    ($task->data_status == 1 ? 'bg-primary' : 
                                    ($task->data_status == 0 ? 'bg-danger' : 'bg-secondary'))
                                }} ms-2"><small>{{ Str::title($task->data_status==1 ? 'On Progress':'Completed') }}</small></span>
                                    <div class="task-assignee-wrapper ms-auto">
                                        <img src="{{ $task->assignedTo->avatar_url }}"
                                            alt="{{ $task->assignedTo->name }}"
                                            class="task-assignee-avatar rounded-circle"
                                            width="28" height="28"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="{{ $task->assignedTo->name }}">
                                    </div>
                                @endif
                            </div>
                        </div>
                        </li>
                    @endforeach
                    </ul>
            </div>
        </div>
    @endforeach
</div>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaskModalLabel">Create New Task for <span id="modalStageName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createTaskForm">
                <div class="modal-body">
                    <input type="hidden" name="project_id" id="modalProjectId">
                    <input type="hidden" name="kanban_stage_id" id="modalKanbanStageId">
                    
                    <div class="mb-3">
                        <label for="taskName" class="form-label">Task Name</label>
                        <input type="text" class="form-control" id="taskName" name="name" required placeholder="e.g., Design UI Mockups">
                    </div>

                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="taskDescription" name="description" rows="3" placeholder="Detailed description of the task..."></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="taskStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="taskStartDate" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="taskEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="taskEndDate" name="end_date">
                        </div>
                        <div class="col-md-6">
                            <label for="taskAssignee" class="form-label">Assign To</label>
                            <select class="form-select" id="taskAssignee" name="assigned_to_user_id">
                                <option value="">Select User (Optional)</option>
                                @foreach($project->showProjectMembers as $user) {{-- Assuming $users is passed to this view --}}
                                    <option value="{{ $user->member->id }}"
                                            data-avatar-url="{{ $user->member->avatar_url }}"
                                            data-initials="{{ $user->member->name}}">
                                        {{ $user->member->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    {{-- Initial status can be set here, or hardcoded in backend --}}
                    <input type="hidden" name="status" value="pending">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Custom CSS for the horizontal scroll wrapper */
    .horizontal-scroll-wrapper {
        overflow-x: auto; /* Enable horizontal scrolling */
        overflow-y: hidden; /* Hide vertical scrollbar if it appears (optional) */
        padding-bottom: 1rem; /* Add some padding at the bottom for the scrollbar to not cut off content */
        /* Optional: Add some padding-left/right if you want space before/after the first/last card */
        /* padding-left: 15px; */
        /* padding-right: 15px; */
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
    function progressBar(percentage,aClass,aSymbol,aValue)
    {
if (percentage < 30) {
                                progressBarClass = 'bg-danger'; // Red for low progress
                            } else if (percentage < 70) {
                                progressBarClass = 'bg-warning'; // Yellow for medium progress
                            } else {
                                progressBarClass = 'bg-success'; // Green for high progress
                            }

                            $(aClass).append( `
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

    progressBar({{ round(($project->remaining_days/$project->start_at->diffInDays($project->end_at))*100) }},'#dayProgress','d',{{ $project->remaining_days }})
    progressBar({{ $project->work_progress_percentage }},'#workProgress','%',{{ $project->work_progress_percentage }})
    
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
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
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

<div class="modal fade" id="memberDetailsModal" tabindex="-1" aria-labelledby="memberDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <img id="modalMemberAvatar" src="" alt="Member Avatar" class="modal-member-avatar rounded-circle mb-3">
                <div class="modal-member-avatar-placeholder mb-3 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold d-none"></div>
                <h5 id="modalMemberName" class="mb-1"></h5>
                <p id="modalMemberEmail" class="text-muted small"></p>
                <p id="modalMemberPosition" class="text-muted small"></p>

                <hr class="my-3">

                @if($project->project_manager_id == auth()->user()->id) {{-- Only project manager can remove --}}
                    <button type="button" class="btn btn-danger btn-sm w-100" id="removeMemberButton">Remove from Project</button>
                    <input type="hidden" id="modalMemberId">
                    <input type="hidden" id="modalProjectId">
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

        // Event listener for when the "Create Task" modal is about to be shown
        $('#createTaskModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); // Button that triggered the modal
            const projectId = button.data('project-id');
            const kanbanStageId = button.data('kanban-stage-id');
            const kanbanStageName = button.data('kanban-stage-name');

            // Populate modal hidden inputs
            $('#modalProjectId').val(projectId);
            $('#modalKanbanStageId').val(kanbanStageId);
            
            // Populate modal title
            $('#modalStageName').text(kanbanStageName);

            // Optional: Clear previous form data when opening modal
            $('#createTaskForm')[0].reset();
            $('#taskAssignee').val(null).trigger('change'); // Clear Select2
        });

        // Handle the "Create Task" form submission via AJAX
        $('#createTaskForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const $form = $(this);
            const formData = $form.serializeArray(); // Get form data as array of objects
            const projectId = $('#modalProjectId').val(); // Get obfuscated project ID
            const kanbanStageId = $('#modalKanbanStageId').val(); // Get kanban stage ID

            // Add CSRF token manually
            formData.push({ name: '_token', value: $('meta[name="csrf-token"]').attr('content') });

            // Send AJAX request
            $.ajax({
                url: `/v1/project-management/${projectId}/stages/${kanbanStageId}/tasks`, // API endpoint to create task
                type: 'POST',
                data: $.param(formData), // Serialize array to URL-encoded string
                success: function(response) {
                    if (response.success) {
                        alert(response.message || 'Task created successfully!');
                        $('#createTaskModal').modal('hide'); // Close modal

                        // Optional: Dynamically add the new task to the appropriate kanban card in the UI
                        // You'll need to decide how to render individual tasks (e.g., a small card, a list item)
                        // For example:
                        // const taskId = response.task.id;
                        // const taskName = response.task.name;
                        // const taskStatus = response.task.status;
                        // const taskAssigneeName = response.task.assigned_to?.name || 'Unassigned';
                        // const taskAssigneeAvatar = response.task.assigned_to?.avatar_url || 'placeholder_url'; // Ensure backend sends this

                        // Append a representation of the new task to the correct stage's task list
                        // $(`#tasks-for-stage-${kanbanStageId}`).append(`
                        //     <div class="task-item border-bottom pb-2 mb-2">
                        //         <small><strong>${taskName}</strong> - (${taskStatus})</small><br>
                        //         <small>Assigned: ${taskAssigneeName}</small>
                        //     </div>
                        // `);

                        // If your task list is complex (e.g., sortable), you might need to reload tasks for that stage.
                        // For simplicity, a page refresh might be acceptable for now
                        location.reload(true); // Consider a partial refresh or specific component update instead
                        
                    } else {
                        alert(response.message || 'Failed to create task.');
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
                let pmId = {{ auth()->user()->id }};
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
                    if(pmId===memberData.id){
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
