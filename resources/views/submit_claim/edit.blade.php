@extends('layouts.app')

@section('title', 'Edit Claim')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .claim-type-group {
        display: grid;
        grid-template-columns: 1fr; /* Default to single column on small screens */
        gap: 1rem 2rem; /* Row gap, column gap */
        margin-bottom: 2rem;
    }
    @media (min-width: 768px) { /* md breakpoint */
        .claim-type-group {
            grid-template-columns: repeat(2, 1fr); /* Force exactly 2 equal columns on medium screens and up */
        }
    }
    .file-preview-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .file-preview-item:last-child {
        border-bottom: none;
    }
    .file-preview-item .file-name {
        flex-grow: 1;
        word-break: break-all;
    }
    .file-preview-item .delete-file-btn {
        flex-shrink: 0;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
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
    </div>

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
    @if (session()->has('error_import'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong> {{ session('error_import') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            {{-- Form action updated to 'update' route and method spoofing for PUT/PATCH --}}
            <form action="{{ route('v1.submit-claim.update', ['id' => $dataClaimItem->obfuscated_id]) }}" method="POST" class="p-4" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Or @method('PATCH') --}}

                {{-- Hidden input for submit_claim_id for convenience --}}
                <input type="hidden" name="submit_claim_id" value="{{ $dataClaimItem->obfuscated_id }}">

                <div class="row mb-3">
                    <div class="col-md-10">
                        <label class="form-label mb-3">Claim Type:</label>
                        <div class="claim-type-group">
                            {{-- Loop through claim types fetched from the database --}}
                            @foreach($claimTypes as $claimType)
                                <div class="form-check">
                                    <input class="form-check-input claim-type-radio" type="radio" name="claim_type_id" id="claimType{{ $claimType->id }}" value="{{ $claimType->id }}"
                                        {{ (old('claim_type_id', $dataClaimItem->claim_type_id) == $claimType->id) ? 'checked' : '' }}> {{-- Check based on existing data or old input --}}
                                    <label class="form-check-label" for="claimType{{ $claimType->id }}">
                                        {{ $claimType->name }}
                                    </label>
                                    @if($claimType->id == 10) {{-- Assuming 10 is the ID for "Miscellaneous/Other" --}}
                                        <div class="mb-3 mt-3" id="otherClaimTypeNameGroup" style="display: {{ (old('claim_type_id', $dataClaimItem->claim_type_id) == 10) ? 'block' : 'none' }};">
                                            <label for="other_claim_type_name" class="form-label">Other Claim Type Name:</label>
                                            <input type="text" class="form-control" id="other_claim_type_name" name="other_claim_type_name"
                                                placeholder="Enter specific claim type name" value="{{ old('other_claim_type_name', $dataClaim->other_claim_type_name ?? '') }}">
                                            <div class="invalid-feedback">
                                                Please enter a name for the miscellaneous claim type.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="start_at" class="form-label">Start Date</label>
                        <input type="date" name="start_at" id="start_at" class="form-control" value="{{ old('start_at', $dataClaimItem->start_at ? \Carbon\Carbon::parse($dataClaimItem->start_at)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_at" class="form-label">End Date</label>
                        <input type="date" name="end_at" id="end_at" class="form-control" value="{{ old('end_at', $dataClaimItem->end_at ? \Carbon\Carbon::parse($dataClaimItem->end_at)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="currency" class="form-label">Claim Currency</label>
                        <select class="form-control" id="currency" name="currency">
                            <option value="SGD" {{ old('currency', $dataClaimItem->currency) == 'SGD' ? 'selected' : '' }}>SGD</option>
                            <option value="MYR" {{ old('currency', $dataClaimItem->currency) == 'MYR' ? 'selected' : '' }}>MYR</option>
                            <option value="IDR" {{ old('currency', $dataClaimItem->currency) == 'IDR' ? 'selected' : '' }}>IDR</option>
                            </select>
                    </div>
                    <div class="col-md-4">
                        <label for="amount" class="form-label">Claim Amount:</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="Enter Claim Amount" value="{{ old('amount', $dataClaimItem->amount) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Existing Documentation Files:</label>
                        <div id="existing-files-container" class="list-group mb-3">
                            @forelse($dataClaimItem->files as $file)
                                <div class="list-group-item d-flex justify-content-between align-items-center file-preview-item">
                                    <span class="file-name">
                                        <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="text-decoration-none">
                                            <i class="fas fa-file-alt me-2"></i> {{ $file->original_name }}
                                        </a>
                                        @if ($file->description)
                                            <small class="text-muted d-block ms-4">{{ $file->description }}</small>
                                        @endif
                                    </span>
                                    <button type="button" class="btn btn-outline-danger btn-sm delete-file-btn" data-file-id="{{ $file->id }}" title="Delete File">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @empty
                                <p class="text-muted">No existing documentation files.</p>
                            @endforelse
                        </div>

                        <label class="form-label">Upload New Documentation Files (PNG, JPG, PDF, DOC/DOCX)</label>
                        <div id="new-file-upload-container">
                            {{-- Initial file input will be added by JavaScript or rendered if old input exists --}}
                            @if(old('project_files'))
                                @foreach(old('project_files') as $index => $oldFile)
                                    <div class="file-upload-item">
                                        <div class="input-group mb-2">
                                            <input type="file" name="project_files[]" class="form-control" accept=".png,.jpg,.pdf,.doc,.docx">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-file-input"><i class="fas fa-trash"></i></button>
                                        </div>
                                        <input type="text" name="project_file_descriptions[]" class="form-control mt-1" placeholder="Please enter file description" value="{{ old('project_file_descriptions.' . $index) }}">
                                    </div>
                                @endforeach
                            @else
                                {{-- This is the initial input if no old data --}}
                                <div class="file-upload-item">
                                    <div class="input-group mb-2">
                                        <input type="file" name="project_files[]" class="form-control" accept=".png,.jpg,.pdf,.doc,.docx">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-file-input" style="display: none;"><i class="fas fa-trash"></i></button>
                                    </div>
                                    <input type="text" name="project_file_descriptions[]" class="form-control mt-1" placeholder="Please enter file description">
                                </div>
                            @endif
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mt-1" id="add-more-files-btn"><i class="fas fa-plus"></i> Add Another File</button>
                        <small class="form-text text-muted d-block mt-2">Max file size: 10MB per file. Allowed types: PNG, JPG, PDF, DOC, DOCX.</small>
                    </div>

                    <div class="col-md-6">
                        <label for="claim_purpose" class="form-label">Claim Purpose:</label>
                        <textarea class="form-control" id="claim_purpose" name="claim_purpose">{{ old('claim_purpose', $dataClaimItem->description) }}</textarea>
                    </div>
                </div>

                <div class="col-md-12">
                    <h6>Submit Claim Header</h6>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="description" class="form-label">Description:</label>
                        <textarea class="form-control" id="description" name="description">{{ old('description', $dataClaimItem->submitClaim->description) }}</textarea>
                    </div>
                    <div class="col-md-5">
                        <label for="serial_number" class="form-label">Serial Number</label>
                        <input type="text" class="form-control" id="serial_number" name="serial_number" value="{{ $dataClaimItem->submitClaim->serial_number }}" disabled>
                    </div>
                </div>

                <div class="col-12">
                    <a href="{{ route('v1.submit-claim.detail', ['id' => $dataClaimItem->submitClaim->obfuscated_id]) }}" class="btn btn-warning">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Claim</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        const MISCELLANEOUS_CLAIM_TYPE_ID = '10'; // IMPORTANT: Match this to the actual ID in your database

        // Function to show/hide the "Other Claim Type Name" input
        function toggleOtherClaimTypeNameInput() {
            const selectedClaimTypeId = $('input[name="claim_type_id"]:checked').val();
            const otherClaimTypeNameGroup = $('#otherClaimTypeNameGroup');
            const otherClaimTypeNameInput = $('#other_claim_type_name');

            if (selectedClaimTypeId === MISCELLANEOUS_CLAIM_TYPE_ID) {
                otherClaimTypeNameGroup.show();
                otherClaimTypeNameInput.attr('required', true); // Make it required when visible
            } else {
                otherClaimTypeNameGroup.hide();
                otherClaimTypeNameInput.attr('required', false); // Make it not required when hidden
                otherClaimTypeNameInput.val(''); // Clear the value when hidden
                otherClaimTypeNameInput.removeClass('is-invalid'); // Remove validation feedback
            }
        }

        // Initial call on page load
        toggleOtherClaimTypeNameInput();

        // Event listener for changes on any claim type radio button
        $('.claim-type-radio').on('change', function() {
            toggleOtherClaimTypeNameInput();
        });

        // --- Logic for New File Uploads ---
        const $newFileUploadContainer = $('#new-file-upload-container');
        const $addMoreFilesBtn = $('#add-more-files-btn');
        const MAX_NEW_FILES = 5; // Max files allowed for new uploads

        // Function to update the visibility of "Remove" buttons for new files
        function updateNewFileRemoveButtonVisibility() {
            if ($newFileUploadContainer.children('.file-upload-item').length > 1) {
                $newFileUploadContainer.find('.remove-file-input').show();
            } else {
                $newFileUploadContainer.find('.remove-file-input').hide(); // Hide if only one remains
            }

            // Disable add button if max new files reached
            if ($newFileUploadContainer.children('.file-upload-item').length >= MAX_NEW_FILES) {
                $addMoreFilesBtn.prop('disabled', true);
            } else {
                $addMoreFilesBtn.prop('disabled', false);
            }
        }

        // Function to add a new file input field group
        function addFileInputField() {
            if ($newFileUploadContainer.children('.file-upload-item').length < MAX_NEW_FILES) {
                const newFileInputHtml = `
                    <div class="file-upload-item">
                        <div class="input-group mb-2">
                            <input type="file" name="project_files[]" class="form-control" accept=".png,.jpg,.pdf,.doc,.docx">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-file-input"><i class="fas fa-trash"></i></button>
                        </div>
                        <input type="text" name="project_file_descriptions[]" class="form-control mt-1" placeholder="Please enter file description">
                    </div>
                `;
                $newFileUploadContainer.append(newFileInputHtml);
                updateNewFileRemoveButtonVisibility();
            } else {
                alert(`You can upload a maximum of ${MAX_NEW_FILES} new files.`);
            }
        }

        // Initial setup for new file uploads on page load:
        // Ensure at least one input if no old data, and adjust button visibility.
        if ($newFileUploadContainer.children('.file-upload-item').length === 0) {
            addFileInputField(); // Add one empty file input if none exist (e.g., fresh load with no old input)
        } else {
            updateNewFileRemoveButtonVisibility(); // If old inputs exist, just update visibility
        }

        // Event listener for "Add Another File" button
        $addMoreFilesBtn.on('click', function() {
            addFileInputField();
        });

        // Event listener for "Remove" button on dynamically added file inputs
        $newFileUploadContainer.on('click', '.remove-file-input', function() {
            $(this).closest('.file-upload-item').remove(); // Remove the entire parent .file-upload-item
            updateNewFileRemoveButtonVisibility();
        });

        // --- Handle Existing File Deletion via AJAX ---
        const CLAIM_OBFUSCATED_ID = "{{ $dataClaim->obfuscated_id ?? '' }}"; // Get claim ID from Blade

        $(document).on('click', '.delete-file-btn', function() { // Use event delegation for dynamically added buttons
            const $button = $(this);
            const fileId = $button.data('file-id');

            if (!CLAIM_OBFUSCATED_ID) {
                alert('Claim ID not found for file deletion.');
                return;
            }

            if (!confirm('Are you sure you want to delete this file? This cannot be undone.')) {
                return;
            }

            $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...'); // Add spinner

            $.ajax({
                url: `/api/submit-claims/${CLAIM_OBFUSCATED_ID}/files/${fileId}`, // Adjust API endpoint for claims
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message || 'File deleted.');
                        $button.closest('.list-group-item').remove(); // Remove the list item from UI
                    } else {
                        alert(response.message || 'Failed to delete file.');
                        $button.prop('disabled', false).html('<i class="fas fa-trash"></i>'); // Reset button
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 403) {
                        errorMessage = 'You are not authorized to delete this file.';
                    }
                    alert(errorMessage);
                    console.error('Error deleting file:', xhr.responseText);
                    $button.prop('disabled', false).html('<i class="fas fa-trash"></i>'); // Reset button
                }
            });
        });

        // --- Select2 Custom Formatting Functions (if you use Select2) ---
        function getAvatarOrPlaceholder(userOption) {
            const avatarUrl = userOption.avatar_url || (userOption.element ? $(userOption.element).data('avatar-url') : null);
            const initials = userOption.initials || (userOption.element ? $(userOption.element).data('initials') : null);
            const userName = userOption.text || userOption.id;

            if (avatarUrl) {
                return avatarUrl;
            } else {
                const fallbackInitials = initials || (userName ? userName.split(' ').map(n => n[0]).join('').toUpperCase() : '?');
                return `https://placehold.co/28x28/d0c5f3/333333?text=${encodeURIComponent(fallbackInitials)}`;
            }
        }

        function formatUserResult(user) {
            if (!user.id) return user.text;
            const avatarSrc = getAvatarOrPlaceholder(user);
            return $(`<span class="d-flex align-items-center"><img src="${avatarSrc}" class="rounded-circle me-2" alt="${user.text || 'User'} Avatar" height="28px" width="28px" /><span>${user.text}</span></span>`);
        }

        function formatUserSelection(user) {
            if (!user.id) return user.text;
            const avatarSrc = getAvatarOrPlaceholder(user);
            return $(`<span class="d-flex align-items-center"><img src="${avatarSrc}" class="rounded-circle me-2" alt="${user.text || 'User'} Avatar" height="28px" width="28px" /><span>${user.text}</span></span>`);
        }

        // --- Select2 Initialization ---
        // Only initialize if you have an element with ID 'addProjectMembers'
        // $('#addProjectMembers').select2({
        //     placeholder: "Select project members",
        //     allowClear: true,
        //     templateResult: formatUserResult,
        //     templateSelection: formatUserSelection,
        // });
    });
</script>
@endsection