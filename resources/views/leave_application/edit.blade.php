@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
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
            

        <div class="card shadow-sm">
            <div class="card-header fw-bold">
                Manual Leave Application
            </div>

            <div class="card-body">
                <form action="{{ route('v1.leave-application.update', ['id' => $leaveApplication->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Country -->
                    <div class="mb-3">
                        <label class="form-label">Country</label>
                        <select name="country_code" class="form-select" required>
                        <option value="">-- Select Country --</option>
                        <option value="SG" {{ old('country_code', $leaveApplication->country_code ?? '') == 'SG' ? 'selected' : '' }}>Singapore</option>
                        <option value="MY" {{ old('country_code', $leaveApplication->country_code ?? '') == 'MY' ? 'selected' : '' }}>Malaysia</option>
                    </select>

                    </div>

                    <!-- Leave Date -->
                    <div class="mb-3">
                        <label class="form-label">Public Holiday Date</label>
                        <input type="date"
                            name="leave_date"
                            class="form-control"
                            value="{{ old('leave_date', optional($leaveApplication->leave_date)->format('Y-m-d') ?? '') }}"
                            required>

                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text"
                            name="title"
                            class="form-control"
                            value="{{ old('title', $leaveApplication->title ?? '') }}"
                            maxlength="150"
                            required>

                    </div>

                    <!-- Description -->
                    <div class="mb-3" style="display: none;">
                        <label class="form-label">Description (optional)</label>
                        <textarea name="description"
          class="form-control"
          maxlength="200">{{ old('description', $leaveApplication->description ?? '') }}</textarea>

                    </div>

                    <div class="text-end">
                        <button class="btn btn-primary">
                            Update Public Holiday
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
