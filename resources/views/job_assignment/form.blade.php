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
                            @if($item=='Job Assignment Form')
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

                .form-check-input:checked {
                    background-color: #ffffff34;
                    /* Checked radio button color */
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
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif
            <div class="card" style="background-color: #004080">
                <div class="card-body">
                    <form class="row g-3" action="{{ route('v1.job-assignment-form.store')}}" method="post">
                        @csrf
                        <div class="col-md-3">
                            <label for="inputEmail4" class="form-label">Job Record ID</label>
                            <input type="text" class="form-control form-control-input" name="job_record_id" value="{{ $job_no }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="inputPassword4" class="form-label">Type of Job</label>
                            <input type="text" class="form-control form-control-input" name="job_type"  >
                        </div>
                        <div class="col-6">
                            <label for="inputAddress" class="form-label">Business Name</label>
                            <input type="text" class="form-control form-control-input" name="business_name" placeholder="enter business name">
                        </div>
                        <div class="col-6">
                            <label for="inputAddress2" class="form-label">Business Address</label>
                            <input type="text" class="form-control form-control-input" name="business_address"
                                placeholder="enter business addressr">
                        </div>
                        <div class="col-md-12">
                            <label for="inputCity" class="form-label">Scope of work</label>
                            <textarea class="form-control form-control-input" name="scope_of_work" cols="6" rows="3"
                                placeholder="enter scope of work"></textarea>
                        </div>
                        <div class="col-2">
                            <label for="inputAddress" class="form-label">Start Date</label>
                            <input type="date" class="form-control form-control-input" name="start_at">
                        </div>
                        <div class="col-2">
                            <label for="inputAddress2" class="form-label">End Date</label>
                            <input type="date" class="form-control form-control-input" name="end_at">
                        </div>
                        <div class="col-md-8">
                        </div>
                        <div class="col-md-6">
                            <label for="inputState" class="form-label">Personnel Involved</label>
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
                            <select class="form-select select2 " name="prsonnel_ids[]" id="personnel-multiple"
                                multiple="multiple">
                                @foreach ($users as $department => $departmentUsers)
                                    <optgroup label="{{ $department }}">
                                        @foreach ($departmentUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <script>
                                $(document).ready(function () {
                                    $('#personnel-multiple').select2();
                                });
                            </script>
                        </div>
                        <div class="col-md-6">
                            <label for="inputZip" class="form-label">Vehicle Require</label>
                            <br>
                            <input class="form-check-input" type="checkbox" value="1" name="is_vehicle_require" />
                            <label class="form-label" for="defaultCheck1">
                                Yes
                            </label>

                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-warning">Reset</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
