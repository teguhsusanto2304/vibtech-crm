@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

                        </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- custom-icon Breadcrumb-->
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
                <form action="{{ route('v1.submit-claim.store') }}" method="POST" class="p-4" enctype="multipart/form-data">
                    @csrf
                    <!-- Project Name -->
                     <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="claimAmount" class="form-label">Claim Title:</label>
                            <textarea class="form-control" id="description" name="description" readonly>@if(!is_null($dataClaim)) {{ $dataClaim->description }} @endif</textarea>
                        </div>
                        @if(!is_null($dataClaim))
                        <div class="col-md-5">
                            <label for="claimAmount" class="form-label">Serial Number</label>
                            <input type="hidden" name="submit_claim_id" value="{{ $dataClaim->obfuscated_id }}">                            
                            <input type="text"  class="form-control" id="serial_number" name="serial_number" value="{{ $dataClaim->serial_number }}" disabled>
                        </div>
                        @endif
                        
                    </div>
                    <div class="row mb-3">
                        
                        <div class="col-md-10">
                            <label class="form-label mb-3">Claim Type:</label>
                            <div class="claim-type-group">
                                {{-- Loop through claim types fetched from the database --}}
                                @foreach($claimTypes as $claimType)
                                    <div class="form-check">
                                        <input class="form-check-input claim-type-radio" type="radio" name="claim_type_id" id="claimType{{ $claimType->id }}" value="{{ $claimType->id }}"
                                            {{ $loop->first ? 'checked' : '' }}> {{-- Check the first one by default --}}
                                        <label class="form-check-label" for="claimType{{ $claimType->id }}">
                                            {{ $claimType->name }}
                                        </label>
                                        @if($claimType->id==10)
                                                                        <!-- NEW: Input field for Miscellaneous/Other claim type name -->
                                        <div class="mb-3 mt-3" id="otherClaimTypeNameGroup" style="display: none;">
                                            <label for="other_claim_type_name" class="form-label">Other Claim Type Name:</label>
                                            <input type="text" class="form-control" id="other_claim_type_name" name="other_claim_type_name" placeholder="Enter specific claim type name">
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

                    <!-- Project Start Date & End Date -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="projectStartDate" class="form-label">Start Date</label>
                            <input type="date" name="start_at" id="start_at" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="projectEndDate" class="form-label">End Date</label>
                            <input type="date" name="end_at" id="end_at" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="projectDescription" class="form-label">Claim Currency</label>
                            <select class="form-control" id="currency" name="currency">
                                <option value="SGD" selected>SGD</option>
                                <option value="MYR">MYR</option>
                                <option value="IDR">IDR</option>
                                <!-- Add more currencies as needed -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="claimAmount" class="form-label">Claim Amount:</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="Enter Claim Amount">
                        </div>
                    </div>

                    <!-- Project Description -->
                    
                    
                    <!-- Members and Manager -->
                    <div class="row mb-3">
                        
                        
                        <!-- Project Files Upload Field ADDED HERE -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload New Documentation Files (PNG, JPG, PDF, DOC/DOCX)</label>
                            <div id="new-file-upload-container">
                                {{-- Initial file input will be added by JavaScript or rendered if old input exists --}}
                                @if(old('project_files'))
                                    @foreach(old('project_files') as $index => $oldFile)
                                        <div class="input-group mb-2 file-upload-item">
                                            <input type="file" name="project_files[]" class="form-control" accept=".png,.jpg,.pdf,.doc,.docx">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-file-input"><i class="fas fa-trash"></i></button>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- This is the initial input if no old data --}}
                                    <div class="file-upload-item">
                                            <div class="input-group mb-2">
                                                <input type="file" name="project_files[]" class="form-control" accept=".png,.jpg,.pdf,.doc,.docx">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-file-input" style="display: none;"><i class="fas fa-trash"></i></button>
                                            </div>
                                            <input type="text" name="project_file_descriptions[]" class="form-control mt-1" placeholder="please enter file description">
                                    </div>
                                @endif
                                
                            </div>
                            
                            <button type="button" class="btn btn-outline-primary btn-sm mt-1" id="add-more-files-btn"><i class="fas fa-plus"></i> Add Another File</button>
                            <small class="form-text text-muted d-block mt-2">Max file size: 3MB per file. Allowed types: PNG,JPG,PDF, DOC, DOCX.</small>
                        </div>
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

                                toggleOtherClaimTypeNameInput();

                                // Event listener for changes on any claim type radio button
                                $('.claim-type-radio').on('change', function() {
                                    toggleOtherClaimTypeNameInput();
                                });

                                const $newFileUploadContainer = $('#new-file-upload-container');
                                const $addMoreFilesBtn = $('#add-more-files-btn');
                                let indexNewFileUpload = 0;

                                // Function to update the visibility of "Remove" buttons
                                function updateRemoveButtonVisibility() {
                                    // Show remove button if there's more than one file input field in the NEW upload section
                                    if ($newFileUploadContainer.children('.file-upload-item').length > 1) {
                                        $newFileUploadContainer.find('.remove-file-input').show();
                                    } else {
                                        $newFileUploadContainer.find('.remove-file-input').hide(); // Hide if only one remains
                                    }
                                }

                                // Function to add a new file input field group
                                function addFileInputField() {
                                    indexNewFileUpload++;
                                    if(indexNewFileUpload<=10){
                                        const newFileInputHtml = `
                                            <div class="file-upload-item">
                                                <div class="input-group mb-2">
                                                    <input type="file" name="project_files[]" class="form-control" accept=".png,.jpg,.pdf,.doc,.docx">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-file-input"><i class="fas fa-trash"></i></button>
                                                </div>
                                                <input type="text" name="project_file_descriptions[]" class="form-control mt-1" placeholder="please enter file description">
                                            </div>
                                        `;
                                        $newFileUploadContainer.append(newFileInputHtml);
                                        updateRemoveButtonVisibility();
                                    } else {
                                        alert('you can not add more than 10 files !');
                                    }
                                    
                                    
                                }

                                // Initial setup on page load:
                                // If no file input fields are present (e.g., fresh create form), add one.
                                // Otherwise, just adjust visibility of existing remove buttons (e.g., after validation error).
                                if ($newFileUploadContainer.children('.file-upload-item').length === 0) {
                                    addFileInputField();
                                } else {
                                    updateRemoveButtonVisibility();
                                }

                                // Event listener for "Add Another File" button
                                $addMoreFilesBtn.on('click', function() {
                                    addFileInputField();
                                });

                                // Event listener for "Remove" button on dynamically added file inputs
                                // Using event delegation because buttons are added dynamically
                                $newFileUploadContainer.on('click', '.remove-file-input', function() {
                                    $(this).closest('.file-upload-item').remove(); // Remove the entire parent .input-group
                                    updateRemoveButtonVisibility();
                                });

                                // --- Handle File Deletion via AJAX (for existing files) ---
                                // (Keep your existing delete-file-btn logic, ensure project obfuscated_id is passed)
                                $(document).on('click', '.delete-file-btn', function() { // Use event delegation for dynamically added buttons
                                    const $button = $(this);
                                    const fileId = $button.data('file-id');
                                    const projectId = "{{ $project->obfuscated_id ?? '' }}"; // Get project ID from Blade

                                    if (!projectId) {
                                        alert('Project ID not found for file deletion.');
                                        return;
                                    }

                                    if (!confirm('Are you sure you want to delete this file? This cannot be undone.')) {
                                        return;
                                    }

                                    $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...'); // Add spinner

                                    $.ajax({
                                        url: `/api/projects/${projectId}/files/${fileId}`, // Adjust API endpoint
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
                            });
                        </script>
                        <div class="col-md-6">
                            <label for="claimAmount" class="form-label">Claim Purpose:</label>
                            <textarea class="form-control" id="claim_purpose" name="claim_purpose" ></textarea>
                        </div>
                    </div>
                                       

                    <!-- Buttons -->
                    <div class="col-12">
                            <a href="
                            @if(!is_null($dataClaim))
                                {{ route('v1.submit-claim.detail', ['id' => $dataClaim->obfuscated_id]) }}
                             @else
                                {{ route('v1.submit-claim') }}
                            @endif" class="btn btn-warning">Cancel</a>
                            <button type="submit" class="btn btn-primary">@if(!is_null($dataClaim)) Add Claim @else Create Claim @endif</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- jQuery (Select2 depends on jQuery) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Select2 JS CDN -->
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // --- Select2 Custom Formatting Functions ---

        // Function to get or generate avatar URL
        function getAvatarOrPlaceholder(userOption) {
            // Check if userOption has a direct avatar_url property (common if loaded via AJAX)
            // or if it has an element (common if loaded from static <option> tags)
            const avatarUrl = userOption.avatar_url || (userOption.element ? $(userOption.element).data('avatar-url') : null);
            const initials = userOption.initials || (userOption.element ? $(userOption.element).data('initials') : null);
            const userName = userOption.text || userOption.id; // Fallback to ID if no text

            if (avatarUrl) {
                return avatarUrl;
            } else {
                // Generate placeholder with initials
                const fallbackInitials = initials || (userName ? userName.split(' ').map(n => n[0]).join('').toUpperCase() : '?');
                return `https://placehold.co/28x28/d0c5f3/333333?text=${encodeURIComponent(fallbackInitials)}`;
            }
        }

        // Formats options in the dropdown results list
        function formatUserResult(user) {
            if (!user.id) {
                return user.text; // Return original text for placeholder/search input
            }

            const avatarSrc = getAvatarOrPlaceholder(user);
            
            // Create a jQuery object for the option HTML
            const $container = $(
                `<span class="d-flex align-items-center">
                    <img src="${avatarSrc}" class="rounded-circle me-2" alt="${user.text || 'User'} Avatar" height="50px" width="50px" />
                    <span>${user.text}</span>
                </span>`
            );
            return $container;
        }

        // Formats selected items within the Select2 input box
        function formatUserSelection(user) {
            if (!user.id) {
                return user.text; // Return original text for placeholder
            }

            const avatarSrc = getAvatarOrPlaceholder(user);

            // Create a jQuery object for the selected tag HTML
            const $container = $(
                `<span class="d-flex align-items-center">
                    <img src="${avatarSrc}" class="rounded-circle me-2" alt="${user.text || 'User'} Avatar" height="50px" width="50px" />
                    <span>${user.text}</span>
                </span>`
            );
            return $container;
        }

        // --- Select2 Initialization ---
        $(document).ready(function() {
            $('#addProjectMembers').select2({
                placeholder: "Select project members", // Text shown when no items are selected
                allowClear: true, // Allows clearing all selections
                templateResult: formatUserResult,    // Function to format dropdown options
                templateSelection: formatUserSelection, // Function to format selected items
                // If you are fetching users via AJAX, you would add an 'ajax' configuration here:
                // ajax: {
                //     url: '/your-api-endpoint-for-users', // e.g., /api/users-for-select2
                //     dataType: 'json',
                //     delay: 250, // Delay in milliseconds before search performs
                //     data: function (params) {
                //         return {
                //             search: params.term, // search term
                //             page: params.page
                //         };
                //     },
                //     processResults: function (data, params) {
                //         params.page = params.page || 1;
                //         return {
                //             results: data.results, // Assume your API returns { results: [...] }
                //             pagination: {
                //                 more: (params.page * 20) < data.total_count // Adjust per your API's pagination
                //             }
                //         };
                //     },
                //     cache: true
                // }
            });
        });
</script>

   
@endsection
