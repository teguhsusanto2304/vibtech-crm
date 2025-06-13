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
      <button class="btn btn-success" disabled>Complete Entire Project</button>     
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
          <img src="{{ $project->projectManager->avatar_url }}" alt="Project Manager Avatar" 
          title="{{ $project->projectManager->name }}"
          class="rounded-circle me-2" data-bs-toggle="tooltip"
                 data-bs-placement="top"
                 width="40" height="40">
        </div>

        <!-- Project Members -->
        <div>
          <p class="mb-2"><strong>Project Members (Total: {{ $project->projectMembers()->count() }}) </strong></p>
          <div class="d-flex align-items-center gap-2">
            @foreach($project->showProjectMembers as $projectMember)

            <img src="{{ $projectMember->member->avatar_url }}"
                 alt="{{ $projectMember->member->name }}'s avatar" {{-- Improved alt text --}}
                 class="rounded-circle"
                 data-bs-toggle="tooltip"
                 data-bs-placement="top"
                 width="40" height="40"
                 title="{{ $projectMember->member->name }}">
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <hr>

    <div class="horizontal-scroll-wrapper"> {{-- Custom wrapper for overflow --}}
    <div class="row flex-nowrap g-4">
    @foreach($kanbanStages as $kanbanStage)
        <div class="col">
            <div class="card h-100y position-relative border border-dark @if($kanbanStage->id > 2) card-disabled @endif"> {{-- h-100 to make cards in a row have equal height --}}
                <div class="card-body d-flex flex-column"> {{-- d-flex flex-column to make content inside vertical --}}
                    <div class="d-flex justify-content-between align-items-center mb-3"> {{-- Flex container for text and buttons --}}
                        <p class="card-text mb-0 flex-grow-1 me-3">{{ $kanbanStage->name }}</p> {{-- Text takes available space --}}
                        <div class="btn-group btn-group-sm btn-group-vertical" role="group" aria-label="Kanban Actions"> {{-- No vertical class --}}
                            <button type="button" class="btn btn-outline-primary btn-sm">Create</button>
                            @if($project->project_manager_id == auth()->user()->id)
                            <button type="button" class="btn btn-outline-success btn-sm">Complete</button>
                            @endif
                        </div>
                    </div>
                    <strong class="text-primary">@if($kanbanStage->id > 2) Inactive @else Active @endif</strong>
                </div>
            </div>
        </div>
    @endforeach
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
    progressBar({{ rand(0, 100) }},'#workProgress','%',{{ rand(0, 100) }})
    
    </script>
</div>

    </div>
</div>
@endsection
