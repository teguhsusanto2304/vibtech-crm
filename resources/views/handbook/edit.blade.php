@extends('layouts.app')

@section('title', 'Edit Employee Handbook')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                @foreach ($breadcrumb as $item)
                    <li class="breadcrumb-item">
                        @if($item == 'Job Assignment Form')
                            <a href="{{ route('v1.job-assignment-form') }}">{{ $item }}</a>
                        @else
                            <a href="javascript:void(0);">{{ $item }}</a>
                        @endif
                        <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                    </li>
                @endforeach
            </ol>
        </nav>

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

        <div class="card">
            <div class="card-body">
                
                    @if (Route::currentRouteName() === 'v1.staff-resources.edit') 
                    <form class="row g-3"
                      action="{{ route('v1.staff-resources.update', $handbook->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @endif
                    @if (Route::currentRouteName() === 'v1.employee-handbooks.edit') 
                    <form class="row g-3"
                      action="{{ route('v1.employee-handbooks.update', $handbook->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @endif
                    @csrf
                    @method('PUT')

                    <div class="col-md-8">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title"
                               value="{{ old('title', $handbook->title) }}"
                               placeholder="Enter title">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="description"
                               value="{{ old('description', $handbook->description) }}"
                               placeholder="Enter description">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">PDF File (Optional)</label>
                        <input type="file" class="form-control" name="path_file" accept=".pdf">
                        @if ($handbook->path_file)
                            <small>Current File: <a href="{{ asset($handbook->path_file) }}" target="_blank">View PDF</a></small>
                        @endif
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('v1.employee-handbooks.list') }}" class="btn btn-warning">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
