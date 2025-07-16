@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">{{ $item }}</a>
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h3>{{ $title }}</h3>

            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Oops! Something went wrong:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div id="msg" class="mb-3"></div> <!-- For AJAX messages -->
        </div>

        <div class="card">
            
            <div class="card-body">
                <form id="meetingMinutesForm">
                    @csrf

                    <div class="mb-3">
                        <label for="topic" class="form-label">Meeting Topic:</label>
                        <input type="text" class="form-control" id="topic" name="topic" required>
                        <div class="invalid-feedback" id="topicFeedback"></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="meeting_date" class="form-label">Date:</label>
                            <input type="date" class="form-control" id="meeting_date" name="meeting_date" required>
                            <div class="invalid-feedback" id="meeting_dateFeedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="start_time" class="form-label">Start Time:</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                            <div class="invalid-feedback" id="start_timeFeedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="end_time" class="form-label">End Time:</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                            <div class="invalid-feedback" id="end_timeFeedback"></div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5>Attendees:</h5>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-check-all-container">
                                <input class="form-check-input" type="checkbox" id="selectAllAttendees">
                                <label class="form-check-label ms-2" for="selectAllAttendees">Select All</label>
                            </div>
                        </div>
                        @foreach($users as $user)
                            <div class="col-md-3 col-sm-6 col-xs-12 mb-2">
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input attendee-checkbox" type="checkbox" id="attendee-{{ $user->id }}" value="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-avatar="{{ $user->avatar_url ?? 'https://placehold.co/40x40/cccccc/333333?text=N/A' }}">
                                    <label class="form-check-label ms-2" for="attendee-{{ $user->id }}">
                                        <img src="{{ $user->avatar_url ?? 'https://placehold.co/40x40/cccccc/333333?text=N/A' }}" alt="{{ $user->name }} Avatar" class="rounded-circle me-2" width="30" height="30">
                                        {{ $user->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="speakerNotesContainer" class="mt-4">
                        <!-- Speaker notes textareas will be dynamically added here -->
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary" id="submitMinutesBtn">Submit Minutes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            const speakerNotesContainer = $('#speakerNotesContainer');
            const users = @json($users); // Pass users data from PHP to JS

            // Function to render/remove speaker notes textarea
            function toggleSpeakerNotes(userId, userName, userAvatar, isChecked) {
                const textareaId = `speaker-notes-textarea-${userId}`;
                const feedbackId = `speaker-notes-feedback-${userId}`;
                const groupContainerId = `attendee-notes-group-${userId}`; // ID for the whole group div

                if (isChecked) {
                    if ($(`#${groupContainerId}`).length === 0) { // Check if the group container exists
                        const noteHtml = `
                            <div class="mb-3 p-3 border rounded bg-light" id="${groupContainerId}">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="${userAvatar}" alt="${userName} Avatar" class="rounded-circle me-2" width="30" height="30">
                                    <h6 class="mb-0">${userName} Notes:</h6>
                                </div>
                                <input type="hidden" name="attendees[${userId}][user_id]" value="${userId}">
                                <textarea class="form-control bg-white" id="${textareaId}" name="attendees[${userId}][speaker_notes]" rows="3" placeholder="Enter notes for ${userName}" ></textarea>
                                <div class="invalid-feedback" id="${feedbackId}"></div>
                            </div>
                        `;
                        speakerNotesContainer.append(noteHtml);
                        console.log(`Added notes group for user ${userId} with ID: #${groupContainerId}`); // DEBUG
                    }
                } else {
                    console.log(`Attempting to remove notes group for user ${userId} with ID: #${groupContainerId}`); // DEBUG
                    const elementToRemove = $(`#${groupContainerId}`);
                    if (elementToRemove.length > 0) {
                        elementToRemove.remove();
                        console.log(`Successfully removed notes group for user ${userId}.`); // DEBUG
                    } else {
                        console.log(`Notes group for user ${userId} with ID: #${groupContainerId} not found for removal.`); // DEBUG
                    }
                }
            }

            // Event listener for individual attendee checkboxes
            $('.attendee-checkbox').on('change', function() {
                const userId = $(this).val();
                const userName = $(this).data('user-name');
                const userAvatar = $(this).data('user-avatar');
                const isChecked = $(this).is(':checked');
                console.log(`Checkbox for user ${userId} changed. Is checked: ${isChecked}`); // DEBUG
                toggleSpeakerNotes(userId, userName, userAvatar, isChecked);
            });

            // Event listener for "Select All" checkbox
            $('#selectAllAttendees').on('change', function() {
                const isChecked = $(this).is(':checked');
                console.log(`Select All checkbox changed. Is checked: ${isChecked}`); // DEBUG
                $('.attendee-checkbox').prop('checked', isChecked).trigger('change'); // Trigger change for each to update notes
            });

            // Handle form submission
            $('#meetingMinutesForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = new FormData(this);

                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');
                $('#msg').empty();

                $.ajax({
                    url: '{{ route("v1.meeting-minutes.store") }}', 
                    type: 'POST',
                    data: formData,
                    processData: false, 
                    contentType: false, 
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#submitMinutesBtn').prop('disabled', true).text('Submitting...');
                    },
                    success: function(response) {
                        window.location.href = '{{ route("v1.meeting-minutes.list") }}'; 
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            if (xhr.responseJSON.errors) {
                                for (const field in xhr.responseJSON.errors) {
                                    let input = form.find(`[name="${field}"]`);
                                    if (input.length) {
                                        input.addClass('is-invalid');
                                        input.next('.invalid-feedback').text(xhr.responseJSON.errors[field][0]);
                                    } else {
                                        const match = field.match(/attendees\.(\d+)\.speaker_notes/);
                                        if (match) {
                                            const userId = match[1];
                                            const noteTextarea = $(`#speaker-notes-textarea-${userId}`);
                                            if (noteTextarea.length) {
                                                noteTextarea.addClass('is-invalid');
                                                $(`#speaker-notes-feedback-${userId}`).text(xhr.responseJSON.errors[field][0]);
                                            }
                                        } else {
                                            console.warn(`Validation error for unknown field: ${field}`, xhr.responseJSON.errors[field][0]);
                                        }
                                    }
                                }
                            }
                        }
                        displayMessage('danger', errorMessage);
                    },
                    complete: function() {
                        $('#submitMinutesBtn').prop('disabled', false).text('Submit Minutes');
                    }
                });
            });

            // Helper function to display messages
            function displayMessage(type, message) {
                const msgDiv = $('#msg');
                msgDiv.html(`
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
                $('html, body').animate({
                    scrollTop: msgDiv.offset().top - 100
                }, 500);
            }
        });
    </script>

@endsection


    <!-- Ensure jQuery and Bootstrap JS are loaded before this script -->
    