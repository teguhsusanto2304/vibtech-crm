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
                <h2 class="mb-0">{{ $title }}</h2>
                <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                    <button type="button" class="btn btn-danger btn-sm" onclick="window.print()"><img
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAAAXNSR0IArs4c6QAAAYxJREFUWEftl71KxEAUhb+Doo2tP41iucUiNlYWgrWtWFj6IOJb+AQWi9hYCpYWYmMldoIiVhZ2inBNluwyO2ZMZpPIikk7c+/9OOfOzI2YsE8TxsPfBDIzq0NJSYUCFG5IQVqgIjtchcrI7uaLjY22rAXKsy9W9totM7NlYAtYBaaTg3boFDkq6jlv3Y/9BB6AS0nPfq5vPWRm+8AxMBdZOHb7G3Ag6dQNHAEys3XgBpiKzT7m/lStrqT7QbwPdJ4A7WSLd0BvzEJFYXtAJ9t0Iil1pf/5QC/AYrbWccmLKsSsm9kacJvFPEpaCQEN36zY+yZ0skb6w3nLQifXV6gF6veNo1wjCpXpp1+17P8BNTGgVbKsBRo0YUjGJsaPspa9AzMZoDtmDEeI2Bvcszsv54ek2dDTcQ1s/HRsKwLlpb6StBkC6gIXyTy0FIKqGSgd0LaD40cKYWbzwC6w4EA1YdkTcCbpNTighVRpoqmDDsRe82X2V7E7+r+sBaqiQB2xpSyro1DZHBMH9AUIgP8l4FGXAQAAAABJRU5ErkJggg=="
                            / height="20px" width="20px"></button>
                    <button type="button" class="btn btn-warning btn-sm" id="exportPdf"><img
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAAAXNSR0IArs4c6QAAAexJREFUWEftmL9LVWEYxz9fyKaaVJKEihbBW5Nrk1CIS2CDCYE06ChS6BSaiEvU4uaijYpCczqoQ0u4iTmIk1Okf4A1PN338t7LQc+9p/fwHrnGfeHAgfd5nvdzvs9z3l+iyZqajIfrA2RmXcAH4BlwJ1DJGUnzgT4V87oKmdkqMJwnqPd5J2kh1L8R0C+gIzTgBfspSR9DYjQCspBA3nbFp7g74RsEFRvoPfAZ+AbkgooOJGnOzO7nhYoNNCfJqUReqMKA8kIVCpQHqnCgelDlWksdOzbQLrBTZ7p4AIxW+64K6J+nrhZQllQthVoKZSmQ1R+jhpLr1D3gC3Ak6aWZtQEl4BXwFjiQ9KgKZWZufZtNQkYBAtwu8jswANwGXnugc+AEeAJMAM8llcxsCegpb0ncpOh2ALUWC2gZ2C8/L4CHQH8C6CYwCWwBG5J6zWwcuOvta4pV9s4Rlo5KyszMKTMILALbHugPcMMDrQObLmUJoCHgcREKuZQdJgKveaDfwA+nGOAGf+MV2gP60go8ikK+htKAbiWKegw49jVUKFDWnxzUH0OhoAGzjP8LoFOgPetLc/afSUo9hBZ5lG7EuippJPXvq+flLxs+lc9XT4HOnEpcdPsJfAWmJbn3S+36XMdEUiQ4TNMp9BfecPYlaKO07QAAAABJRU5ErkJggg=="
                            width="20px" width="20px" /></button>
                </div>
            </div>
            <script>
                document.getElementById("exportPdf").addEventListener("click", function () {
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF('p', 'mm', 'a4');

                    html2canvas(document.body, {
                        scale: 2, // Improves quality
                        useCORS: true
                    }).then(canvas => {
                        const imgData = canvas.toDataURL("image/png");
                        const imgWidth = 210; // A4 width in mm
                        const imgHeight = (canvas.height * imgWidth) / canvas.width; // Maintain aspect ratio
                        pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
                        pdf.save("Job_Requisition_Form.pdf");
                    });
                });
            </script>

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
                            <img src="{{ asset('assets/img/logo.png')}}" alt="Vibtech Genesis Logo" class="logo"
                                width="200px" height="100px">
                            <div class="mt-auto">
                                <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    @if((int) $job->user_id === (int) auth()->user()->id)
                                        @if((int) $job->is_publish === 1)
                                            <button type="button" class="btn btn-warning btn-md action-btn"
                                                    data-action="recall">Recall</button>
                                        @else
                                            <button type="button" class="btn btn-primary btn-md action-btn"
                                                    data-action="publish">Publish</button>
                                            <a href="{{ route('v1.job-assignment-form.edit', ['id' => $job->id])}}"
                                                    class="btn btn-success">Edit</a>
                                        @endif
                                    @endif
                                    </div>
                                    <h3 class="h3 ">Job Requisition Form</h3>
                                </div>
                            </div>
                            <div class="col-6 text-end">
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
                                <div class="rowd-md-flex justify-content-md-end ">
                                    <h3 class="h3">Status</h3>
                                    @if((int) $job->user_id === (int) auth()->user()->id)

                                        <!-- Confirmation Modal -->
                                        <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirm Action</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p style="color: #131313">Are you sure you want to <span
                                                                id="actionText"></span> this job requisition form data?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">No</button>
                                                        <button type="button" class="btn btn-primary"
                                                            id="confirmBtnStatus">Yes</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p>
                                                @if($job->job_status == 0)
                                                    <span class="badge bg-info">Pending</span>
                                                    <p>You was created this job record</p>
                                                @elseif($job->job_status == 1)
                                                        <span class="badge bg-success">Accepted</span>
                                                    <p>You was accepted this job record made on dashboard calendar</p>
                                                @elseif($job->job_status == 2)
                                                    <span class="badge bg-danger">Rejected</span>
                                                    <p>You was accepted rejected this job</p>
                                                @elseif($job->job_status == 3)
                                                    <span class="badge bg-danger">Deleted</span>
                                                    <p>You was accepted deleted this job</p>
                                                @endif
                                            </p>

                                        <script>
                                            $(document).ready(function () {
                                                let action = ""; // To store the action type (publish/cancel)

                                                // Open Modal on Button Click
                                                $(".action-btn").on("click", function () {
                                                    action = $(this).data("action"); // Get the action type
                                                    $("#actionText").text(action === "publish" ? "publish" : "recall"); // Update modal text
                                                    $("#confirmModal").modal("show"); // Show modal
                                                });

                                                // Confirm Action
                                                $("#confirmBtnStatus").off("click").on("click", function () {
                                                    $.ajax({
                                                        url: "{{ route('v1.job-assignment-form.history.update-status') }}", // Laravel route
                                                        type: "POST",
                                                        data: {
                                                            _token: "{{ csrf_token() }}",
                                                            id: "{{ $job->id }}",
                                                            action: action, // Pass action to server
                                                        },
                                                        success: function (response) {
                                                            if (response.success) {
                                                                if (action === "cancel") {
                                                                    window.location.href = "{{ route('v1.job-assignment-form.history') }}"; // Redirect when canceled
                                                                } else {
                                                                    location.reload(); // Refresh page for other actions (e.g., publish)
                                                                    showToast("Job assignment status updated successfully!", "success");
                                                                }
                                                            } else {
                                                                alert("Failed to update event.");
                                                            }
                                                        },
                                                        error: function () {
                                                            alert("Error updating event.");
                                                        }
                                                    });

                                                    $("#confirmModal").modal("hide"); // Close modal after confirmation
                                                });
                                            });
                                        </script>

                                    @else

                                        <p>
                                            @if($job->job_status == 0)
                                                    <span class="badge bg-info">Pending</span>
                                                <p>No record made on dashboard calendar until all personnel involved accepted this job
                                                </p>
                                            @elseif($job->job_status == 1)
                                                <span class="badge bg-success">Accepted</span>
                                                <p>Originator had accepted this job record made on dashboard calendar</p>
                                            @elseif($job->job_status == 2)
                                                <span class="badge bg-danger">Rejected</span>
                                                <p>Originator had rejected this job</p>
                                            @elseif($job->job_status == 3)
                                                <span class="badge bg-danger">Deleted</span>
                                                <p>Originator had deleted this job</p>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr style="color: #fff">
                    <div class="row">
                        <div class="col-6 text-end">
                            <div class="row px-4">
                                <h3 class="h3">Type of Job</h3>
                                <p>{{ $job->job_type }}</p>
                            </div>
                        </div>
                        <div class="col-6 text-start mr-10">
                            <div class="row px-4">
                                <h3 class="h3">Vehicle Required</h3>
                                @if($job->job_status != 0 || auth()->user()->id != $job->user_id)
                                    <p>{{ ($job->is_vehicle_require === 1 ? 'Yes' : 'No') }}</p>
                                @else
                                    <div class="form-check form-switch">
                                        <style>
                                            /* Default switch colors */
                                            .form-check-input {
                                                background-color: #ccc;
                                                /* Default background */
                                                border-color: #aaa;
                                            }

                                            /* Checked (ON) state */
                                            .form-check-input:checked {
                                                background-color: #28a745 !important;
                                                /* Green when ON */
                                                border-color: #28a745 !important;
                                            }

                                            /* Customize switch handle (thumb) */
                                            .form-check-input:checked::before {
                                                background-color: white;
                                            }

                                            /* Hover effect */
                                            .form-check-input:hover {
                                                cursor: pointer;
                                            }
                                        </style>
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked"
                                            data-id="{{ $job->id }}" {{ $job->is_vehicle_require ? 'checked' : '' }}>
                                            <label class="form-check-label" style="color:#fff;" for="flexSwitchCheckChecked"
                                            id="switchLabel">{{ $job->is_require=1 ? 'Yes' : 'No' }}</label>
                                    </div>
                                    <div class="toast-container position-fixed top-0 end-0 p-3">
                                        <div id="successToast" class="toast align-items-center text-white bg-success border-0"
                                            role="alert" aria-live="assertive" aria-atomic="true">
                                            <div class="d-flex">
                                                <div class="toast-body">Success message here</div>
                                                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                                    data-bs-dismiss="toast" aria-label="Close"></button>
                                            </div>
                                        </div>

                                    </div>


                                    <script>
                                        document.getElementById("flexSwitchCheckChecked").addEventListener("change", function () {
                                            let label = document.getElementById("switchLabel");
                                            label.textContent = this.checked ? "Yes" : "No";
                                        });
                                    </script>
                                    <script>
                                        function showToast(message, type = "success") {
                                            let toastId = type === "success" ? "successToast" : "errorToast";

                                            // Update the toast message
                                            $("#" + toastId + " .toast-body").text(message);

                                            // Show the toast
                                            let toast = new bootstrap.Toast(document.getElementById(toastId));
                                            toast.show();
                                        }
                                        $(document).ready(function () {
                                            $(".form-check-input").on("change", function () {
                                                let jobId = $(this).data("id");
                                                let isRequire = $(this).prop("checked") ? 1 : 0; // Get new status

                                                $.ajax({
                                                    url: "{{ route('v1.job-assignment-form.update-vehicle-require') }}",  // Update this with your actual route
                                                    type: "POST",
                                                    data: {
                                                        _token: "{{ csrf_token() }}", // CSRF protection
                                                        id: jobId,
                                                        is_vehicle_require: isRequire
                                                    },
                                                    success: function (response) {
                                                        showToast("Vehicle requirement updated successfully!", "success");
                                                    },
                                                    error: function () {
                                                        showToast("Error updating vehicle requirement!", "error");
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="row px-4">
                                <h3 class="h3">Business Name</h3>
                                <p>{{ $job->business_name }}</p>
                            </div>
                        </div>
                        <div class="col-6 text-start mr-10">
                            <div class="row px-4">
                                <h3 class="h3">Additional Document</h3>
                                <p>{{ $job->job_record_id }}.pdf</p>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="row px-4">
                                <h3 class="h3">Business Location</h3>
                                <p>{{ $job->business_address }}</p>
                            </div>
                        </div>
                        <div class="col-6 text-start mr-10">
                            <div class="row px-4">
                                <h3 class="h3">Scope of Work</h3>
                                <p>{{ $job->scope_of_work }}</p>
                            </div>
                        </div>
                        <div class="col-6 text-end">
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
                                    @php $count++; @endphp
                                @endforeach
                            </div>
                        </div>
                        <div class="col-6 mr-10">
                        </div>
                        <div class="col-6 text-end">
                            <div class="row px-4">
                                <h3 class="h3">No of Day</h3>

                                <p>{{ $count }}</p>
                            </div>
                        </div>
                        <div class="col-6 text-start mr-10">
                            @if(session('success'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            <div class="row px-4">
                                <h3 class="h3">Personnel Involved</h3>
                                @php
                                    $assignment_status = 9;
                                    $job_status = 9;
                                    $confirm_status = 0;

                                @endphp
                                @foreach ($personnels as $person)
                                                            @php

                                                                if ((int) $person->user_id === (int) auth()->user()->id) {
                                                                    $assignment_status = 0;
                                                                    $person_id = $person->id;
                                                                    $job_status = $person->assignment_status;
                                                                } else {
                                                                    $assignment_status = $person->assignment_status;
                                                                }
                                                            @endphp
                                                            <div class="row">
                                                                <div class="col-6 mb-4">
                                                                    <span class="status-label">{{ $person->user->name }}</span>
                                                                    @if((int) $person->assignment_status == 2)
                                                                        <p><small style="color: #eec658"><i>{{ $person->reason }}</i></small>
                                                                            @if(!empty($person->purpose_at))
                                                                                <small style="color: #eec658">and purposed alternatif
                                                                                    date at </small><small
                                                                                    style="color: #efa780"><strong>{{ $person->purpose_at }}</strong></small>
                                                                            @endif
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                                <style>
                                                                    .same-height-element {
                                                                        padding: 0.375rem 0.75rem;
                                                                        /* Adjust padding as needed */
                                                                        line-height: 1.5;
                                                                        /* Adjust line height as needed */
                                                                        display: inline-flex;
                                                                        /* Use inline-flex for alignment */
                                                                        align-items: center;
                                                                        /* Vertically align items */
                                                                    }

                                                                    .btn-sm {
                                                                        padding: 0.25rem 0.5rem;
                                                                        /* Adjust padding for small button */
                                                                    }

                                                                    /* Optional: Adjust icon size if needed */
                                                                    .icon-base {
                                                                        font-size: 1rem;
                                                                        /* Adjust icon size as needed */
                                                                    }
                                                                </style>
                                                                <div class="col-6 mb-4">
                                                                    @if((int) $person->assignment_status == 0)
                                                                        <div class="btn-group align-items-center" role="group"
                                                                            aria-label="Basic mixed styles example">
                                                                            <span class="badge bg-info same-height-element">Awaiting Response</span>
                                                                            @if($person->user->id === auth()->user()->id)
                                                                                <button class="btn btn-primary btn-sm same-height-element"
                                                                                    data-bs-toggle="modal" data-bs-target="#staffModal"><i
                                                                                        class="icon-base bx bx-plus"></i></button>
                                                                            @endif
                                                                        </div>

                                                                    @elseif((int) $person->assignment_status == 1)
                                                                        <div class="btn-group align-items-center" role="group"
                                                                            aria-label="Basic mixed styles example">
                                                                            <span class="badge bg-success same-height-element">Accepted Job</span>
                                                                        </div>
                                                                    @elseif((int) $person->assignment_status == 3)
                                                                                                    @php
                                                                                                        if ((int) auth()->user()->id === (int) $person->user_id) {
                                                                                                            $confirm_status = $person->assignment_status;
                                                                                                        }
                                                                                                    @endphp
                                                                                                    <div class="btn-group align-items-center" role="group"
                                                                                                        aria-label="Basic mixed styles example">
                                                                                                        <span class="badge bg-warning same-height-element">Waiting For
                                                                                                            Confirmation</span>
                                                                                                    </div>
                                                                    @else
                                                                        <div class="btn-group align-items-center" role="group"
                                                                            aria-label="Basic mixed styles example">
                                                                            <span class="badge bg-danger same-height-element">Rejected Job</span>
                                                                        </div>

                                                                    @endif
                                                                </div>
                                                            </div>
                                @endforeach
                                <script>
                                    $(document).ready(function () {
                                        $('#staffModal').on('show.bs.modal', function () {
                                            $('button, a').not('.modal .btn, .modal a').prop('disabled', true); // Disable all except modal buttons
                                        });

                                        $('#staffModal').on('hidden.bs.modal', function () {
                                            $('button, a').prop('disabled', false); // Re-enable buttons after closing modal
                                        });
                                    });
                                </script>

                                <div class="modal fade" id="staffModal" tabindex="-1" aria-labelledby="myModalLabel"
                                    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Invite a personal involve</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="modal-body">
                                                    <table class="table table-striped table-bordered" id="staffTable">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Name</th>
                                                                <th>Position</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $i = 1; @endphp
                                                            @foreach ($staff as $row)
                                                                <tr>
                                                                    <td>{{ $i++ }}</td>
                                                                    <td>{{ $row->name }}</td>
                                                                    <td>{{ $row->position }}</td>
                                                                    <td>
                                                                        <a class="btn btn-primary btn-sm"
                                                                            href="{{ route('v1.job-assignment-form.job.invited-staff', ['user_id' => $row->id, 'job_id' => $job->id])}}">
                                                                            Invite
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-dismiss="modal">Close</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($respond == 'no')
                <div class="col-12 mt-4 text-center">
                    <button type="submit" class="btn btn-warning">Send Reminder</button>
                </div>
            @else
                <form action="{{ route('v1.job-assignment-form.respond') }}" method="post">
                    @csrf
                    @if((int) $job_status === 0)
                        <div class="col-12 mt-4 text-center">
                            <h3> Response this Job </h3>
                            <input type="hidden" name="id" value="{{ $person_id }}">
                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                            <div class="row mt-3 mb-4">
                                <div class="col-4"></div>
                                <div class="col-4 ">
                                    <label for="reason" class="form-label" style="color: #010101" id="reason_label">Reason</label>
                                    <textarea class="form-control" style="color: #010101" name="reason" id="reason" rows="3"
                                        placeholder="Enter your reason here" style="display: none;"></textarea>
                                </div>
                                <div class="col-4"></div>
                                <div class="col-4"></div>
                                <div class="col-4 ">
                                    <label for="purpose_at" class="form-label" style="color: #010101" id="purpose_at_label">Purpose
                                        Alternative Date</label>
                                    <input type="date" class="form-control" style="color: #010101" name="purpose_at"
                                        id="purpose_at">
                                </div>
                                <div class="col-4"></div>
                            </div>
                            <button type="submit" name="response" value="accept" class="btn btn-success"
                                onclick="hideReason()">Accept</button>
                            <button type="submit" name="response" value="decline" class="btn btn-danger"
                                onclick="showReason()">Decline</button>
                            <script>
                                function showReason() {
                                    document.getElementById('reason_label').style.display = 'block';
                                    document.getElementById('purpose_at_label').style.display = 'block';
                                    document.getElementById('purpose_at').style.display = 'block';
                                    document.getElementById('reason').style.display = 'block';
                                    document.getElementById('reason').setAttribute('required', 'required'); // Make it required when declining
                                    //document.getElementById('purpose_at').setAttribute('required', 'required');
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
                    @elseif((int) $confirm_status === 3)
                        <div class="col-12 mt-4 text-center">
                            <h3> Confirm this Job</h3>
                            <input type="hidden" name="id" value="{{ $person_id }}">
                            <input type="hidden" name="job_id" value="{{ $job->id }}">
                            <button type="submit" name="response" value="confirm" class="btn btn-success"
                                onclick="hideReason()">Confirm</button>
                        </div>
                    @endif
                </form>
            @endif
        </div>

@endsection
