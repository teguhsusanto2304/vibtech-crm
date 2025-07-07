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
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="mb-0">{{ $title }}</h3>
                <x-print-button-widget />
            </div>
            <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-section {
            background: linear-gradient(90deg,rgb(19, 125, 237), #004080);
            color: white;
            padding: 40px 30px;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .header-info h5 {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .header-info p {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .header-status .badge {
            font-size: 1rem;
            padding: 0.5em 1em;
            border-radius: 20px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card-header {
            background-color:rgb(223, 235, 247);
            font-weight: bold;
            font-size: 1.1rem;
            color: #004080;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .job-requisition-heading {
            font-size: 2rem;
            font-weight: 700;
            color: #343a40;
            border-bottom: 3px solid #004080;
            padding-bottom: 10px;
        }

        .job-requisition-subtext {
            font-style: italic;
            color: #6c757d;
            font-size: 1rem;
        }

        .detail-item {
            margin-bottom: 20px;
        }

        .detail-item label {
            font-weight: 700;
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 4px;
        }

        .detail-item p {
            margin-bottom: 0;
            font-size: 1.05rem;
            color: #212529;
        }

        .personnel-list li,
        .file-list li {
            padding: 12px 20px;
            border-bottom: 1px dashed #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .personnel-list li:last-child,
        .file-list li:last-child {
            border-bottom: none;
        }

        .personnel-list .badge {
            border-radius: 20px;
        }

        .file-list-item-name {
            display: flex;
            align-items: center;
            flex-grow: 1;
            margin-right: 15px;
            font-size: 1rem;
        }

        .file-list-item-name i {
            margin-right: 10px;
        }

        .file-actions a {
            font-size: 1rem;
            margin-left: 12px;
            transition: color 0.2s ease;
        }

        .file-actions a:hover {
            color: #0056b3 !important;
        }

        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 10px 20px;
        }
        h5 {
            color: #dee2e6;
        }
    </style>
        </div>
            <div class="header-section">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0 d-flex align-items-center">
                            <img src="{{ asset('assets/img/logo.png')}}" alt="Vibtech Genesis Logo" class="logo"
                                        width="200px" height="100px">
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="header-info">
                                <h5>Job Record ID</h5>
                                <p>{{ $job->job_record_id }}</p>

                                <h5>Job Created On</h5>
                                <p>{{ $job->user->name }}</p>
                                <label>{{ $job->created_at->format('l, d/m/Y h:i A') }}</label>

                                <h5>Status</h5>
                                <div class="header-status">
                                    <span class="badge bg-success">Accepted</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container mt-4">
                <p class="job-requisition-subtext">Originator had accepted this job record made on dashboard calendar</p>
                <div class="row mt-4">
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                Job Details
                            </div>
                            <div class="card-body mt-2">
                                <div class="row">
                                    <div class="col-md-6 detail-item">
                                        <label>Type of Job</label>
                                        <p>{{ $job->job_type }}</p>
                                    </div>
                                    <div class="col-md-6 detail-item">
                                        <label>Vehicle Required</label>
                                        <x-toast-notification />
                            <x-vehicle-require-switch-widget :job="$job" />
                                    </div>
                                    <div class="col-md-6 detail-item">
                                        <label>Business Name</label>
                                        <p>{{ $job->business_name }}</p>
                                    </div>
                                    
                                    <div class="col-md-6 detail-item">
                                        <label>Business Location</label>
                                        <p>{{ $job->business_address }}</p>
                                    </div>
                                    <div class="col-md-6 detail-item">
                                        <label>Scope of Work</label>
                                        <p>{{ $job->scope_of_work }}</p>
                                    </div>
                                    <div class="col-md-6 detail-item">
                                        <label>Date of Job</label>
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
                                @php $count++; @endphp
                            @endforeach
                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="col-md-6 detail-item">
                                        <label>No of Day</label>
                                        <p>{{ $count }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                Personnel Involved
                            </div>
                            <div class="card-body p-0">
                                
                            </div>
                        </div>
                        <x-personnal-involved-v2-widget :personnels="$personnels" :staff="$staff" :job="$job" />
                    </div>
                </div>
                <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        Uploaded Files
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-unstyled file-list mb-0">
                            <li>
                                <div class="file-list-item-name">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                    <span>Project_Report_Q2_2025.pdf</span>
                                </div>
                                <div class="file-actions">
                                    <a href="#" class="text-decoration-none text-info me-2" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="#" class="text-decoration-none text-primary" title="Download"><i class="fas fa-download"></i></a>
                                    <a href="#" class="text-decoration-none text-danger ms-2" title="Delete"><i class="fas fa-trash"></i></a>
                                </div>
                            </li>
                            <li>
                                <div class="file-list-item-name">
                                    <i class="fas fa-file-image text-info"></i>
                                    <span>Site_Photo_01.jpg</span>
                                </div>
                                <div class="file-actions">
                                    <a href="#" class="text-decoration-none text-info me-2" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="#" class="text-decoration-none text-primary" title="Download"><i class="fas fa-download"></i></a>
                                    <a href="#" class="text-decoration-none text-danger ms-2" title="Delete"><i class="fas fa-trash"></i></a>
                                </div>
                            </li>
                            <li>
                                <div class="file-list-item-name">
                                    <i class="fas fa-file-word text-primary"></i>
                                    <span>Meeting_Minutes_20250705.docx</span>
                                </div>
                                <div class="file-actions">
                                    <a href="#" class="text-decoration-none text-info me-2" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="#" class="text-decoration-none text-primary" title="Download"><i class="fas fa-download"></i></a>
                                    <a href="#" class="text-decoration-none text-danger ms-2" title="Delete"><i class="fas fa-trash"></i></a>
                                </div>
                            </li>
                        </ul>
                        <div class="card-footer text-end">
                            <small class="text-muted">Total Files: 3</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            </div>    
        </div>
        

@endsection
