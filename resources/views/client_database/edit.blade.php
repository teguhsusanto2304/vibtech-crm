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
        <div class="card">
            <div class="card-body">
                <form class="row g-3" action="{{ route('v1.client-database.update-request') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <!-- Name -->
                    <div class="form-group">
                         <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <input type="hidden" name="status" value="edit">
                        <label for="name">Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
                    </div>

                    <!-- Email -->
                    <div class="form-group col-6">
                        <label for="email">Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}" required>
                    </div>

                    <!-- Office Number -->
                    <div class="form-group col-3">
                        <label for="office_number">Office Number *</label>
                        <input type="number" name="office_number" class="form-control" value="{{ old('office_number', $client->office_number) }}" required>
                    </div>

                    <!-- Mobile Number -->
                    <div class="form-group col-3">
                        <label for="mobile_number">Mobile Number</label>
                        <input type="number" name="mobile_number" class="form-control" value="{{ old('mobile_number', $client->mobile_number) }}">
                    </div>

                    <!-- Job Title -->
                    <div class="form-group col-6">
                        <label for="job_title">Job Title</label>
                        <input type="text" name="job_title" class="form-control" value="{{ old('job_title', $client->job_title) }}">
                    </div>

                    <!-- Company -->
                    <div class="form-group">
                        <label for="company">Company *</label>
                        <input type="text" name="company" class="form-control" value="{{ old('company', $client->company) }}" required>
                    </div>

                    <!-- Industry Category -->
                    <div class="form-group">
                        <label for="industry_category_id">Industry *</label>
                        <select name="industry_category_id" class="form-control" required>
                            @foreach ($industries as $industry)
                                <option value="{{ $industry->id }}" {{ $client->industry_category_id == $industry->id ? 'selected' : '' }}>
                                    {{ $industry->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Country -->
                    <div class="form-group">
                        <label for="country_id">Country *</label>
                        <select name="country_id" class="form-control" required>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ $client->country_id == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sales Person -->
                    <div class="form-group">
                        <label for="sales_person_id">Sales Person *</label>
                        <select name="sales_person_id" class="form-control" required>
                            @foreach ($salesPeople as $user)
                                <option value="{{ $user->id }}" {{ $client->sales_person_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Upload Image -->
                    <div class="form-group">
                        <label for="image_path">Upload Image</label>
                        <input type="file" name="image_path" class="form-control-file">
                        @if ($client->image_path)
                            <p class="mt-2">Current Image: <br>
                                <img src="{{ asset('storage/' . $client->image_path) }}" alt="Client Image" width="100">
                            </p>
                        @endif
                        <br>
                        <small>Max 2 MB file size only</small>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('v1.client-database.list') }}" class="btn btn-warning">Cancel</a>
                    </div>
                </form>



            </div>
        </div>
    </div>
    </div>
@endsection
