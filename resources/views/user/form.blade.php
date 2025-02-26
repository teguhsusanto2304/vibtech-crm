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
                        action="{{  route('v1.users.store') }}"
                        method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="col-md-8">
                            <label for="staffName" class="form-label">Staff Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $user->name ?? '') }}"
                                placeholder="Enter staff name">
                        </div>

                        <div class="col-md-4">
                            <label for="staffID" class="form-label">Staff ID</label>
                            <input type="text" class="form-control" name="user_number"
                                value="{{ old('user_number', $user->user_number ?? '') }}" placeholder="Enter staff ID">
                        </div>

                        <div class="col-md-6">
                            <label for="department" class="form-label">Department</label>
                            <div class="input-group mb-3">
                            <select class="form-control" name="department_id">
                                <option value="">Choose a Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department', $user->department_id ?? '') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}  {{-- Display the department name --}}
                                    </option>
                                @endforeach
                            </select>
                            <a class="btn btn-outline-secondary  btn-sm" type="button" id="button-addon1" href="{{ route('v1.departments.create') }}">
                                <i class="icon-base bx bx-plus icon-md me-4"></i>
                            </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" name="location"
                                value="{{ old('location', $user->location ?? '') }}" placeholder="Enter location">
                        </div>

                        <div class="col-md-4">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control" name="position"
                                value="{{ old('position') }}" placeholder="Enter position">
                            <select class="form-control invisible" name="position1">
                                <option value="">Choose a Position</option>
                                <option value="Marketing & Events" {{ old('position', $user->position ?? '') == 'Marketing & Events' ? 'selected' : '' }}>Marketing & Events</option>
                                <option value="Human Resources" {{ old('position', $user->position ?? '') == 'Human Resources' ? 'selected' : '' }}>Human Resources</option>
                                <option value="General Manager" {{ old('position', $user->position ?? '') == 'General Manager' ? 'selected' : '' }}>General Manager</option>
                                <option value="Sales & Application Engineer" {{ old('position', $user->position ?? '') == 'Sales & Application Engineer' ? 'selected' : '' }}>Sales & Application Engineer
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="position" class="form-label">Position Level</label>
                            <select class="form-control" name="position_level_id">
                                <option value="">Choose a Position Level</option>
                                @foreach ($position_levels as $position_level)
                                    <option value="{{ $position_level->id }}" {{ old('department', $user->position_level_id ?? '') == $position_level->id ? 'selected' : '' }}>
                                        {{ $position_level->name }}  {{-- Display the department name --}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="joined_at" class="form-label">Date Joined</label>
                            <input type="date" class="form-control" name="joined_at"
                                value="{{ old('joined_at', $user->joined_at ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="dob" value="{{ old('dob', $user->dob ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" name="phone_number"
                                value="{{ old('phone_number', $user->phone_number ?? '') }}"
                                placeholder="Enter contact number">
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="{{ old('email', $user->email ?? '') }}" placeholder="Enter email">
                        </div>

                        <div class="col-md-6">
                            <label for="path_image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="path_image" accept="image/*">
                            @if(isset($user->path_image))
                                <img src="{{ asset($user->path_image) }}" alt="Profile Image" class="img-thumbnail mt-2"
                                    width="100">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Role</label>
                            <div class="input-group mb-3">
                            <select class="form-control" name="role_id">
                                <option value="">Choose a Role</option>

                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <a class="btn btn-outline-secondary  btn-sm" type="button" id="button-addon1" href="{{ route('v1.roles.create') }}">
                                <i class="icon-base bx bx-plus icon-md me-4"></i>
                            </a>
                            </div>
                        </div>

                        <div class="col-md-6">

                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('v1.users')}}" class="btn btn-warning">Cancel</a>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
@endsection
