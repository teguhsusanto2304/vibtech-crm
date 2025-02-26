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
                    background-color: #fff;
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
                <style>
                    .h3 {
                        color: #fff;
                    }
                    </style>
            <div class="card" style="background-color: #004080;color: #ffff">
                <div class="container mt-10 mr-10 ml-10 mb-10">
                    <div class="row">
                        <div class="col-6 d-flex flex-column">
                            <img src="{{ asset('assets/img/logo.png')}}" alt="Vibtech Genesis Logo" class="logo" width="200px" height="100px">
                            <h3 class="h3 mt-auto">Job Requisition Form</h3>
                        </div>
                        <div class="col-6 text-end" >
                            <div class="row">
                                <h3 class="h3">Job Record ID</h3>
                                <p>{{ $job->job_record_id }}</p>
                            </div>
                            <div class="row">
                                <h3 class="h3">Job Created</h3>
                                @php
                                    use Carbon\Carbon;
                                    $created_at = Carbon::parse($job->created_at);
                                    $formatted_date = $created_at->format('l, d/m/Y h:i A')
                                @endphp
                                <p>{{ $formatted_date}}</p>
                            </div>
                            <div class="row">
                                <h3 class="h3">Job Created By</h3>

                                <p>{{ $job->user->name }}</p>
                            </div>
                            <div class="row">
                                <h3 class="h3">Status</h3>

                                <p>
                                    @if($job->job_status==0)
                                        <span class="badge bg-info">Pending</span>
                                        <p>No record made on dashboard calendar until all personnel involved accepted this job</p>
                                    @elseif($job->job_status==1)
                                        <span class="badge bg-success">Accepted</span>
                                        <p>All personnel accepted this job record made on dashboard calendar</p>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                        <p>No record made on dashboard calendar until all personnel involved accepted this job</p>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr style="color: #fff">
                <div class="row">
                    <div class="col-6 text-end" >
                        <div class="row px-4">
                            <h3 class="h3">Type of Job</h3>
                            <p>{{ $job->job_type }}</p>
                        </div>
                    </div>
                    <div class="col-6 text-start mr-10" >
                        <div class="row px-4">
                            <h3 class="h3">Vehicle Required</h3>
                            <p>{{ ($job->is_vehicle_require===1? 'Yes':'No') }}</p>
                        </div>
                    </div>
                    <div class="col-6 text-end" >
                        <div class="row px-4">
                            <h3 class="h3">Business Name</h3>
                            <p>{{ $job->business_name }}</p>
                        </div>
                    </div>
                    <div class="col-6 text-start mr-10" >
                        <div class="row px-4">
                            <h3 class="h3">Additional Document</h3>
                            <p>{{ $job->job_record_id }}.pdf</p>
                        </div>
                    </div>
                    <div class="col-6 text-end" >
                        <div class="row px-4">
                            <h3 class="h3">Business Location</h3>
                            <p>{{ $job->business_address }}</p>
                        </div>
                    </div>
                    <div class="col-6 text-start mr-10" >
                        <div class="row px-4">
                            <h3 class="h3">Scope of Work</h3>
                            <p>{{ $job->scope_of_work }}.pdf</p>
                        </div>
                    </div>
                    <div class="col-6 text-end" >
                        <div class="row px-4">
                            <h3 class="h3">Date of Job</h3>
                            @php

$start = new DateTime($job->start_at);
$end = new DateTime($job->end_at);
$end->modify('+1 day'); // Include end date

$interval = new DateInterval('P1D'); // 1 Day interval
$dateRange = new DatePeriod($start, $interval, $end);
$count = 0;
                            @endphp
                            @foreach ($dateRange as $date)
                            <p>{{ $date->format('Y-m-d') }}</p>
                            @php $count ++; @endphp
                            @endforeach
                        </div>
                    </div>
                    <div class="col-6 mr-10">
                    </div>
                    <div class="col-6 text-end" >
                        <div class="row px-4">
                            <h3 class="h3">No of Day</h3>

                            <p>{{ $count }}</p>
                        </div>
                    </div>
                    <div class="col-6 text-start mr-10" >
                        @if(session('success'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="row px-4">
                            <h3 class="h3">Personnel Involved</h3>
                            @php $assignment_status = 9; @endphp
                            @foreach ($personnels as $person )
                            @php

                                if($person->user_id===auth()->user()->id){
                                    $assignment_status = $person->assignment_status;
                                    $person_id= $person->id;
                                }
                            @endphp
                            <div class="row">
                                    <div class="col-6 mb-4">
                                    <span class="status-label">{{ $person->user->name }}</span>
                                    @if($person->assignment_status===2)
                                        <p><small style="color: #eec658"><i>{{ $person->reason }}</i> and purposed alternatif date at </small><small style="color: #efa780"><strong>{{ $person->purpose_at }}</strong></small></p>
                                    @endif
                                    </div>
                                    <style>
                                        .same-height-element {
                                            padding: 0.375rem 0.75rem; /* Adjust padding as needed */
                                            line-height: 1.5; /* Adjust line height as needed */
                                            display: inline-flex; /* Use inline-flex for alignment */
                                            align-items: center; /* Vertically align items */
                                        }

                                        .btn-sm {
                                            padding: 0.25rem 0.5rem; /* Adjust padding for small button */
                                        }

                                        /* Optional: Adjust icon size if needed */
                                        .icon-base {
                                            font-size: 1rem; /* Adjust icon size as needed */
                                        }
                                    </style>
                                    <div class="col-6 mb-4">
                                        @if($person->assignment_status==0)
                                        <div class="btn-group align-items-center" role="group" aria-label="Basic mixed styles example">
                                            <span class="badge bg-info same-height-element">Awaiting Response</span>
                                            @if($person->user->id === auth()->user()->id)
                                                <button class="btn btn-primary btn-sm same-height-element" data-bs-toggle="modal" data-bs-target="#staffModal"><i class="icon-base bx bx-plus"></i></button>
                                            @endif
                                        </div>

                                        @elseif($person->assignment_status==1)
                                        <div class="btn-group align-items-center" role="group" aria-label="Basic mixed styles example">
                                            <span class="badge bg-success same-height-element">Accepted Job</span>
                                        </div>
                                        @else
                                        <div class="btn-group align-items-center" role="group" aria-label="Basic mixed styles example">
                                        <span class="badge bg-danger same-height-element">Rejected Job</span>
                                        </div>

                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div class="modal fade" id="staffModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="myModalLabel">Invite a personal involve</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                            @php $i = 1; @endphp
                                            @foreach ($staff as $row )
                                                <div class="col-1">
                                                    <p style="color: #010101">{{ $i++ }}</p>
                                                </div>
                                                <div class="col-5">
                                                    <p style="color: #010101">{{ $row->name }}</p>
                                                </div>
                                                <div class="col-4">
                                                    <p style="color: #010101">{{ $row->position }}</p>
                                                </div>
                                                <div class="col-2">
                                                    <a class="btn btn-primary btn-sm" href="{{ route('v1.job-assignment-form.job.invited-staff',['user_id'=>$row->id,'job_id'=>$job->id])}}">Invite</a>
                                                </div>
                                            @endforeach
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($respond=='no')
        <div class="col-12 mt-4 text-center">
            <button type="submit" class="btn btn-warning">Send Reminder</button>
        </div>
        @else
        <form action="{{ route('v1.job-assignment-form.respond') }}" method="post">
            @csrf
        @if($assignment_status===0 && $job->job_status===0)
        <div class="col-12 mt-4 text-center">
            <h3> Response this Job</h3>
            <input type="hidden" name="id" value="{{ $person_id }}">
            <input type="hidden" name="job_id" value="{{ $job->id }}">
            <div class="row mt-3 mb-4">
                <div class="col-4"></div>
                <div class="col-4 ">
                    <label for="reason" class="form-label" style="color: #010101" id="reason_label">Reason</label>
                    <textarea class="form-control" style="color: #010101" name="reason" id="reason" rows="3" placeholder="Enter your reason here" style="display: none;"></textarea>
                </div>
                <div class="col-4"></div>
                <div class="col-4"></div>
                <div class="col-4 ">
                    <label for="purpose_at" class="form-label" style="color: #010101" id="purpose_at_label">Purpose Alternative Date</label>
                    <input type="date" class="form-control" style="color: #010101" name="purpose_at" id="purpose_at" >
                </div>
                <div class="col-4"></div>
            </div>
            <button type="submit" name="response" value="accept" class="btn btn-success" onclick="hideReason()">Accept</button>
    <button type="submit" name="response" value="decline" class="btn btn-danger" onclick="showReason()">Decline</button>
            <script>
                function showReason() {
                    document.getElementById('reason_label').style.display = 'block';
                    document.getElementById('purpose_at_label').style.display = 'block';
                    document.getElementById('purpose_at').style.display = 'block';
                    document.getElementById('reason').style.display = 'block';
                    document.getElementById('reason').setAttribute('required', 'required'); // Make it required when declining
                    document.getElementById('purpose_at').setAttribute('required', 'required');
                }

                function hideReason() {
                    document.getElementById('reason').style.display = 'none';
                    document.getElementById('reason').removeAttribute('required'); // Remove required when accepting

                    document.getElementById('purpose_at').style.display = 'none';
                    document.getElementById('purpose_at').removeAttribute('required');

                    document.getElementById('reason_label').style.display = 'none';
                    document.getElementById('purpose_at_label').style.display = 'none';
                }
                hideReason();
            </script>
        </div>
        @endif
        </form>
        @endif
    </div>

@endsection
