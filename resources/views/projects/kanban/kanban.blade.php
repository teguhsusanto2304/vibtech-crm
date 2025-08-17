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
        <div class="col-md-12 mb-4">
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
<style>
    /*
     * The main container. We set this to display flex and allow
     * horizontal scrolling.
     */
    #kanban-board {
        overflow-x: auto;
        overflow-y: hidden; /* Hide vertical overflow */
        padding: 1rem 0;
        gap: 1rem;
        /* The jkanban library itself sets a flex display,
           but it's good practice to ensure it here if needed.
           However, the key is the overflow property.
         */
    }

    /*
     * This targets the individual kanban board columns.
     * We ensure they do not shrink and maintain a fixed width.
     * The library sets a class `kanban-container` which is the direct parent of all boards.
     * Let's target the boards directly.
     */
    .kanban-board {
        /* Prevents the boards from shrinking below their width */
        flex-shrink: 0;
        width: 280px; /* Or a width of your choice */
    }

    /*
     * Optional: Better scrollbar styling for a cleaner look.
     */
    #kanban-board::-webkit-scrollbar {
        height: 8px;
    }
    #kanban-board::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }
    #kanban-board::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
    }
</style>
{!! $styleKanbanCard !!}

        <div class="col-md-12 mb-4">
            <div id="kanban-board"></div>
        </div>
        
    </div>
</div>

<!-- Task Detail Modal -->
<div class="modal fade" id="task-modal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modal-task-title">Judul Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                <p id="modal-task-description" class="text-muted"></p>
                <div class="d-flex align-items-center mb-2">
                     <label class="col-md-3">Assigned To :</label>
                    <p id="modal-task-assigned" class="mb-0 fw-semibold"></p>
                </div>
                <div class="d-flex align-items-center mb-2">
                     <label class="col-md-3">Stage On :</label>
                    <p id="modal-task-dates" class="mb-0 fw-semibold"></p>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <label class="col-md-3">Start - End Date :</label>
                    <p id="modal-task-stage" class="mb-0 fw-semibold"></p>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer">

                <button type="button" id="delete-task-button" class="btn btn-danger">
                    <i class="bi bi-trash-fill me-1"></i> Remove
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- jKanban Styles and Script -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jkanban/1.2.0/jkanban.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jkanban/1.2.0/jkanban.min.js"></script>


<script>
    const kanbanBoards = [];
    const currentUserId = {{ auth()->id() ?? 'null' }};
    const currentUserName = '{{ auth()->user()->name ?? 'null' }}';
    const projectManagerId = {{ $project->project_manager_id }};
    const projectKanbans = {{ Js::from($project->projectKanban) }};
    let secondLastProjectKanbanId = 'null';

    // Logika untuk mendapatkan item kedua dari bawah
    if (projectKanbans.length >= 2) {
        // Menggunakan slice(-2, -1) untuk mendapatkan array dengan satu elemen (item kedua terakhir)
        const secondLastKanban = projectKanbans.slice(-2, -1)[0];
        secondLastProjectKanbanId = secondLastKanban.id;
    }
    const lastProjectKanbanId = {{ $project->projectKanban->last()->id ?? 'null' }};

    @foreach ($project->projectKanban as $idx => $kanban)
    
        kanbanBoards.push({
            id: 'kanban-{{ $kanban->id }}',
            title: '<label class="text-white">{{ $kanban->name }} </label>',
            class: 'kanban{{ $idx+1 }}',
            item: [
                @foreach ($kanban->tasks as $task)
                    {
                        // Pastikan data yang dibutuhkan ada di id atau data-* atribut
                        id: 'task-{{ $task->id }},{{ $task->assigned_to_user_id }}',
                        title: `<div class="p-2" 
                            data-eid="task-{{ $task->id }},{{ $task->assigned_to_user_id }}" 
                            data-assigned-id="{{ $task->assignedTo->id ?? 'null' }}"
                            data-etask-name="{{ $task->name }}"
                                    data-task-description="{{ $task->description }}"
                                    data-task-assigned-name="{{ $task->assignedTo->name ?? 'Unassigned' }}"
                                    data-task-start="{{ $task->start_at ? \Carbon\Carbon::parse($task->start_at)->format('d M Y') : 'No start' }}"
                                    data-task-end="{{ $task->end_at ? \Carbon\Carbon::parse($task->end_at)->format('d M Y') : 'No end' }}"
                                    data-task-stage="{{ $task->projectStage->name ?? '-' }}">
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
            const taskIdOri = el.getAttribute('data-eid').replace('task-', '');
            const taskIdSplite = taskIdOri.split(',');
            const taskId = taskIdSplite[0];
            const fullText = el.innerText;
            const lines = fullText.split('\n'); // Memecah berdasarkan baris baru
            const titleLine = lines[0]; // Baris pertama
            const descriptionLine = lines[1]; // Baris kedua
            const taskAssignedLine = lines[4];
            // Isi modal dengan data task
            document.getElementById('modal-task-title').innerText = titleLine;
            document.getElementById('modal-task-description').innerText = descriptionLine;
            document.getElementById('modal-task-assigned').innerText = taskAssignedLine;
            document.getElementById('modal-task-dates').innerText = lines[6];
            document.getElementById('modal-task-stage').innerText = lines[8];

            // Set data-task-id pada tombol delete untuk digunakan nanti
            document.getElementById('delete-task-button').disabled = true;
            document.getElementById('delete-task-button').setAttribute('data-task-id', taskId);
            if (currentUserName==taskAssignedLine)
            {
                document.getElementById('delete-task-button').disabled = false;
            }

            // Tampilkan modal
            const taskModal = new bootstrap.Modal(document.getElementById('task-modal'));
            taskModal.show();
        },
        dropEl: async function (el, target, source, sibling) {
            try {
                const taskIdOri = el.getAttribute('data-eid').replace('task-', '');
                const taskIdSplite = taskIdOri.split(',');
                const taskId = taskIdSplite[0];
                const assignedId = taskIdSplite[1];
                const prevBoardId = source.getAttribute('data-eid');
                
                const boardElement = target.closest('.kanban-board');
                if (!boardElement) {
                    console.error('Board element not found');
                    return;
                }
                const newStatusId = boardElement.getAttribute('data-id').replace('kanban-', '');
                // --- Kunci Logika Role-Based Access ---
                if (projectManagerId == currentUserId) {
                    if(newStatusId==lastProjectKanbanId || newStatusId==secondLastProjectKanbanId){
                        const response = await fetch(`/v1/projects/${taskId}/tasks/move`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ data_status: newStatusId })
                        });
                        
                        if (!response.ok) {
                            alert('Failed to update task. The card will be moved back. mgr');
                            //kanban.addElement(prevBoardId, el.outerHTML);
                            //el.remove();
                        }
                    } else {
                        alert('You can\'t move this card — only In review cards move to complete and from completed back to in review');
                        location.reload();
                    }
                    
                } else if (assignedId == currentUserId && newStatusId!=lastProjectKanbanId) {
                        const response = await fetch(`/v1/projects/${taskId}/tasks/move`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ data_status: newStatusId })
                        });
                        
                        if (!response.ok) {
                            alert('Failed to update task. The card will be moved back.');
                            kanban.addElement(prevBoardId, el.outerHTML);
                            el.remove();
                        }
                    
                }  else {
                    if (newStatusId!=lastProjectKanbanId){
                        alert('You can\'t move this card — it is not your task');
                    } else {
                        alert('You can\'t move this card — only project manager allowed complete your card');
                    }
                    
                    kanban.addElement(prevBoardId, el.outerHTML);
                    el.remove();
                }
            } catch (error) {
                console.error('Error moving task:', error);
                location.reload();
            }
        }
    });

</script>
@endsection
