@php
    $assignment_status = 9;
    $job_status = 9;
    $confirm_status = 0;
    $user_logged = false;

@endphp
@foreach ($personnels as $person)
    @php

        if ((int) $person->user_id === (int) auth()->user()->id) {
            $assignment_status = 0;
            $person_id = $person->id;
            $job_status = $person->assignment_status;
            $confirm_status = $person->assignment_status;
            $user_logged = true;
        } else {
            $assignment_status = $person->assignment_status;
        }
    @endphp
@endforeach

@if($respond == 'no' && (int) $job->job_status != 3)
    @if($confirm_status == 3)
        <form action="{{ route('v1.job-assignment-form.respond') }}" method="post">
        @csrf
        @if((int) $job_status === 0 && (int) $job->job_status != 3)
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
                        <input type="date" class="form-control" style="color: #010101" name="purpose_at" id="purpose_at">
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
    @else
        <form action="{{ route('v1.job-assignment-form.respond') }}" method="post">
            @csrf
                <div class="col-12 mt-4 text-center">
                    <input type="hidden" name="id" value="{{ $job->user_id }}">
                    <input type="hidden" name="job_id" value="{{ $job->id }}">
                    <button type="submit" name="response" value="reminder" class="btn btn-warning">Send Reminder</button>
                </div>
        </form>
    @endif
@else
    <form action="{{ route('v1.job-assignment-form.respond') }}" method="post">
        @csrf
        @if((int) $job_status === 0 && (int) $job->job_status != 3)
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
                        <input type="date" class="form-control" style="color: #010101" name="purpose_at" id="purpose_at">
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
