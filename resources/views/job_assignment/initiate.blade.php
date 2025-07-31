@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5>Send Bulk Emails</h5>
    </div>
    <div class="card-body">
        <form id="bulkEmailForm">
            @csrf
            <div class="mb-3">
                <label for="recipientEmails" class="form-label">Recipient Emails (comma-separated)</label>
                <textarea class="form-control" id="recipientEmails" name="recipient_emails" rows="3" required>test1@example.com,test2@example.com</textarea>
            </div>
            <div class="mb-3">
                <label for="emailSubject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="emailSubject" name="email_subject" required value="Test Bulk Email">
            </div>
            <div class="mb-3">
                <label for="emailBody" class="form-label">Body</label>
                <textarea class="form-control" id="emailBody" name="email_body" rows="5" required>Hello, this is a test bulk email from our system.</textarea>
            </div>
            <button type="submit" class="btn btn-primary" id="sendBulkEmailBtn">Send Emails</button>
        </form>

        <hr class="my-4">

        {{-- Progress Display Area --}}
        <div id="emailProgressContainer" class="d-none">
            <h5>Email Sending Progress: <span id="progressStatus" class="badge bg-secondary">Pending</span></h5>
            <div class="progress mb-2">
                <div id="emailProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
            <p>Sent: <span id="sentCount">0</span> / <span id="totalRecipients">0</span></p>
            <p>Failed: <span id="failedCount">0</span></p>
            <div id="progressAlert" class="alert mt-3 d-none" role="alert"></div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        const bulkEmailForm = $('#bulkEmailForm');
        const sendBulkEmailBtn = $('#sendBulkEmailBtn');
        const emailProgressContainer = $('#emailProgressContainer');
        const emailProgressBar = $('#emailProgressBar');
        const progressStatus = $('#progressStatus');
        const sentCountSpan = $('#sentCount');
        const totalRecipientsSpan = $('#totalRecipients');
        const failedCountSpan = $('#failedCount');
        const progressAlert = $('#progressAlert');

        let progressPollingInterval; // To store the interval ID

        // Function to update the progress UI
        function updateProgressUI(data) {
            const percentage = data.progress_percentage;
            emailProgressBar.css('width', percentage + '%').attr('aria-valuenow', percentage).text(percentage + '%');
            sentCountSpan.text(data.sent_count);
            totalRecipientsSpan.text(data.total_recipients);
            failedCountSpan.text(data.failed_count);
            progressStatus.text(data.status);

            // Update status badge color
            progressStatus.removeClass('bg-secondary bg-info bg-success bg-danger').addClass(function() {
                switch (data.status) {
                    case 'pending': return 'bg-secondary';
                    case 'in_progress': return 'bg-info';
                    case 'completed': return 'bg-success';
                    case 'failed': return 'bg-danger';
                    default: return 'bg-secondary';
                }
            });

            // Show completion/failure message
            if (data.status === 'completed') {
                clearInterval(progressPollingInterval);
                progressAlert.removeClass('d-none alert-danger').addClass('alert-success').text('Bulk emails sent successfully!');
                sendBulkEmailBtn.prop('disabled', false).text('Send Emails');
            } else if (data.status === 'failed') {
                clearInterval(progressPollingInterval);
                progressAlert.removeClass('d-none alert-success').addClass('alert-danger').text('Bulk email sending failed. Check logs for details.');
                sendBulkEmailBtn.prop('disabled', false).text('Send Emails');
            }
        }

        // Function to start polling for progress
        function startProgressPolling(batchId) {
            emailProgressContainer.removeClass('d-none');
            progressAlert.addClass('d-none').empty(); // Clear previous alerts
            
            // Initial fetch
            fetchProgress(batchId);

            // Set up polling every 3 seconds
            progressPollingInterval = setInterval(function() {
                fetchProgress(batchId);
            }, 3000); // Poll every 3 seconds (adjust as needed)
        }

        // Function to fetch progress from the backend
        function fetchProgress(batchId) {
            $.ajax({
                url: `/bulk-email/progress/${batchId}`, // Use the named route
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        updateProgressUI(response);
                    } else {
                        clearInterval(progressPollingInterval);
                        progressAlert.removeClass('d-none alert-success').addClass('alert-danger').text(response.message || 'Failed to fetch progress.');
                        sendBulkEmailBtn.prop('disabled', false).text('Send Emails');
                    }
                },
                error: function(xhr) {
                    clearInterval(progressPollingInterval);
                    progressAlert.removeClass('d-none alert-success').addClass('alert-danger').text('Error fetching progress. Please check console.');
                    sendBulkEmailBtn.prop('disabled', false).text('Send Emails');
                    console.error('Progress polling error:', xhr.responseText);
                }
            });
        }

        // Handle form submission to initiate bulk send
        bulkEmailForm.on('submit', function(e) {
            e.preventDefault();

            sendBulkEmailBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...');
            progressAlert.addClass('d-none').empty(); // Clear any previous alerts
            emailProgressContainer.addClass('d-none'); // Hide progress container initially

            $.ajax({
                url: "{{ route('bulk_email.send') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        // Start polling for progress
                        startProgressPolling(response.batch_id);
                    } else {
                        sendBulkEmailBtn.prop('disabled', false).text('Send Emails');
                        progressAlert.removeClass('d-none alert-success').addClass('alert-danger').text(response.message || 'Failed to initiate email send.');
                    }
                },
                error: function(xhr) {
                    sendBulkEmailBtn.prop('disabled', false).text('Send Emails');
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    progressAlert.removeClass('d-none alert-success').addClass('alert-danger').html(errorMessage);
                    console.error('Initiate send error:', xhr.responseText);
                }
            });
        });

        // Clear interval if user navigates away or closes the page (optional)
        $(window).on('beforeunload', function() {
            if (progressPollingInterval) {
                clearInterval(progressPollingInterval);
            }
        });
    });
</script>

@endsection