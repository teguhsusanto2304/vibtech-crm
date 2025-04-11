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
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <i class="bx bx-search bx-md"></i>
                <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="Search..."
                    aria-label="Search..." />
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Place this tag where you want the button to render. -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="bx bx-user bx-md" style="color: #fff; font-size: 24px; margin-right: 25px;"></i>
                </a>
            </li>

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
                        <a class="dropdown-item" href="#"> <i class="bx bx-cog bx-md me-3"></i><span>Settings</span>
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
