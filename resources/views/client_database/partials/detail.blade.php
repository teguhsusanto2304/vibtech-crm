<div class="row">
    @if($users)

    <!-- Sales person -->
                    <div class="form-group col-8">
                        <input id="client_id" name="client_id" type="hidden">
                        <label for="contact_for_id">Salesperson *</label>
                        <select name="sales_person_id" id="sales_person_id" class="form-control" required>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-4">
                         <label for="contact_for_id mb-4">Allow Edit</label>
                         <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="is_editable" name="is_editable">
                            <label class="form-check-label" for="checkDefault">
                                Yes
                            </label>
                        </div>
                    </div>


    @else
    {{-- Left: Image --}}
    <div class="col-md-4 text-center">
        @if ($client->image_path)
            <img src="{{ asset('storage/' . $client->image_path) }}"
                 alt="Client Image"
                 class="img-fluid rounded shadow"
                 style="max-height: 200px;">
        @else
            <svg fill="#000000" width="150px" height="150px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                <defs><style>.cls-1{fill:none;}</style></defs>
                <title>no-image</title>
                <path d="M30,3.4141L28.5859,2,2,28.5859,3.4141,30l2-2H26a2.0027,2.0027,0,0,0,2-2V5.4141ZM26,26H7.4141l7.7929-7.793,
                    2.3788,2.3787a2,2,0,0,0,2.8284,0L22,19l4,3.9973Zm0-5.8318-2.5858-2.5859a2,2,0,0,0-2.8284,
                    0L19,19.1682l-2.377-2.3771L26,7.4141Z"/>
                <path d="M6,22V19l5-4.9966,1.3733,1.3733,1.4159-1.416-1.375-1.375a2,2,0,0,0-2.8284,
                    0L6,16.1716V6H22V4H6A2.002,2.002,0,0,0,4,6V22Z"/>
                <rect class="cls-1" width="32" height="32"/>
            </svg>
        @endif
    </div>

    {{-- Right: Details --}}
    <div class="col-md-8">
  <table class="table">
    <tbody>
      <tr>
        <td colspan="2"><h3><strong>{{ $client->name }}</strong></h3></td>
      </tr>
      <tr>
        <th>Company</th>
        <td>{{ $client->company }}</td>
      </tr>
      <tr>
        <th>Email</th>
        <td>{{ $client->email }}</td>
      </tr>
      <tr>
        <th>Office</th>
        <td>{{ $client->office_number }}</td>
      </tr>
      <tr>
        <th>Mobile</th>
        <td>{{ $client->mobile_number }}</td>
      </tr>
      <tr>
        <th>Job Title</th>
        <td>{{ $client->job_title }}</td>
      </tr>
      <tr>
        <th>Industry</th>
        <td>{{ $client->industryCategory->name ?? '-' }}</td>
      </tr>
      <tr>
        <th>Country</th>
        <td>{{ $client->country->name ?? '-' }}</td>
      </tr>
      <tr>
        <th>Sales Person</th>
        <td>{{ $client->salesPerson->name ?? '-' }}</td>
      </tr>
      <tr>
        <th>Type</th>
        <td>{!! $client->client_type_label ?? '-' !!}</td>
      </tr>
      @if(!$delete)
      <tr>
        <th>Activity Logs</th>
        <td>
            <div style="max-height: 100px; overflow-y: auto; border: 1px solid #eee; padding: 5px;">
            <ul>
                @foreach($client->activityLogs as $log)
                    <li><small>{{ $log->activity }}</small></li>
                @endforeach
            </ul>
        </div>
        </td>
      </tr>
      @endif
      @if($delete)
        @php
            if($delete=='yes'){
                $header = 'Are you sure you want to delete this client?';
                $subheader = 'Reason for Deletion:';
                $status = 'delete';
            } else {
                $header = 'Are you sure you want to edit this client?';
                $subheader = 'Reason for Editing:';
                $status = 'edit';
            }
        @endphp
      <tr>
        <td colspan="2"><p>{{ $header }}</p>
            <label for="deleteReason" class="form-label">{{ $subheader }}</label>
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <textarea class="form-control" id="remark" name="remark" rows="3" required></textarea>
        </td>
      </tr>
      @endif
    </tbody>
  </table>
</div>
@endif

</div>
