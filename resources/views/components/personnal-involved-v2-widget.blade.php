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
        <div class="col-4 mb-4 ">
            <span class="status-label"><small><strong>{{ $person->user->name }}</strong></small></span>
            @if($person->is_booker && $job->is_vehicle_require == 1)
                @php
                    if (!empty($job->vehicleBookings->id)) {
                        $color = 'success';
                    } else {
                        $color = 'warning';
                    }
                @endphp
                <p><span class="badge rounded-pill text-bg-{{ $color }}">Vehicle Booker </span></p>
            @endif
            @if((int) $person->assignment_status == 2)
                <p><small style="color: #eec658"><i>{{ $person->reason }}</i></small>
                    @if(!empty($person->purpose_at))
                        <small style="color: #eec658">and purposed alternatif
                            date at </small><small style="color: #efa780"><strong>{{ $person->purpose_at }}</strong></small>
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
                <div class="btn-group btn-sm align-items-center same-height-group" role="group" aria-label="Basic mixed styles example">
                    <span class="badge bg-info same-height-element"><small>Awaiting Response</small></span>
                    @if($person->user->id == auth()->user()->id && (int) $job->job_status != 3)
                        <button class="btn btn-primary btn-sm same-height-element" data-bs-toggle="modal"
                            data-bs-target="#staffModal"><i class="icon-base bx bx-plus"></i></button>
                    @endif
                    @if($job->user_id == auth()->user()->id && (int) $job->job_status != 3 && $job->is_vehicle_require == 1)
                        <button class="btn btn-success btn-sm same-height-element assign-booker-btn" data-id="{{ $person->id }}"
                            data-name="{{ $person->user->name }}"><i class="icon-base bx bx-car"
                                title="Assign pesonel involved as vehicle booker"></i></button>
                    @endif
                </div>

            @elseif((int) $person->assignment_status == 1)
                <div class="btn-group align-items-center" role="group" aria-label="Basic mixed styles example">
                    <span class="badge bg-success same-height-element">Accepted Job</span>
                </div>
            @elseif((int) $person->assignment_status == 3)
                @php
                    if ((int) auth()->user()->id === (int) $person->user_id) {
                        $confirm_status = $person->assignment_status;
                    }
                @endphp
                <div class="btn-group align-items-center" role="group" aria-label="Basic mixed styles example">
                    <span class="badge bg-warning same-height-element">Waiting For
                        Confirmation</span>
                </div>
            @else
                <div class="btn-group align-items-center" role="group" aria-label="Basic mixed styles example">
                    <span class="badge bg-danger same-height-element">Rejected Job</span>
                </div>

            @endif
        </div>
    </div>
@endforeach
<!-- Confirm Assign Modal -->
<div class="modal fade" id="confirmAssignModal" tabindex="-1" aria-labelledby="confirmAssignModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmAssignModalLabel">Confirm Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="assignConfirmationText" style="color: #010101">
                    Are you sure you want to assign <strong><span id="personName"></span></strong> as the vehicle
                    booker?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmAssignBtn" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let jobId; // To store selected job ID

        // Open the confirmation modal when button is clicked
        $(".assign-booker-btn").on("click", function () {
            jobId = $(this).data("id"); // Get the job ID
            personName = $(this).data("name");
            $("#personName").text(personName);
            $("#confirmAssignModal").modal("show"); // Show modal
        });

        // When the user confirms, send an AJAX request to update the database
        $("#confirmAssignBtn").on("click", function () {
            $.ajax({
                url: "{{ route('v1.job-assignment-form.assign-vehicle-booker') }}", // Your update route
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    job_id: jobId
                },
                success: function (response) {
                    $("#confirmAssignModal").modal("hide"); // Hide modal
                    location.reload(); // Refresh the page to show changes
                },
                error: function () {
                    alert("Failed to assign personnel.");
                }
            });
        });
    });
</script>

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

<div class="modal fade" id="staffModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Invite a personal involve</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>
