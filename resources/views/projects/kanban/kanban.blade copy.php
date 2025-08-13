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
        <h2 class="text-2xl font-bold mb-4">{{ $project->name }} - Kanban</h2>

        <div id="myKanban" class="kanban-board"></div>
    </div>

    <script>
    const kanbanBoards = [];

    @foreach ($project->phases as $phase)
        @foreach ($phase->projectStages as $stage)
            kanbanBoards.push({
                id: 'stage-{{ $stage->id }}',
                title: '{{ $stage->kanban_stage_id }}', // or $stage->name
                item: [
                    @foreach ($stage->tasks as $task)
                        {
                            id: 'task-{{ $task->id }}',
                            title: `<div class="p-2 bg-white shadow rounded">
                                        <strong>{{ $task->name }}</strong>
                                        <p class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}</p>
                                    </div>`
                        },
                    @endforeach
                ]
            });
        @endforeach
    @endforeach

    const kanban = new jKanban({
        element: '#myKanban',
        boards: kanbanBoards,
        gutter: '15px',
        widthBoard: '300px',
        dragBoards: false,
        dragItems: true,
        click: function (el) {
            alert("Task Clicked: " + el.innerText);
        },
        dropEl: function (el, target, source, sibling) {
            const taskId = el.getAttribute('data-eid').replace('task-', '');
            const newStageId = target.parentElement.getAttribute('data-id').replace('stage-', '');

            // Send Ajax request to update task stage in backend
            fetch(`/api/kanban/task/${taskId}/move`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    project_stage_id: newStageId
                })
            }).then(response => response.json())
              .then(data => console.log(data));
        }
    });
</script>


</div>
@endsection