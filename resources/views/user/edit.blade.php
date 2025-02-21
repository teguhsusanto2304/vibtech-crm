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
            <div class="card">
                <div class="card-body">
                    <form class="row g-3"
      action="{{ route('v1.users.update', $emp->id) }}"
      method="post"
      enctype="multipart/form-data">
    @csrf
    @if(isset($emp))
        @method('PUT') {{-- Laravel requires PUT for updates --}}
    @endif

    <div class="col-md-8">
        <label for="staffName" class="form-label">Staff Name - {{ $emp->name }}</label>
        <input type="text" class="form-control" name="name" value="{{ $emp->name ?? '' }}" placeholder="Enter staff name">
    </div>

    <div class="col-md-4">
        <label for="staffID" class="form-label">Staff ID</label>
        <input type="text" class="form-control" value="{{ $emp->user_number ?? '' }}" placeholder="Enter staff ID" readonly>
    </div>

    <div class="col-md-6">
        <label for="department" class="form-label">Department</label>
        <select class="form-control" name="department">
            <option value="">Choose a Department</option>
            <option value="Marketing" {{ ($emp->department ?? '') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
            <option value="Sales" {{ ($emp->department ?? '') == 'Sales' ? 'selected' : '' }}>Sales</option>
            <option value="Operations" {{ ($emp->department ?? '') == 'Operations' ? 'selected' : '' }}>Operations</option>
            <option value="IT Network" {{ ($emp->department ?? '') == 'IT Network' ? 'selected' : '' }}>IT Network</option>
            <option value="System Projects" {{ ($emp->department ?? '') == 'System Projects' ? 'selected' : '' }}>System Projects</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="location" class="form-label">Location</label>
        <input type="text" class="form-control" name="location" value="{{ $emp->location ?? '' }}" placeholder="Enter location">
    </div>

    <div class="col-md-6">
        <label for="position" class="form-label">Position</label>
        <select class="form-control" name="position">
            <option value="">Choose a Position</option>
            <option value="Marketing & Events" {{ ($emp->position ?? '') == 'Marketing & Events' ? 'selected' : '' }}>Marketing & Events</option>
            <option value="Human Resources" {{ ($emp->position ?? '') == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
            <option value="General Manager" {{ ($emp->position ?? '') == 'General Manager' ? 'selected' : '' }}>General Manager</option>
            <option value="Sales & Application Engineer" {{ ($emp->position ?? '') == 'Sales & Application Engineer' ? 'selected' : '' }}>Sales & Application Engineer</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="joined_at" class="form-label">Date Joined</label>
        <input type="date" class="form-control" name="joined_at" value="{{ $emp->joined_at ?? '' }}">
    </div>

    <div class="col-md-6">
        <label for="dob" class="form-label">Date of Birth</label>
        <input type="date" class="form-control" name="dob" value="{{ $emp->dob ?? '' }}">
    </div>

    <div class="col-md-6">
        <label for="phone_number" class="form-label">Contact Number</label>
        <input type="tel" class="form-control" name="phone_number" value="{{ $emp->phone_number ?? '' }}" placeholder="Enter contact number">
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" value="{{ $emp->email ?? '' }}" placeholder="Enter email" readonly>
    </div>

    <div class="col-md-6">
        <label for="path_image" class="form-label">Profile Image</label>
        <input type="file" class="form-control" name="path_image" accept="image/*">
        @if(isset($emp->path_image))
            <img src="{{ asset($emp->path_image) }}" alt="Profile Image" class="img-thumbnail mt-2" width="100">
        @endif
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Role {{ $emp->roles->first()->id }}</label>
        <select class="form-control" name="role_id">
            <option value="">Choose a Role</option>

            @foreach ($roles as $role)
                <option value="{{ $role->name }}" @if($emp->roles->first()->id == $role->id) selected @endif>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">

    </div>


    <div class="col-12">
        <button type="submit" class="btn btn-primary">{{ isset($emp) ? 'Update' : 'Submit' }}</button>
        <a href="{{ route('v1.users')}}" class="btn btn-warning">Cancel</a>
    </div>
</form>



                </div>
            </div>
        </div>
    </div>
@endsection
