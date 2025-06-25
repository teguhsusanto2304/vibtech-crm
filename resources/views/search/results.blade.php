@extends('layouts.app') {{-- Assuming you have a main application layout --}}

@section('content')
    <div class="container mt-4">
        <h4 class="mb-4">Search Results for: "<span class="text-primary">{{ $query }}</span>"</h4>

        @if (empty($results) || (empty($results['projects']) && empty($results['tasks']) && empty($results['users']) && empty($results['job_requisitions'])))
            <div class="alert alert-info" role="alert">
                No results found for your query.
            </div>
        @else
            {{-- Projects Section --}}
            @if (!empty($results['projects']) && $results['projects']->count() > 0)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-folder me-2"></i> Projects
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($results['projects'] as $project)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">
                                            <a href="{{ route('v1.project-management.detail', ['project'=>$project->obfuscated_id ?? $project->id]) }}" class="text-decoration-none">
                                                {{ $project->name }}
                                            </a>
                                        </h5>
                                        <small class="text-muted">{{ Str::limit($project->description, 100) }}</small>
                                    </div>
                                    <span class="badge bg-secondary rounded-pill">{{ $project->projectMembers->count() ?? 0 }} Members</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer">
                        {{ $results['projects']->links('pagination::bootstrap-5') }} {{-- Use Bootstrap 5 pagination style --}}
                    </div>
                </div>
            @endif

            {{-- Tasks Section --}}
            @if (!empty($results['tasks']) && $results['tasks']->count() > 0)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-tasks me-2"></i> Tasks
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($results['tasks'] as $task)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">
                                            <a href="{{ route('v1.project-management.detail', ['project' => $task->projectStage->project->obfuscated_id ?? $task->projectStage->project->id, 'task' => $task->task_obfuscated_id ?? $task->id]) }}" class="text-decoration-none">
                                                {{ $task->name }}
                                            </a>
                                            <small class="text-muted ms-2">in Project: {{ $task->projectStage->project->name ?? 'N/A' }}</small>
                                        </h5>
                                        <small class="text-muted">{{ Str::limit($task->description, 100) }}</small>
                                        <br>
                                        <small class="text-muted">Due: {{ $task->end_at ? $task->end_at->format('d M Y') : 'N/A' }}</small>
                                    </div>
                                    <span class="badge {{ $task->task_status_badge ?? 'bg-secondary' }} rounded-pill">{{ $task->task_status ?? 'Unknown' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer">
                        {{ $results['tasks']->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

            {{-- Users Section --}}
            @if (!empty($results['users']) && $results['users']->count() > 0)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-user me-2"></i> Users
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($results['users'] as $user)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">
                                            <a href="" class="text-decoration-none">
                                                {{ $user->name }}
                                            </a>
                                        </h5>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                    @if ($user->position)
                                        <span class="badge bg-dark rounded-pill">{{ $user->position }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer">
                        {{ $results['users']->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

            {{-- Job Requisitions Section (Conditional, only if the model exists and results are found) --}}
            @if (isset($results['job_requisitions']) && !empty($results['job_requisitions']) && $results['job_requisitions']->count() > 0)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-file-contract me-2"></i> Job Requisitions
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($results['job_requisitions'] as $jr)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">
                                            <a href="{{ route('v1.job-assignment-form.list', $jr->id) }}" class="text-decoration-none">
                                                {{ $jr->title }}
                                            </a>
                                        </h5>
                                        <small class="text-muted">{{ Str::limit($jr->description, 100) }}</small>
                                    </div>
                                    {{-- Add any relevant badges or info for JR --}}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer">
                        {{ $results['job_requisitions']->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

        @endif
    </div>
@endsection