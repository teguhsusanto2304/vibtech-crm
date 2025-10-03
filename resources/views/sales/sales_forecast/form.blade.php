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
                <form action="{{ route('v1.sales-forecast.store') }}" method="POST" class="p-4" enctype="multipart/form-data">
                    @csrf
                    <!-- Project Name -->
                    <div class="row mb-3">
                        <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label mb-3">Individually Type:</label>
                            {{-- Global Select All Checkbox --}}
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="selectAllIndividually">
                                <label class="form-check-label fw-bold" for="selectAllIndividually">
                                    Select All Individually
                                </label>
                            </div>

                            <div class="user-selection-container mb-3" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.25rem; padding: 10px;">
                                @foreach ($individuallies as $individually)
                                    <div class="mb-3">
                                        
                                        <div class="ms-4"> {{-- Indent department users --}}
                                        
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input indivudually-checkbox" type="checkbox"
                                                        name="individually_ids[]"
                                                        value="{{ $individually->id }}"
                                                        id="user_{{ $individually->name }}">
                                                    <label class="form-check-label" for="individually_{{ $individually->id }}">                                                        
                                                        {{ $individually->name }}
                                                    </label>
                                                </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        <script>
                            $(document).ready(function() {
                                const selectAllIndividuallyCheckbox = $('#selectAllIndividually');
                                const indivuduallyCheckboxes = $('.indivudually-checkbox');

                                // 1. Global "Select All Users" functionality
                                selectAllIndividuallyCheckbox.on('change', function() {
                                    const isChecked = $(this).is(':checked');
                                    indivuduallyCheckboxes.prop('checked', isChecked);
                                     // Also check/uncheck department checkboxes
                                });

                                // 2. Department-level "Select All" functionality


                                // 3. Individual User Checkbox change listener
                                indivuduallyCheckboxes.on('change', function() {
                                    
                                    // Update global "Select All Users" checkbox
                                    updateSelectAllIndividuallyCheckbox();
                                });

                                // Helper function to update the global "Select All Users" checkbox
                                function updateSelectAllIndividuallyCheckbox() {
                                    if (indivuduallyCheckboxes.length === indivuduallyCheckboxes.filter(':checked').length) {
                                        selectAllIndividuallyCheckbox.prop('checked', true);
                                    } else {
                                        selectAllIndividuallyCheckbox.prop('checked', false);
                                    }
                                }

                                // Initial check on page load (e.g., if some users are pre-selected)
                                updateSelectAllIndividuallyCheckbox();
                                
                            });
                        </script>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sent To</label>
                            {{-- Global Select All Checkbox --}}
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                <label class="form-check-label fw-bold" for="selectAllUsers">
                                    Select All Users
                                </label>
                            </div>

                            <div class="user-selection-container mb-3" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.25rem; padding: 10px;">
                                @foreach (\App\Models\User::where('user_status',1)->get() as $user)
                                    <div class="mb-3">
                                        
                                        <div class="ms-4"> {{-- Indent department users --}}
                                        
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input user-checkbox" type="checkbox"
                                                        name="personnel_ids[]"
                                                        value="{{ $user->id }}"
                                                        id="user_{{ $user->id }}">
                                                    <label class="form-check-label" for="user_{{ $user->id }}">
                                                        {{-- Optional: Display avatar/initials --}}
                                                        @if($user->avatar_url)
                                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle me-1" width="20" height="20">
                                                        @else
                                                            <span class="avatar-initials rounded-circle bg-secondary text-white me-1" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7em;">{{ Str::limit($user->name, 1, '') }}</span>
                                                        @endif
                                                        {{ $user->name }}
                                                    </label>
                                                </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        <script>
                            $(document).ready(function() {
                                const selectAllUsersCheckbox = $('#selectAllUsers');
                                const departmentSelectAllCheckboxes = $('.select-all-department');
                                const userCheckboxes = $('.user-checkbox');

                                // 1. Global "Select All Users" functionality
                                selectAllUsersCheckbox.on('change', function() {
                                    const isChecked = $(this).is(':checked');
                                    userCheckboxes.prop('checked', isChecked);
                                    departmentSelectAllCheckboxes.prop('checked', isChecked); // Also check/uncheck department checkboxes
                                });

                                // 2. Department-level "Select All" functionality
                                departmentSelectAllCheckboxes.on('change', function() {
                                    const isChecked = $(this).is(':checked');
                                    const departmentSlug = $(this).data('department');
                                    // Select/deselect only users belonging to this department
                                    userCheckboxes.filter(`[data-department="${departmentSlug}"]`).prop('checked', isChecked);

                                    // Update global "Select All Users" checkbox based on department selections
                                    updateSelectAllUsersCheckbox();
                                });

                                // 3. Individual User Checkbox change listener
                                userCheckboxes.on('change', function() {
                                    const departmentSlug = $(this).data('department');
                                    const departmentUsers = userCheckboxes.filter(`[data-department="${departmentSlug}"]`);
                                    const checkedDepartmentUsers = departmentUsers.filter(':checked');

                                    // Update department "Select All" checkbox
                                    if (checkedDepartmentUsers.length === departmentUsers.length) {
                                        $(`#selectAllDept_${departmentSlug}`).prop('checked', true);
                                    } else {
                                        $(`#selectAllDept_${departmentSlug}`).prop('checked', false);
                                    }

                                    // Update global "Select All Users" checkbox
                                    updateSelectAllUsersCheckbox();
                                });

                                // Helper function to update the global "Select All Users" checkbox
                                function updateSelectAllUsersCheckbox() {
                                    if (userCheckboxes.length === userCheckboxes.filter(':checked').length) {
                                        selectAllUsersCheckbox.prop('checked', true);
                                    } else {
                                        selectAllUsersCheckbox.prop('checked', false);
                                    }
                                }

                                // Initial check on page load (e.g., if some users are pre-selected)
                                updateSelectAllUsersCheckbox();
                                departmentSelectAllCheckboxes.each(function() {
                                    const departmentSlug = $(this).data('department');
                                    const departmentUsers = userCheckboxes.filter(`[data-department="${departmentSlug}"]`);
                                    const checkedDepartmentUsers = departmentUsers.filter(':checked');
                                    if (checkedDepartmentUsers.length === departmentUsers.length && departmentUsers.length > 0) {
                                        $(this).prop('checked', true);
                                    }
                                });
                            });
                        </script>
                        </div>

                    </div>

                    <!-- Project Start Date & End Date -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="projectStartDate" class="form-label">Forecast Year</label>
                            <input type="number" name="year" id="year" class="form-control" min="{{ date('Y') }}" max="{{ date('Y')+5 }}" value="{{ date('Y') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="projectStartDate" class="form-label">Currency</label>
                            <select name="currency" id="currency" class="form-control" >
                                <option value="SGD">Singapore Dollar</option>
                                <option value="MYR">Malaysia Ringgit</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="projectStartDate" class="form-label">Company</label>
                            <input type="text" name="company" id="company" class="form-control" >
                        </div>                        
                        
                    </div>
                    

                    <!-- Project Description -->
                    
                    
                    <!-- Members and Manager -->
                    
                    <!-- Buttons -->
                    <div class="col-12">
                            <a href="
                            {{ route('v1.sales-forecast') }}
                            " class="btn btn-warning">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Forecast</button>
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
