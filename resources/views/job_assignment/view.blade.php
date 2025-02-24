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
                            <p>{{ $job->jobType->name }}</p>
                        </div>
                    </div>
                    <div class="col-6 text-start mr-10" >
                        <div class="row px-4">
                            <h3 class="h3">Vehicle Required</h3>
                            <p>{{ $job->vehicle->name }}</p>
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
                                    </div>
                                    <div class="col-6 mb-4">
                                        @if($person->assignment_status==0)
                                            <span class="badge bg-info">Awaiting Response</span>
                                        @elseif($person->assignment_status==1)
                                        <span class="badge bg-success">Accepted Job</span>
                                        @else
                                        <span class="badge bg-danger">Rejected Job</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
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
            <button type="submit" name="response" value="accept" class="btn btn-success">Accept</button>
            <button type="submit" name="response" value="decline" class="btn btn-danger">Decline</button>
        </div>
        @endif
        </form>
        @endif
    </div>

@endsection
