@if((int) $job->user_id === (int) auth()->user()->id)
    <p>
        @if((int) $job->job_status === 0)
                <span class="badge badge-xs bg-info">Pending</span>
            <p ><small>You was created this job record</small></p>
        @elseif((int) $job->job_status === 1)
            <span class="badge bg-success">Accepted</span>
            <p>You was accepted this job record made on dashboard calendar</p>
        @elseif((int)  $job->job_status === 2)
            <span class="badge bg-danger">Rejected</span>
            <p>You was accepted rejected this job</p>
        @elseif((int) $job->job_status === 3)
            <span class="badge bg-danger">Deleted</span>
            <p>You was accepted deleted this job</p>
        @elseif((int) $job->job_status === 4)
            <span class="badge bg-warning">Recall</span>
            <p>Originator had recall this job</p>
        @endif
    </p>
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
        @elseif($job->job_status == 4)
            <span class="badge bg-warning">Recall</span>
            <p>Originator had recall this job</p>
        @endif

    </p>
@endif
