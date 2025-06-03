{{-- resources/views/components/filter-form.blade.php --}}

@props(['salesPersons', 'industries', 'countries', 'formId' => 'filters-form'])

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
            <button id="download-csv" class="btn btn-outline-primary">Request To Download CSV</button>
            <button id="download-pdf" class="btn btn-outline-danger">Request To Download PDF</button>
            <button type="button" id="reset-filters" class="btn btn-secondary">Reset Filters</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
    const downloadStatusMessage = $('#download-status-message');
    let pollingInterval; // To store the interval ID for polling, allows us to stop it

    /**
     * Sends an AJAX request to the backend to create a new download request.
     * @param {string} fileType - The type of file requested ('csv' or 'pdf').
     */
    function sendDownloadRequest(fileType) {
        // Clear any previous messages and show loading state
        downloadStatusMessage.html('<div class="alert alert-info">Requesting download for ' + fileType.toUpperCase() + '... Please wait for admin approval.</div>');
        $('#download-csv, #download-pdf').prop('disabled', true); // Disable buttons to prevent multiple requests

        $.ajax({
            url: '/api/download-request', // Your API endpoint to create a request (Laravel route: POST /api/download-request)
            method: 'POST',
            data: {
                file_type: fileType,
                // If your download needs to be based on current table filters, pass them here:
                // filters: getTableFilters(), // You'd need a function to gather these filters
                _token: '{{ csrf_token() }}' // Essential for Laravel CSRF protection
            },
            success: function(response) {
                if (response.request_id) {
                    downloadStatusMessage.html('<div class="alert alert-warning">Your request (ID: ' + response.request_id + ') has been submitted. Waiting for admin approval...</div>');
                    startPollingStatus(response.request_id); // Start polling the server for status updates
                } else {
                    // Handle cases where request_id might be missing from response (though unlikely with proper backend)
                    downloadStatusMessage.html('<div class="alert alert-danger">Error submitting request: ' + (response.message || 'Unknown error') + '</div>');
                    $('#download-csv, #download-pdf').prop('disabled', false); // Re-enable buttons on backend error
                }
            },
            error: function(xhr) {
                // Handle AJAX error (e.g., network error, 500 server error)
                downloadStatusMessage.html('<div class="alert alert-danger">Error submitting request: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Server error occurred. Please try again.') + '</div>');
                $('#download-csv, #download-pdf').prop('disabled', false); // Re-enable buttons on AJAX error
            }
        });
    }

    /**
     * Starts polling the server to check the status of a specific download request.
     * @param {number} requestId - The ID of the download request to poll.
     */
    function startPollingStatus(requestId) {
        // Clear any existing polling interval to prevent multiple intervals running simultaneously
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }

        // Set up polling to check status every 5 seconds
        pollingInterval = setInterval(function() {
            $.ajax({
                url: '/api/download-status/' + requestId, // Your API endpoint to check status (Laravel route: GET /api/download-status/{id})
                method: 'GET',
                success: function(response) {
                    if (response.status === 'approved') {
                        clearInterval(pollingInterval); // Stop polling when approved
                        downloadStatusMessage.html('<div class="alert alert-success">Admin approved! Initiating download...</div>');

                        if (response.download_url) {
                            // Trigger the actual download by redirecting the browser
                            window.location.href = response.download_url;
                        } else {
                            downloadStatusMessage.html('<div class="alert alert-danger">Approval received, but no download URL found. Please contact support.</div>');
                        }
                        $('#download-csv, #download-pdf').prop('disabled', false); // Re-enable buttons

                    } else if (response.status === 'rejected') {
                        clearInterval(pollingInterval); // Stop polling when rejected
                        downloadStatusMessage.html('<div class="alert alert-danger">Your download request has been rejected by the admin.</div>');
                        $('#download-csv, #download-pdf').prop('disabled', false); // Re-enable buttons

                    } else {
                        // Request is still 'pending' or in another intermediate status
                        downloadStatusMessage.html('<div class="alert alert-warning">Your request (ID: ' + requestId + ') is still ' + response.status + '. Waiting for admin approval...</div>');
                    }
                },
                error: function(xhr) {
                    clearInterval(pollingInterval); // Stop polling on error
                    downloadStatusMessage.html('<div class="alert alert-danger">Error checking status: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Server error occurred.') + ' Please refresh and try again.</div>');
                    $('#download-csv, #download-pdf').prop('disabled', false); // Re-enable buttons
                }
            });
        }, 5000); // Poll every 5 seconds (adjust this interval as needed)
    }

    // Attach click event listeners to your buttons
    $('#download-csv').on('click', function() {
        sendDownloadRequest('csv');
    });

    $('#download-pdf').on('click', function() {
        sendDownloadRequest('pdf');
    });

    // Optional: If you need to pass table filters to the backend
    // function getTableFilters() {
    //     return {
    //         sales_person: $('#filter-sales-person').val(),
    //         industry: $('#filter-industry').val(),
    //         country: $('#filter-country').val()
    //     };
    // }
});
    </script>
