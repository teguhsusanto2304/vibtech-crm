<!-- Navbar -->
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="bx bx-menu bx-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <form id="global-search-form" class="d-flex w-100 me-3" role="search">
            <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                    <i class="bx bx-search bx-md"></i>
                    <style>
                        #global-search-input {
                            color: #FFFFFF; /* Sets the font color to white */
                        }
                    </style>
                    <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" 
                    placeholder="Search..."
                    id="global-search-input"
                    autocomplete="off"
                        aria-label="Search..." />
                </div>
            </div>
            <button type="submit" class="btn btn-primary d-none">Search</button>
        </form>
        <div id="global-search-suggestions" class="list-group position-absolute bg-white shadow-lg rounded" style="z-index: 1000; width: 300px; max-height: 400px; overflow-y: auto; display: none;">
            <!-- Suggestions will be appended here -->
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                const $globalSearchInput = $('#global-search-input');
                const $globalSearchForm = $('#global-search-form');
                const $globalSearchSuggestions = $('#global-search-suggestions');

                let searchTimeout;
                const MIN_CHARS_FOR_SEARCH = 3; // Minimum characters before triggering search

                // --- Autocomplete/AJAX Search (as user types) ---
                $globalSearchInput.on('keyup', function() {
                    clearTimeout(searchTimeout); // Clear previous timeout
                    const query = $(this).val().trim();

                    if (query.length >= MIN_CHARS_FOR_SEARCH) {
                        searchTimeout = setTimeout(function() {
                            performAjaxSearch(query);
                        }, 300); // Debounce: wait 300ms after last keypress
                    } else {
                        $globalSearchSuggestions.hide().empty(); // Hide and clear suggestions if query is too short
                    }
                });

                // Handle suggestion click (navigate to specific item)
                $globalSearchSuggestions.on('click', '.list-group-item', function(e) {
                    e.preventDefault();
                    const url = $(this).data('url');
                    if (url) {
                        window.location.href = url;
                        $globalSearchSuggestions.hide().empty(); // Hide suggestions after selection
                        $globalSearchInput.val(''); // Clear search input
                    }
                });

                // Hide suggestions when clicking outside
                $(document).on('click', function(e) {
                    if (!$globalSearchInput.is(e.target) && $globalSearchInput.has(e.target).length === 0 && !$globalSearchSuggestions.is(e.target) && $globalSearchSuggestions.has(e.target).length === 0) {
                        $globalSearchSuggestions.hide().empty();
                    }
                });

                // --- Function to perform AJAX search for autocomplete ---
                function performAjaxSearch(query) {
                    $.ajax({
                        url: "{{ route('v1.dashboard.global-search-autocomplete') }}", // Create this route
                        method: 'GET',
                        data: { query: query },
                        success: function(response) {
                            if (response.success && response.results) {
                                displaySearchResults(response.results);
                            } else {
                                $globalSearchSuggestions.hide().empty();
                            }
                        },
                        error: function(xhr) {
                            console.error('Global search AJAX error:', xhr.responseText);
                            $globalSearchSuggestions.hide().empty();
                        }
                    });
                }

                // --- Function to display search results in the suggestion dropdown ---
                function displaySearchResults(results) {
                    $globalSearchSuggestions.empty();
                    let hasResults = false;

                    // Iterate through categories (e.g., projects, tasks, users)
                    for (const category in results) {
                        if (results[category].length > 0) {
                            hasResults = true;
                            $globalSearchSuggestions.append(`<h6 class="dropdown-header px-3 py-2 bg-light text-primary fw-bold">${capitalizeFirstLetter(category)}</h6>`);
                            results[category].forEach(item => {
                                // Assuming each item has 'name' (or title) and 'url'
                                let name = item.name || item.title || item.email || 'Untitled'; // Fallback
                                let url = item.url || '#'; // Fallback

                                $globalSearchSuggestions.append(`
                                    <a href="${url}" data-url="${url}" class="list-group-item list-group-item-action py-2">
                                        <i class="${getIconForCategory(category)} me-2 text-muted"></i>
                                        ${name}
                                        ${item.meta_info ? `<br><small class="text-muted ms-4">${item.meta_info}</small>` : ''}
                                    </a>
                                `);
                            });
                        }
                    }

                    if (hasResults) {
                        $globalSearchSuggestions.css({
                            top: $globalSearchInput.outerHeight() + $globalSearchInput.position().top + 5, // Position below input
                            left: $globalSearchInput.position().left,
                            width: $globalSearchInput.outerWidth()
                        }).show();
                    } else {
                        $globalSearchSuggestions.html('<div class="list-group-item text-muted text-center py-3">No results found.</div>').show();
                        $globalSearchSuggestions.css({
                            top: $globalSearchInput.outerHeight() + $globalSearchInput.position().top + 5,
                            left: $globalSearchInput.position().left,
                            width: $globalSearchInput.outerWidth()
                        }).show();
                    }
                }

                // Helper function to capitalize first letter
                function capitalizeFirstLetter(string) {
                    return string.charAt(0).toUpperCase() + string.slice(1);
                }

                // Helper function to get icon based on category (customize as needed)
                function getIconForCategory(category) {
                    switch (category) {
                        case 'projects': return 'fas fa-folder';
                        case 'tasks': return 'fas fa-tasks';
                        case 'users': return 'fas fa-user';
                        case 'job_requisitions': return 'fas fa-file-contract';
                        default: return 'fas fa-info-circle';
                    }
                }

                // --- Full Page Search (when user hits Enter or explicitly submits the form) ---
                $globalSearchForm.on('submit', function(e) {
                    // Only prevent default if we want to handle the search entirely via JS
                    // If you want a dedicated search results page, remove e.preventDefault()
                    // and let the browser submit the form to the route defined by form action.
                    e.preventDefault();
                    const query = $globalSearchInput.val().trim();
                    if (query.length > 0) {
                        window.location.href = "{{ route('v1.dashboard.search-results') }}?query=" + encodeURIComponent(query);
                    }
                });

            });
            </script>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="bx bx-bell bx-md" style="color: #fff; font-size: 24px; margin-right: 25px;"></i>
                    <span class="badge bg-danger rounded-circle position-absolute" id="notification-count"
                        style="top: 5px; right: 30px; font-size: 12px;">0</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end" id="notification-list">

                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <!-- Notifications will be inserted here -->
                    <li class="text-center py-2">
                        <a href="#" id="mark-all-read" class="text-muted">Mark all as read</a>
                    </li>
                </ul>
            </li>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function () {
                    function fetchNotifications() {
                        $.ajax({
                            url: "{{ route('notifications.get') }}", // Define route in web.php
                            method: "GET",
                            success: function (data) {
                                let list = $("#notification-list");
                                list.find("li:not(:last)").remove(); // Clear existing notifications

                                if (data.length > 0) {
                                    $("#notification-count").text(data.length).show();
                                    data.forEach(function (notif) {
                                        let color = notif.data.type === "success" ? "green" :
                                            notif.data.type === "error" ? "red" :
                                                notif.data.type === "decline" ? "red" :
                                                    notif.data.type === "accept" ? "blue" :
                                                        "orange";
                                        let link = notif.data.url ? notif.data.url : "#";
                                        list.prepend(`
                                    <li>
                                        <div class="flex-grow-1 dropdown-item">
                                            <h6 class="small mb-0">Job Requisition Form</h6>
                                        <a class="" href="${link}?notif=${notif.id}">
                                            <small style="color: ${color};">${notif.data.message} [${notif.data.time}]</small>
                                        </a>
                                        </div>
                                    </li>
                                `);
                                    });
                                    list.prepend(`<li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h6 class="mb-0 me-auto">Notification</h6>
              <div class="d-flex align-items-center h6 mb-0">
                <span class="badge bg-label-primary me-2" >`+ data.length + ` New</span>
                <a href="javascript:void(0)" class="dropdown-notifications-all p-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i class="icon-base bx bx-envelope-open text-heading"></i></a>
              </div>
            </div>
          </li>`);
                                } else {
                                    $("#notification-count").hide();
                                    list.prepend(`<li class="text-center py-2"><small>No new notifications</small></li>`);
                                    list.prepend(`<li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h6 class="mb-0 me-auto">Notification</h6>
              <div class="d-flex align-items-center h6 mb-0">
                <span class="badge bg-label-primary me-2" >`+ data.length + ` New</span>
                <a href="javascript:void(0)" class="dropdown-notifications-all p-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i class="icon-base bx bx-envelope-open text-heading"></i></a>
              </div>
            </div>
          </li>`);
                                }
                            }
                        });
                    }

                    fetchNotifications(); // Fetch notifications on page load

                    $("#mark-all-read").click(function () {
                        $.ajax({
                            url: "{{ route('notifications.read') }}",
                            method: "POST",
                            data: { _token: "{{ csrf_token() }}" },
                            success: function () {
                                fetchNotifications();
                            }
                        });
                    });
                });
            </script>


            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ asset(auth()->user()->path_image) }}" alt
                            class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('assets/img/photos/' . auth()->user()->path_image) }}" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                    <small class="text-muted">{{ auth()->user()->position }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile') }}">
                            <i class="bx bx-user bx-md me-3"></i><span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-power-off bx-md me-3"></i><span>Log Out</span>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>

<!-- / Navbar -->
