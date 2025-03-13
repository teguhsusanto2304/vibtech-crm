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
            <style>
                input:-webkit-autofill,
                input:-webkit-autofill:focus,
                input:-webkit-autofill:hover,
                input:-webkit-autofill:active {
                    background-color: rgb(241, 243, 244) !important;
                    color: black !important;
                    box-shadow: 0 0 0px 1000px rgb(248, 249, 249) inset !important;
                    /* Forces color */
                }

                .form-container {
                    background-color: #fff;
                    /* Dark blue background */
                    padding: 30px;
                    border-radius: 5px;
                }

                .form-control:focus {
                    background-color: white;
                    /* Change background color on focus */
                    color: #fff;
                    /* Change text color on focus */
                }

                .form-container h2 {
                    color: #fff;
                    margin-bottom: 20px;
                }

                .form-control-input {
                    color: #010101;
                    /* Text color */
                    background-color: #fff;
                    /* Background color */
                    border-color: #fff;
                    /* Border color */
                }

                .form-control-input:focus {
                    color: #010101;
                    /* Text color */
                    background-color: #fff;
                    /* Background color */
                    border-color: #fff;
                    /* Border color */
                }



                .form-check-input {
                    background-color: #fff;
                    /* Radio button background color */
                    border-color: #fff;
                    /* Radio button border color */
                }



                .form-select {
                    background-color: white;
                    /* Set background color to white */
                    color: #131313;
                    /* Set text color to match your background */
                    border: 1px solid #ccc;
                    /* Add a border for better contrast */
                }

                .form-label {
                    color: #fff;
                }
            </style>
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

            <div class="card" style="background-color: #004080">
                <div class="card-body">
                    <form class="row g-3" action="{{ route('v1.job-assignment-form.update', $job->id) }}" method="post">
                        @csrf
                        @method('PUT')

                        <div class="col-md-3">
                            <label class="form-label">Job Record ID</label>
                            <input type="text" class="form-control form-control-input" name="job_record_id"
                                value="{{ $job->job_record_id }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Type of Job</label>
                            <input type="text" class="form-control form-control-input" name="job_type"
                                value="{{ old('job_type', $job->job_type) }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Publish on Dashboard Calendar</label>
                            <br>
                            <input class="form-check-input" type="checkbox" value="1" name="job_status" {{ $job->job_status ? 'checked' : '' }}>
                            <label class="form-label">Yes</label>
                        </div>

                        <div class="col-6">
                            <label class="form-label">Business Name</label>
                            <input type="text" class="form-control form-control-input" name="business_name"
                                value="{{ old('business_name', $job->business_name) }}" placeholder="Enter business name">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Business Address</label>
                            <input type="text" class="form-control form-control-input" name="business_address"
                                value="{{ old('business_address', $job->business_address) }}"
                                placeholder="Enter business address">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Scope of Work</label>
                            <textarea class="form-control form-control-input" name="scope_of_work" cols="6" rows="3"
                                placeholder="Enter scope of work">{{ old('scope_of_work', $job->scope_of_work) }}</textarea>
                        </div>

                        <div class="col-2">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control form-control-input" name="start_at"
                                value="{{ old('start_at', $job->start_at) }}">
                        </div>

                        <div class="col-2">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control form-control-input" name="end_at"
                                value="{{ old('end_at', $job->end_at) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Sent To</label>
                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
                                rel="stylesheet" />
                            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                            <style>
                                .select2-container .select2-selection--single {
                                    height: 36px !important;
                                    /* Adjust height as needed */
                                    border: 1px solid #ccc !important;
                                    /* Add border for consistency */
                                    border-radius: 4px !important;
                                    /* Add border-radius for consistency */
                                }
                            </style>
                            <select class="form-select select2" name="prsonnel_ids[]" id="personnel-multiple"
                                multiple="multiple">
                                @foreach ($users as $dept => $departmentUsers)
                                    <optgroup label="{{ $dept }}">
                                        @foreach ($departmentUsers as $user)
                                            <option value="{{ $user->id }}" {{ in_array($user->id, $selectedUsers) ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Vehicle Require</label>
                            <br>
                            <input class="form-check-input" type="checkbox" value="1" name="is_vehicle_require" {{ $job->is_vehicle_require ? 'checked' : '' }}>
                            <label class="form-label">Yes</label>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('v1.job-assignment-form.view', ['id' => $job->id, 'respond' => 'yes']) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                $('#personnel-multiple').select2();
            });
        </script>

    </form>

    </div>
    </div>
@endsection
