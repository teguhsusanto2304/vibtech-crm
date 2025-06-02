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
        @if (session()->has('error_import'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> {{ session('error_import') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
        <div class="card">
            <div class="card-body">
                <!-- Tabs Nav -->
        <ul class="nav nav-tabs" id="inputTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual"
                    type="button" role="tab" aria-controls="manual" aria-selected="true">
                    Manual Input
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload"
                    type="button" role="tab" aria-controls="upload" aria-selected="false">
                    Upload CSV File
                </button>
            </li>

        </ul>

        <!-- Tabs Content -->
        <div class="tab-content pt-3" id="inputTabsContent">
            <!-- Manual Input Tab -->
            <div class="tab-pane fade show active" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                <form class="row g-3" action="{{  route('v1.client-database.store') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <!-- Email -->
                    <div class="form-group col-6">
                        <label for="email">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <!-- Office Number -->
                    <div class="form-group col-3">
                        <label for="office_number">Office Number *</label>
                        <input type="number" name="office_number" class="form-control" required>
                    </div>

                    <!-- Mobile Number -->
                    <div class="form-group col-3">
                        <label for="mobile_number">Mobile Number</label>
                        <input type="number" name="mobile_number" class="form-control">
                    </div>

                    <!-- Job Title -->
                    <div class="form-group col-6">
                        <label for="job_title">Job Title</label>
                        <input type="text" name="job_title" class="form-control">
                    </div>
                    <!-- Company -->
                    <div class="form-group col-6">
                        <label for="company">Company *</label>
                        <input type="text" name="company" class="form-control" required>
                    </div>

                    <!-- Industry Category -->
                    <div class="form-group col-6">
                        <label for="industry_category_id">Industry *</label>
                        <select name="industry_category_id" class="form-control" required>
                            @foreach ($industries as $industry)
                                <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Country -->
                    <div class="form-group col-6">
                        <label for="country_id">Country *</label>
                        <select name="country_id" class="form-control" required>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" @if($country->name == 'Singapore') selected @endif>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sales person -->
                    <div class="form-group col-6">
                        <label for="contact_for_id">Recommended For (Salesperson) *</label>
                        <select name="contact_for_id" class="form-control" required>
                            @foreach ($salesPeople as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                                    <!-- Upload Image -->
                    <div class="form-group col-6">
                        <label for="image_path">Upload Image</label>
                        <input type="file" name="image_path" class="form-control">
                        <small>Max 2 MB file size only</small>
                    </div>

                    <div class="form-group col-12">
                        <label for="image_path">Add Remarks</label>
                        <textarea class="form-control" name="remarks" id="remarks" cols="5"></textarea>
                    </div>


                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('v1.client-database.list')}}" class="btn btn-warning">Cancel</a>
                    </div>
                </form>

</div>

            <!-- Upload CSV Tab -->
            <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                <form class="row g-3" action="{{ route('v1.client-database.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group  col-12">
                        <p>Please <a href="/template/import_client.csv">download</a> and use this excel template to upload</p>
                    </div>
                    <div class="form-group  col-6">
                        <label for="csv_file">Upload CSV File *</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <small>CSV format only, Max 2MB file size</small>
                    </div>
                    <!-- Sales person -->
                    <div class="form-group col-6">
                        <label for="country_id">Recommended For (Sales Person) *</label>
                        <select name="contact_for_id" class="form-control" required>
                            @foreach ($salesPeople as $user)
                                <option value="{{ $user->id }}" >{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label for="image_path">Add Remarks</label>
                        <textarea class="form-control" name="upload_remarks" id="upload_remarks" cols="5"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">Import</button>
                        <a href="{{ route('v1.client-database.list') }}" class="btn btn-warning">Cancel</a>
                    </div>
                </form>
            </div>
 <!-- Reference Data Tab -->
            <div class="tab-pane fade" id="reference" role="tabpanel" aria-labelledby="reference-tab">
        <!-- Country Table -->
        <div class="mt-5">
            <h5>Available Countries</h5>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Country Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($countries as $country)
                        <tr>
                            <td>{{ $country->id }}</td>
                            <td>{{ $country->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Industry Category Table -->
        <div class="mt-5">
            <h5>Available Industry Categories</h5>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Industry Category</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($industries as $industry)
                        <tr>
                            <td>{{ $industry->id }}</td>
                            <td>{{ $industry->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
        </div>
    </div>
</div>
@endsection
