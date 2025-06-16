@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />    

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
                <form action="{{ route('v1.project-management.store') }}" method="POST" class="p-4">
                    @csrf
                    <!-- Project Name -->
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <label for="projectName" class="form-label">Project Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter project name">
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
                    </div>

                    <!-- Project Description -->
                    <div class="mb-3">
                        <label for="projectDescription" class="form-label">Project Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control" placeholder="Enter description..."></textarea>
                    </div>
                    <style>

/* Style for individual selected tags (THE KEY PART FOR SELECTED OPTION HEIGHT) */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #e9ecef;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    
    /* --- CUSTOMIZE THESE PROPERTIES FOR HEIGHT --- */
    padding: 0.2rem 0.6rem; /* Adjust vertical padding (0.2rem) and horizontal padding (0.6rem) */
    /* You can also use fixed height, but padding is more flexible: */
    /* height: 30px; */ 
    
    margin-top: 0;
    margin-bottom: 0;
    margin-right: 0;
    color: #212529;
    display: flex;
    align-items: center; /* Ensures content (avatar, text, x) is vertically centered */
}

/* Style for the remove 'x' button on selected tags */
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #6c757d;
    margin-right: 0.3rem;
}

/* Style for the search input within the multiple select */
.select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
    border: none;
    outline: none;
    box-shadow: none;
    padding: 0;
    margin: 0;
    height: auto;
    min-width: 50px;
    flex-grow: 1;
}
/* Style for search results highlight */
.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
    background-color: #0d6efd;
    color: #fff;
}
/* Style for already selected options in the dropdown */
.select2-container--default .select2-results__option--selected {
    background-color: #f8f9fa;
    color: #495057;
}

                        </style>
                    <!-- Members and Manager -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="addProjectMembers" class="form-label">Add Project Members</label>
                            <select name="addProjectMembers[]" id="addProjectMembers" class="form-select" multiple="multiple">
                                @foreach($users as $user)
                        <option value="{{ $user->id }}"
                                data-avatar-url="{{ $user->avatar_url }}"
                                data-initials="{{ $user->name }}"
                                >{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project Manager</label>
                            <div class="d-flex align-items-center p-2 border rounded bg-white">
                                <img src="{{ asset(auth()->user()->avatar_url) }}" alt="Manager" class="rounded-circle me-2" width="40" height="40">
                                <span class="fw-semibold text-dark">You</span>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-12">
                            <a href="{{ route('v1.project-management')}}" class="btn btn-warning">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Project</button>
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
