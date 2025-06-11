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
                <form action="{{ route('v1.project-management.store') }}" method="POST" class="p-4">
                    @csrf
                    <!-- Project Name -->
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <label for="projectName" class="form-label">Project Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter project name">
                        </div>
                    </div>

                    <!-- Project Start Date & End Date -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="projectStartDate" class="form-label">Start Date</label>
                            <input type="date" name="start_at" id="start_at" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="projectEndDate" class="form-label">End Date</label>
                            <input type="date" name="end_at" id="end_at" class="form-control">
                        </div>
                    </div>

                    <!-- Project Description -->
                    <div class="mb-3">
                        <label for="projectDescription" class="form-label">Project Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control" placeholder="Enter description..."></textarea>
                    </div>

                    <!-- Members and Manager -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="addProjectMembers" class="form-label">Add Project Members</label>
                            <select name="addProjectMembers[]" id="addProjectMembers" class="form-select" multiple="multiple">
                                        <!-- Removed 'disabled selected' from here as it conflicts with multiple select -->
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project Manager</label>
                            <div class="d-flex align-items-center p-2 border rounded bg-white">
                                <img src="{{ asset(auth()->user()->path_image) }}" alt="Manager" class="rounded-circle me-2" width="40" height="40">
                                <span class="fw-semibold text-dark">You</span>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-12">
                            <a href="{{ route('v1.project-management')}}" class="btn btn-warning">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Project</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- jQuery (Select2 depends on jQuery) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Select2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Initialize Select2 on your select element
        $(document).ready(function() {
            $('#addProjectMembers').select2({
                placeholder: "Select project members", // Custom placeholder text
                allowClear: true // Option to clear all selections
            });
        });

        // Custom Tailwind classes for form elements (from previous code)
        // Ensure these are applied to your form inputs
        // This is a simplified version; in a real app, you'd apply them more robustly
        // via component classes or directives.
        document.querySelectorAll('.form-input').forEach(input => {
            input.classList.add('block', 'w-full', 'px-3', 'py-2', 'border', 'border-gray-300', 'rounded-md', 'shadow-sm', 'focus:outline-none', 'focus:ring-blue-500', 'focus:border-blue-500', 'sm:text-sm');
        });
        document.querySelectorAll('.form-label').forEach(label => {
            label.classList.add('block', 'text-sm', 'font-medium', 'text-gray-700', 'mb-1');
        });
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.classList.add('px-4', 'py-2', 'bg-blue-600', 'text-white', 'font-semibold', 'rounded-md', 'hover:bg-blue-700', 'focus:outline-none', 'focus:ring-2', 'focus:ring-offset-2', 'focus:ring-blue-500', 'transition', 'ease-in-out', 'duration-150');
        });
        document.querySelectorAll('.btn-secondary').forEach(btn => {
            btn.classList.add('px-4', 'py-2', 'text-gray-700', 'font-semibold', 'rounded-md', 'hover:bg-gray-100', 'focus:outline-none', 'focus:ring-2', 'focus:ring-offset-2', 'focus:ring-gray-300', 'transition', 'ease-in-out', 'duration-150');
        });
        document.querySelectorAll('.profile-img').forEach(img => {
            img.classList.add('w-10', 'h-10', 'rounded-full', 'object-cover', 'mr-2');
        });
    </script>
@endsection
