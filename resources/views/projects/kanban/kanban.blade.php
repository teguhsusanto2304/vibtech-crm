@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/jkanban@1.3.1/dist/jkanban.min.css">
<script src="https://unpkg.com/jkanban@1.3.1/dist/jkanban.min.js"></script>

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
    <div class="p-6">
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
                                        <tr>
                                            <td class="detail-label">Project Phases :</td>
                                            <td class="detail-value text-muted">
                                                @php
                                                $currentPhase = \App\Models\ProjectPhase::find($selectedPhaseId);
                                                @endphp
                                                <div class="row">
                            
                                                                <p><label class="form-label col-3">Name  </label><label class="form-label col-3">: {{ $currentPhase->name }}</label><br>
                                                                <label class="form-label col-3"><small>Description </label><label class="form-label col-4">: {{ $currentPhase->description }}</small></label><br>
                                                                <label class="form-label col-3"><small>Due Date </label><label class="form-label col-4">: <span class="badge {{ $currentPhase->status_date_badge }} rounded-pill ms-2">
                                                                <i class="fas fa-clock me-1"></i> {{ $currentPhase->end_date ? $currentPhase->end_date->format('d M Y') : 'N/A' }}
                                                                </span></small></label><br>
                                                                <label class="form-label col-3"><small>Status </label><label class="form-label col-4">: <span class="badge bg-small {{ $currentPhase->phase_status_badge }} rounded-pill ms-2">{{ $currentPhase->phase_status }}</span></small>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
    <a href="{{ route('v1.projects.detail',['project'=>$project->obfuscated_id]) }}" class="btn btn-sm bg-secondary text-white mb-3" style="margin-left: 15px;">Back to Project Detail</a>
    <div id="kanban-board" class="overflow-x-auto">
        
    </div>
</div>

<!-- jKanban Styles and Script -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jkanban/1.2.0/jkanban.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jkanban/1.2.0/jkanban.min.js"></script>

<script>
    const kanbanBoards = [];
    const currentUserId = {{ auth()->id() ?? 'null' }};
    const projectManagerId = {{ $project->project_manager_id }};

    @foreach ($project->projectKanban as $kanban)
    @php
        // Example: Set color class based on task status
        $cardColor = match($kanban->data_status) {
            1 => 'bg-warning', // On Going
            2 => 'bg-info',    // Pending Review
            3 => 'bg-success',      // Overdue
            default => 'bg-danger',
        };
    @endphp
        kanbanBoards.push({
            id: 'kanban-{{ $kanban->id }}',
            title: '<label class="text-white">{{ $kanban->name }}</label>',
            class: '{{ $cardColor }}',
            item: [
                @foreach ($kanban->tasks as $task)
                    {
                        id: 'task-{{ $task->id }},{{ $task->assignedTo->id }}',
                        title: `<div class="p-2" 
                            data-eid="task-{{ $task->id }}" 
                            data-euser-id="user-{{ $task->assignedTo->id }}">
                                    <strong>{{ $task->name }}</strong>
                                    <p class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}</p>

                                    <div class="d-flex align-items-center mt-2">
                                        @if($task->assignedTo)
                                            <img src="{{ $task->assignedTo->avatar_url ?? asset('images/default-avatar.png') }}" 
                                                alt="{{ $task->assignedTo->name }}"
                                                class="rounded-circle me-2"
                                                width="24" height="24">
                                            <span class="text-xs text-gray-600">{{ $task->assignedTo->name }}</span>
                                        @else
                                            <span class="badge bg-secondary text-xs">Unassigned</span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-gray-600 mt-1">
                                        <small><span class="badge bg-info">{{ $task->projectStage->name ?? '-' }}</span></small>
                                    </p>

                                    <p class="text-xs text-gray-600 mt-1 mb-0">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <small><label class="text-success">{{ $task->start_at ? \Carbon\Carbon::parse($task->start_at)->format('d M Y') : 'No start' }}
                                        </label></small>
                                        - 
                                        <small><label class="text-danger">{{ $task->end_at ? \Carbon\Carbon::parse($task->end_at)->format('d M Y') : 'No end' }}
                                        </label></small>
                                    </p>
                                </div>`,
                    },
                    
                @endforeach
            ]
        });
    @endforeach

    const kanban = new jKanban({
    element: '#kanban-board',
    boards: kanbanBoards,
    gutter: '15px',
    widthBoard: '280px',
    dragBoards: false,
    dragItems: true,
    click: function (el) {
        alert("Task Clicked: " + el.innerText);
    },
    dropEl: async function (el, target, source, sibling) {
        try {
            const taskIdOri = el.getAttribute('data-eid').replace('task-', '');
            const taskIdSplit = taskIdOri.split(',');
            const taskId = taskIdSplit[0];
            const assignedId = taskIdSplit[1];
            const newStatusId = target.parentElement.getAttribute('data-id').replace('kanban-', '');
            const newBoardId = target.parentElement.getAttribute('data-id');


            // Get correct board ID (column)
            const boardElement = target.closest('.kanban-board');
            if (!boardElement) {
                console.error('Board element not found');
                return;
            }
            //const newStatusId = boardElement.getAttribute('data-id').replace('kanban-', '');

            // Send update to server
            setTimeout(async () => {
                if (assignedId === currentUserId  || projectManagerId===currentUserId) {
                    // User allowed: update DB
                    const response = await fetch(`/v1/projects/${taskId}/tasks/move`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ data_status: newStatusId })
                    });

                    if (!response.ok) {
                        alert('Failed to update task');
                        KanbanBoard.addElement(prevBoardId, el.outerHTML);
                        el.remove();
                    }
                } else {
                    alert('You can\'t move this card — it is not your task.');
                    // Move card back to old column
                    KanbanBoard.addElement(prevBoardId, el.outerHTML);
                    el.remove();
                }
            }, 0);
            

        } catch (error) {
            console.error('Error moving task:', error);

            // Optional: revert move on failure
            location.reload();
        }
    }
});

</script>
@endsection
