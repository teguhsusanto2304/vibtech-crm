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
                <div class="space-y-4">
            <p class="text-lg text-gray-700"><span class="font-semibold">Type:</span> {{ $claimTypeName }}</p>
            <p class="text-lg text-gray-700"><span class="font-semibold">Forecast Year:</span> {{ $forecastYear }}</p>
            
            <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                <p class="text-xl font-bold text-green-800">Forecasted Sales Value:</p>
                <p class="text-4xl font-extrabold text-green-600 mt-1">
                    $SGD {{ $forecastedSales }}
                </p>
            </div>

            <div class="pt-4 text-sm text-gray-500 italic border-t mt-4">
                <p class="font-semibold text-gray-600">Calculation Note:</p>
                <p>{{ $calculationDetails }}</p>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('v1.sales-forecast.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 shadow-md">
                Go Back
            </a>
        </div>
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
