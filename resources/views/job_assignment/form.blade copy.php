@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- custom-icon Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            @if($item=='Job Assignment Form')
                                <a href="{{ route('v1.job-assignment-form')}}">{{ $item }}</a>
                            @else
                                <a href="javascript:void(0);">{{ $item }}</a>
                            @endif
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <h3>{{ $title }}</h3>
            <style>
                                input:-webkit-autofill,
input:-webkit-autofill:focus,
input:-webkit-autofill:hover,
input:-webkit-autofill:active {
    background-color: rgb(241, 243, 244) !important;
    color: black !important;
    box-shadow: 0 0 0px 1000px rgb(248, 249, 249) inset !important; /* Forces color */
}

                .form-container {
                    background-color: #fff;
                    /* Dark blue background */
                    padding: 30px;
                    border-radius: 5px;
                }

                .form-control:focus {
                    background-color: white;
                    /* Change background color on focus */
                    color: #fff;
                    /* Change text color on focus */
                }

                .form-container h2 {
                    color: #fff;
                    margin-bottom: 20px;
                }

                .form-control-input {
                    color: #010101;
                    /* Text color */
                    background-color: #fff;
                    /* Background color */
                    border-color: #fff;
                    /* Border color */
                }

                .form-control-input:focus {
                    color: #010101;
                    /* Text color */
                    background-color: #fff;
                    /* Background color */
                    border-color: #fff;
                    /* Border color */
                }



                .form-check-input {
                    background-color: #fff;
                    /* Radio button background color */
                    border-color: #fff;
                    /* Radio button border color */
                }



                .form-select {
                    background-color: white;
                    /* Set background color to white */
                    color: #131313;
                    /* Set text color to match your background */
                    border: 1px solid #ccc;
                    /* Add a border for better contrast */
                }

                .form-label {
                    color: #fff;
                }
            </style>
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
            <div class="card" style="background-color: #004080">
                <div class="card-body">
                    <form class="row g-3" action="{{ route('v1.job-assignment-form.store')}}" method="post">
                        @csrf
                        <div class="col-md-3">
                            <label for="inputEmail4" class="form-label">Job Record ID</label>
                            <input type="text" class="form-control form-control-input" name="job_record_id" value="{{ $job_no }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="inputPassword4" class="form-label">Type of Job </label>
                            <input type="text" class="form-control form-control-input" name="job_type"  value="{{ old('job_type')}}">
                        </div>
                        <div class="col-md-2">
                            <label for="inputZip" class="form-label">Publish on dashboard calendar</label>
                            <br>
                            <input class="form-check-input" type="checkbox" value="1" name="job_status" />
                            <label class="form-label" for="defaultCheck1">
                                Yes
                            </label>
                        </div>
                        <div class="col-6">
                            <label for="inputAddress" class="form-label">Business Name</label>
                            <input type="text" class="form-control form-control-input" name="business_name" value="{{ old('business_name')}}" placeholder="enter business name">
                        </div>
                        <div class="col-6">
                            <label for="inputAddress2" class="form-label">Business Address</label>
                            <input type="text" class="form-control form-control-input" name="business_address" value="{{ old('business_address')}}"
                                placeholder="enter business addressr">
                        </div>
                        <div class="col-md-12">
                            <label for="inputCity" class="form-label">Scope of work</label>
                            <textarea class="form-control form-control-input" name="scope_of_work" cols="6" rows="3"
                                placeholder="enter scope of work">{{ old('scope_of_work')}}</textarea>
                        </div>
                        <div class="col-2">
                            <label for="inputAddress" class="form-label">Start Date</label>
                            <input type="date" class="form-control form-control-input" name="start_at">
                        </div>
                        <div class="col-2">
                            <label for="inputAddress2" class="form-label">End Date</label>
                            <input type="date" class="form-control form-control-input" name="end_at">
                        </div>
                        <div class="col-md-8">
                        </div>
                        <div class="col-md-6">
                            <label for="inputState" class="form-label">Sent To</label>
                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
                                rel="stylesheet" />
                            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                            <style>
                                .select2-container .select2-selection--single {
                                    height: 36px !important;
                                    /* Adjust height as needed */
                                    border: 1px solid #ccc !important;
                                    /* Add border for consistency */
                                    border-radius: 4px !important;
                                    /* Add border-radius for consistency */
                                }
                            </style>
                            <select class="form-select select2 " name="prsonnel_ids[]" id="personnel-multiple"
                                multiple="multiple">
                                @foreach ($users as $dept => $departmentUsers)
                                    <optgroup label="{{ $dept }}">
                                        @foreach ($departmentUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <script>
                                $(document).ready(function () {
                                    $('#personnel-multiple').select2();
                                });
                            </script>
                        </div>
                        <div class="col-md-6">
                            <label for="inputZip" class="form-label">Vehicle Require</label>
                            <br>
                            <input class="form-check-input" type="checkbox" value="1" name="is_vehicle_require" />
                            <label class="form-label" for="defaultCheck1">
                                Yes
                            </label>

                        </div>
                        <!-- Project Files Upload Field ADDED HERE -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload New Project Files (PDF, DOC/DOCX)</label>
                            <div id="new-file-upload-container">
                                {{-- Initial file input will be added by JavaScript or rendered if old input exists --}}
                                @if(old('project_files'))
                                    @foreach(old('project_files') as $index => $oldFile)
                                        <div class="input-group mb-2 file-upload-item">
                                            <input type="file" name="project_files[]" class="form-control" accept=".pdf,.doc,.docx">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-file-input"><i class="fas fa-trash"></i></button>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- This is the initial input if no old data --}}
                                    <div class="file-upload-item">
                                            <div class="input-group mb-2">
                                                <input type="file" name="project_files[]" class="form-control" accept=".pdf,.doc,.docx">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-file-input" style="display: none;"><i class="fas fa-trash"></i></button>
                                            </div>
                                            <input type="text" name="project_file_descriptions[]" class="form-control mt-1" placeholder="please enter file description">
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-1" id="add-more-files-btn"><i class="fas fa-plus"></i> Add Another File</button>
                            <small class="form-text text-muted d-block mt-2">Max file size: 3MB per file. Allowed types: PDF, DOC, DOCX.</small>
                        </div>
                        <script>
                            $(document).ready(function() {
                                // ... (Your existing JavaScript code for Select2, modals, tooltips, etc.) ...

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
                                    if(indexNewFileUpload<=5){
                                        const newFileInputHtml = `
                                            <div class="file-upload-item">
                                                <div class="input-group mb-2">
                                                    <input type="file" name="project_files[]" class="form-control" accept=".pdf,.doc,.docx">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-file-input"><i class="fas fa-trash"></i></button>
                                                </div>
                                                <input type="text" name="project_file_descriptions[]" class="form-control mt-1" placeholder="please enter file description">
                                            </div>
                                        `;
                                        $newFileUploadContainer.append(newFileInputHtml);
                                        updateRemoveButtonVisibility();
                                    } else {
                                        alert('you can not add more than 5 files !');
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
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-warning">Reset</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
