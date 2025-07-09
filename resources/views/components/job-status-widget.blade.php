@if((int) $job->user_id === (int) auth()->user()->id)
    <p>
        @if((int) $job->job_status === 0)
                <span class="badge badge-xs bg-info">Pending</span>
            <p ><small>You was created this job record</small></p>
        @elseif((int) $job->job_status === 1)
            <span class="badge bg-success">Accepted</span>
            <p><small>You was accepted this job record made on dashboard calendar</small></p>
        @elseif((int)  $job->job_status === 2)
            <span class="badge bg-danger">Rejected</span>
            <p><small>You was accepted rejected this job</small></p>
        @elseif((int) $job->job_status === 3)
            <span class="badge bg-danger">Deleted</span>
            <p><small>You was accepted deleted this job</small></p>
        @elseif((int) $job->job_status === 4)
            <span class="badge bg-warning">Recall</span>
            <p><small>Originator had recall this job</small></p>
        @endif
    </p>
@else

    <p>
        @if($job->job_status == 0)
                <span class="badge bg-info">Pending</span>
            <p><small>No record made on dashboard calendar until all personnel involved accepted this job
            </small></p>
        @elseif($job->job_status == 1)
            <span class="badge bg-success">Accepted</span>
            <p><small>Originator had accepted this job record made on dashboard calendar</small></p>
        @elseif($job->job_status == 2)
            <span class="badge bg-danger">Rejected</span>
            <p><small>Originator had rejected this job</small></p>
        @elseif($job->job_status == 3)
            <span class="badge bg-danger">Deleted</span>
            <p><small>Originator had deleted this job</small></p>
        @elseif($job->job_status == 4)
            <span class="badge bg-warning">Recall</span>
            <p><small>Originator had recall this job</small></p>
        @endif

    </p>
@endif
