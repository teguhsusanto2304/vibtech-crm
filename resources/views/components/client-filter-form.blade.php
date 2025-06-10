{{-- resources/views/components/filter-form.blade.php --}}

@props(['salesPersons', 'industries', 'countries','downloadFile', 'formId' => 'filters-form'])

<div id="{{ $formId }}" class="row g-3">
    @if($salesPersons)
        <div class="col-md-4">
            <label for="filter-sales-person" class="form-label">Sales Person</label>
            <select id="filter-sales-person" class="form-select">
                <option value="">All Sales Persons</option>
                <option value="-">Unassigned Sales Person</option>
                @foreach ($salesPersons as $salesPerson)
                    <option value="{{ $salesPerson->name }}">{{ $salesPerson->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-4">
        <label for="filter-industry" class="form-label">Industry</label>
        <select id="filter-industry" class="form-select">
            <option value="">All Industries</option>
            @foreach ($industries as $industry)
                <option value="{{ $industry->name }}">{{ $industry->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label for="filter-country" class="form-label">Country</label>
        <select id="filter-country" class="form-select">
            <option value="">All Countries</option>
            @foreach ($countries as $country)
                <option value="{{ $country->name }}">{{ $country->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8 d-flex align-items-end justify-content-start mt-4">
        <div class="btn-group" role="group">
                @php $pdf = 0; $csv=0; @endphp
                @forelse ($downloadFile as $row )
                    @if($row->file_type=='csv' && $row->data_status==0)
                        <button id="download-csv1" class="btn btn-outline-primary">Request To Download CSV</button>
                    @elseif($row->file_type=='csv' && $row->data_status==1)
                        <button id="download-csv1" class="btn btn-outline-primary" disabled>Waiting response</button>
                    @elseif($row->file_type=='csv' && $row->data_status==2)
                        <button id="download-csv" class="btn btn-outline-primary">Download CSV</button>
                    @endif
                    @if($row->file_type=='pdf' && $row->data_status==0)
                        <button id="download-pdf1" class="btn btn-outline-danger">Request To Download PDF</button>
                    @elseif($row->file_type=='pdf' && $row->data_status==1)
                        <button id="download-pdf1" class="btn btn-outline-danger" disabled>Waiting response</button>
                    @elseif($row->file_type=='pdf' && $row->data_status==2)
                        <button id="download-pdf" class="btn btn-outline-danger">Download PDF</button>
                    @endif
                    @if ($row->file_type=='pdf')
                       @php $pdf ++;  @endphp
                    @endif
                    @if ($row->file_type=='csv')
                       @php $csv ++;  @endphp
                    @endif
                @empty
                @endforelse
                @if($pdf == 0)
                    <button id="download-pdf1" class="btn btn-outline-danger">Request To Download PDF</button>
                @endif
                @if($csv == 0)
                    <<button id="download-csv1" class="btn btn-outline-primary">Request To Download CSV</button>
                @endif
                <button type="button" id="reset-filters" class="btn btn-secondary">Reset Filters</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const downloadStatusMessage = $('#download-status-message');
        const csvButton = $('#download-csv1');
        const pdfButton = $('#download-pdf1');

        let pollingInterval;     // To store the interval ID for polling
        let currentRequestId = null; // Stores the ID of the ongoing request
        let lastRequestedFileType = null; // Stores 'csv' or 'pdf' for the current request

        // Function to reset buttons to their initial "request" state
        function resetButtons() {
            csvButton.prop('disabled', false).text('Request To Download CSV');
            pdfButton.prop('disabled', false).text('Request To Download PDF');
        }

        // Function to set buttons to "waiting" state
        function setWaitingState() {
            csvButton.prop('disabled', true).text('Waiting response');
            pdfButton.prop('disabled', true).text('Waiting response');
        }

        // Function to send the initial download request
        function sendDownloadRequest(fileType) {
            // Clear any previous messages and set initial UI state
            downloadStatusMessage.html('');
            //setWaitingState();
            if(fileType==='csv'){
                csvButton.prop('disabled', true).text('Waiting response');
            } else {
                pdfButton.prop('disabled', true).text('Waiting response');
            }

            lastRequestedFileType = fileType; // Store the type for later reference

            $.ajax({
                url: '{{ route('v1.client-database.request-download') }}', // Your API endpoint to create a request
                method: 'POST',
                data: {
                    user_id: $('#filter-sales-person').val(),
                    total_data:2,
                    file_type: fileType,
                    _token: '{{ csrf_token() }}' // For CSRF protection in Laravel
                },
                success: function(response) {
                    if (response.request_id) {
                        currentRequestId = response.request_id; // Store the ID of the new request
                        if(response.request_id===1){
                            $('#msg').html(`
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <p>${response.message}</p>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    `);
                            if(fileType==='csv'){
                                csvButton.prop('disabled', false).text('Request To Download CSV');
                            } else {
                                pdfButton.prop('disabled', false).text('Request To Download PDF');
                            }
                        } else {
                            $('#msg').html(`
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <p>${response.message}</p>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    `);
                        }
                        const reloadDelay = 5000;
                        setTimeout(function() {
                            //location.reload();
                        }, reloadDelay);
                    } else {
                        downloadStatusMessage.html('<div class="alert alert-danger">Error submitting request: ' + (response.message || 'Unknown error') + '</div>');
                        //resetButtons(); // Reset buttons on error
                    }
                },
                error: function(xhr) {
                    downloadStatusMessage.html('<div class="alert alert-danger">Error submitting request: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Server error') + '</div>');
                    resetButtons(); // Reset buttons on error
                }
            });
        }



        // Attach click event handlers to your buttons
        csvButton.on('click', function() {
            sendDownloadRequest('csv');
        });

        pdfButton.on('click', function() {
            sendDownloadRequest('pdf');
        });

    });
</script>
