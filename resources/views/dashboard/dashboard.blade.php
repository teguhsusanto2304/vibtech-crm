@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <link rel="stylesheet" href="../assets/vendor/libs/fullcalendar/fullcalendar.css" />

    <link rel="stylesheet" href="../assets/vendor/css/pages/app-calendar.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        .callout {
            padding: 15px;
            border-left: 5px solid #80e491;
            background-color: #e7edfd;
            margin-bottom: 0px;
            border-radius: 4px;
        }

        .callout-event {
            padding: 15px;
            border-left: 5px solid #80e491;
            background-color: #defbe3;
            margin-bottom: 0px;
            border-radius: 4px;
        }

        .callout h5 {
            margin-top: 0;
            font-weight: bold;
        }
        .fc-event {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- custom-icon Breadcrumb-->
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
        <div id="notification-alerts-container" class="mt-3 mx-auto" >
            {{-- Alerts will be injected here by JavaScript --}}
        </div>
        <script>
        $(document).ready(function () {
    const notificationAlertsContainer = $("#notification-alerts-container");

function fetchNotifications() {
    $.ajax({
        url: "{{ route('notifications.get-specifically') }}",
        method: "GET",
        success: function (data) {
            notificationAlertsContainer.empty();

            if (data.length > 0) {
                $("#notification-count").text(data.length).show();

                let username = @json(auth()->user()->name);

                // Group notifications by category
                let grouped = {
                    "employee-handbooks": [],
                    "management-memo": [],
                    "general": []
                };

                data.forEach(function (notif) {
                    let link = notif.data.url ? notif.data.url : "#";
                    let notifUrl = notif.data.url?.toLowerCase() || "";

                    if (notifUrl.includes('/employee-handbooks')) {
                        grouped["employee-handbooks"].push(
                            `${username}, a new employee handbook was uploaded. 
                             <a href="${link}?notif=${notif.id}" class="alert-link">Read here</a>
                             <br><small class="text-muted">(${notif.data.time})</small>`
                        );
                    } else if (notifUrl.includes('/management-memo')) {
                        grouped["management-memo"].push(
                            `${username}, a new management memo was issued. 
                             <a href="${link}?notif=${notif.id}" class="alert-link">Read here</a>
                             <br><small class="text-muted">(${notif.data.time})</small>`
                        );
                    } else {
                        grouped["general"].push(
                            `${username}, you have a new notification.
                             <br><small class="text-muted">(${notif.data.time})</small>`
                        );
                    }
                });

                // Accordion wrapper
                let accordionHtml = `<div class="accordion" id="notificationsAccordion">`;

                // Employee Handbooks
                if (grouped["employee-handbooks"].length > 0) {
                    accordionHtml += `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-handbook">
                                <button class="accordion-button collapsed bg-success text-white" 
                                    type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse-handbook" 
                                    aria-expanded="false" aria-controls="collapse-handbook">
                                    <i class="fas fa-book me-2"></i> Employee Handbooks
                                </button>
                            </h2>
                            <div id="collapse-handbook" class="accordion-collapse collapse" 
                                aria-labelledby="heading-handbook" data-bs-parent="#notificationsAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        ${grouped["employee-handbooks"].map(item => `<li class="list-group-item">${item}</li>`).join("")}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Management Memo
                if (grouped["management-memo"].length > 0) {
                    accordionHtml += `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-memo">
                                <button class="accordion-button collapsed bg-warning text-dark" 
                                    type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse-memo" 
                                    aria-expanded="false" aria-controls="collapse-memo">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Management Memo
                                </button>
                            </h2>
                            <div id="collapse-memo" class="accordion-collapse collapse" 
                                aria-labelledby="heading-memo" data-bs-parent="#notificationsAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        ${grouped["management-memo"].map(item => `<li class="list-group-item">${item}</li>`).join("")}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // General
                if (grouped["general"].length > 0) {
                    accordionHtml += `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-general">
                                <button class="accordion-button collapsed bg-info text-white" 
                                    type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse-general" 
                                    aria-expanded="false" aria-controls="collapse-general">
                                    <i class="fas fa-bell me-2"></i> General Notifications
                                </button>
                            </h2>
                            <div id="collapse-general" class="accordion-collapse collapse" 
                                aria-labelledby="heading-general" data-bs-parent="#notificationsAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        ${grouped["general"].map(item => `<li class="list-group-item">${item}</li>`).join("")}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;
                }

                accordionHtml += `</div>`; // close accordion
                notificationAlertsContainer.append(accordionHtml);

            } else {
                $("#notification-count").hide();
                notificationAlertsContainer.html(`
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i> No new notifications at this time.
                    </div>
                `);
            }
        },
        error: function (xhr) {
            console.error("Error fetching notifications:", xhr.responseText);
            notificationAlertsContainer.empty().html(`
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> Failed to load notifications.
                </div>
            `);
            $("#notification-count").hide();
        }
    });
}


    function fetchNotificationsOld() {
        $.ajax({
            url: "{{ route('notifications.get-specifically') }}", // Define route in web.php
            method: "GET",
            success: function (data) {
                // Clear existing alerts before adding new ones
                notificationAlertsContainer.empty();

                if (data.length > 0) {
                    $("#notification-count").text(data.length).show(); // Update count in your header/dropdown

                    let username = @json(auth()->user()->name); // Blade-safe variable

                    data.forEach(function (notif) {
                        let alertClass = '';
                        let iconClass = '';
                        let alertMessage = '';
                        let link = notif.data.url ? notif.data.url : "#";

                        // Get lowercase URL safely
                        let notifUrl = notif.data.url?.toLowerCase() || '';

                        // Handle specific messages by URL
                        if (notifUrl.includes('/employee-handbooks')) {
                            alertClass = "alert-success";
                            iconClass = "fas fa-check-circle";
                            alertMessage = `${username}, there is a new employee handbook uploaded, please click <a href="${link}?notif=${notif.id}" class="alert-link ms-1">here</a> to read them.`;
                        } else if (notifUrl.includes('/management-memo')) {
                            alertClass = "alert-warning";
                            iconClass = "fas fa-exclamation-triangle";
                            alertMessage = `${username}, there is a new management memo issued, please click <a href="${link}?notif=${notif.id}" class="alert-link ms-1">here</a> to read them.`;
                        } else {
                            alertMessage = `${username}, you have a new notification.`;
                        }

                        

                        // Build HTML
                        let alertHtml = `
                            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                <i class="${iconClass} me-2"></i>
                                ${alertMessage}
                                <small class="text-muted ms-3">(${notif.data.time})</small>
                            </div>
                        `;

                        notificationAlertsContainer.append(alertHtml);
                    });


                    // Optional: If you still have a notification dropdown, you might want to update it separately
                    // For example, if you want a "New Notifications" header in the dropdown:
                    let list = $("#notification-list"); // Your dropdown list
                    list.find("li:not(:last)").remove(); // Clear existing notifications in dropdown
                    list.prepend(`<li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h6 class="mb-0 me-auto">Notification</h6>
                            <div class="d-flex align-items-center h6 mb-0">
                                <span class="badge bg-label-primary me-2" >`+ data.length + ` New</span>
                                <a href="javascript:void(0)" class="dropdown-notifications-all p-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i class="icon-base bx bx-envelope-open text-heading"></i></a>
                            </div>
                        </div>
                    </li>`);
                    // And then you can append the same alerts to the dropdown if you want them in both places
                    // Or just keep the dropdown for a summary.
                } else {
                    $("#notification-count").hide();
                    // Display a "No new notifications" message in the alert container if desired
                    notificationAlertsContainer.html(`
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i> No new notifications at this time.
                        </div>
                    `);
                    // And for your dropdown list if it still exists:
                    let list = $("#notification-list");
                    list.find("li:not(:last)").remove();
                    list.prepend(`<li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h6 class="mb-0 me-auto">Notification</h6>
                            <div class="d-flex align-items-center h6 mb-0">
                                <span class="badge bg-label-primary me-2" >0 New</span>
                                <a href="javascript:void(0)" class="dropdown-notifications-all p-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i class="icon-base bx bx-envelope-open text-heading"></i></a>
                            </div>
                        </div>
                    </li>`);
                    list.append(`<li class="text-center py-2"><small>No new notifications</small></li>`);
                }
            },
            error: function(xhr) {
                console.error("Error fetching notifications:", xhr.responseText);
                notificationAlertsContainer.empty().html(`
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> Failed to load notifications.
                    </div>
                `);
                $("#notification-count").hide();
            }
        });
    }

    fetchNotifications(); // Fetch notifications on page load

    // You might want to automatically refresh notifications periodically
    // setInterval(fetchNotifications, 60000); // Fetch every 60 seconds

    // Assuming you have a "Mark all as read" button/link with this ID/class
    $(document).on('click', '.dropdown-notifications-all', function () { // Use event delegation if element is dynamic
        $.ajax({
            url: "{{ route('notifications.read') }}",
            method: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function () {
                fetchNotifications(); // Re-fetch to update display
                // Optionally hide the dropdown menu if it's open
                // $('.dropdown-toggle').dropdown('hide');
            },
            error: function(xhr) {
                console.error("Error marking notifications as read:", xhr.responseText);
                // Display error in a temporary alert or console
            }
        });
    });

    // Optional: Function to mark a single notification as read (e.g., when clicked)
    // You'd need to add a click listener to the alert itself or its "View Details" link
    function markNotificationAsRead(notificationId) {
        $.ajax({
            url: `/notifications/${notificationId}/read`, // Example route
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function() {
                // No need to re-fetch all, just remove the specific alert if desired
                $(`#notification-alert-${notificationId}`).remove(); // If you give alerts unique IDs
            },
            error: function(xhr) {
                console.error(`Error marking notification ${notificationId} as read:`, xhr.responseText);
            }
        });
    }
});
</script>

        <!-- Vehicle Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bookingModalLabel">Vehicle Booking Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Vehicle Image -->

                            <!-- Booking Details -->
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-5"><strong>Vehicle</strong></div>
                                    <div class="col-md-1">:</div>
                                    <div class="col-md-6"><span id="bookingVehicle"></span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><strong>Start Date</strong></div>
                                    <div class="col-md-1">:</div>
                                    <div class="col-md-6"><span id="bookingStart"></span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><strong>End Date</strong></div>
                                    <div class="col-md-1">:</div>
                                    <div class="col-md-6"><span id="bookingEnd"></span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><strong>Purposes</strong></div>
                                    <div class="col-md-1">:</div>
                                    <div class="col-md-6"><span id="bookingPurpose"></span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><strong>Job Assignment</strong></div>
                                    <div class="col-md-1">:</div>
                                    <div class="col-md-6"><span id="bookingJob"></span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5"><strong>Created By</strong></div>
                                    <div class="col-md-1">:</div>
                                    <div class="col-md-6"><span id="bookingCreator"></span></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <img id="bookingImage" src="" alt="Vehicle Image" class="img-fluid rounded"
                                    style="max-width: 300px;">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center">
            <div class="me-2 bg-primary" style="width: 20px; height: 20px;"></div>
            <div><small>Vehicle Booking</small></div>
        </div>

        <div class="d-flex align-items-center mt-2 mb-2">
            <div class="me-2 bg-success" style="width: 20px; height: 20px;"></div>
            <div><small>Job Requisition Form</small></div>
        </div>
        <div class="card app-calendar-wrapper ">
            <div class="row g-0">
                <!-- Calendar & Modal -->
                <div class="col app-calendar-content">
                    <div class="card shadow-none border-0">
                        <div class="card-body pb-0">
                            <!-- FullCalendar -->
                            <div id="calendar"></div>
                        </div>
                    </div>
                    <div class="app-overlay"></div>
                    <!-- FullCalendar Offcanvas -->
                    <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar"
                        aria-labelledby="addEventSidebarLabel">
                        <div class="offcanvas-header border-bottom">
                            <h5 class="offcanvas-title" id="addEventSidebarLabel">Detail</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body" >
                            <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                                <div class="mb-6 form-control-validation">
                                    <label class="form-label" for="eventTitle">Date Selected</label>
                                    <input type="text" class="form-control" id="eventDate1" name="eventDate" readonly />
                                </div>
                                <h4>Activities</h4>
                                <div id="eventList1"></div>

                            </form>
                        </div>
                    </div>
                </div>
                <!-- Calendar Sidebar -->
                <div class="col app-calendar-sidebar border-end " style="display: none;" id="app-calendar-sidebar">
                    <div class="border-bottom p-6 my-sm-0 mb-4">
                        <p>Date Selected :<strong id="eventDate"></strong></p>
                    </div>
                    <div class="px-6 pb-2">
                        <!-- Filter -->
                        <div>
                            <h5>Activities</h5>
                            <div id="eventList"></div>
                        </div>

                        <div class="form-check form-check-secondary mb-5 ms-2" style="display: none;">
                            <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all"
                                checked />
                            <label class="form-check-label" for="selectAll">View All</label>
                        </div>

                        <div class="app-calendar-events-filter text-heading" style="display: none;">
                            <div class="form-check form-check-danger mb-5 ms-2">
                                <input class="form-check-input input-filter" type="checkbox" id="select-personal"
                                    data-value="personal" checked />
                                <label class="form-check-label" for="select-personal">Personal</label>
                            </div>
                            <div class="form-check mb-5 ms-2">
                                <input class="form-check-input input-filter" type="checkbox" id="select-business"
                                    data-value="business" checked />
                                <label class="form-check-label" for="select-business">Business</label>
                            </div>
                            <div class="form-check form-check-warning mb-5 ms-2">
                                <input class="form-check-input input-filter" type="checkbox" id="select-family"
                                    data-value="family" checked />
                                <label class="form-check-label" for="select-family">Family</label>
                            </div>
                            <div class="form-check form-check-success mb-5 ms-2">
                                <input class="form-check-input input-filter" type="checkbox" id="select-holiday"
                                    data-value="holiday" checked />
                                <label class="form-check-label" for="select-holiday">Holiday</label>
                            </div>
                            <div class="form-check form-check-info ms-2">
                                <input class="form-check-input input-filter" type="checkbox" id="select-etc"
                                    data-value="etc" checked />
                                <label class="form-check-label" for="select-etc">ETC</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Calendar Sidebar -->
                <style>
                    .bg-label-success {
                        height: 30px;
                    }
                    .bg-label-primary {
                        height: 30px;
                    }
                    .event-dot::before {
                        content: "●";
                        /* Unicode dot character */
                        margin-right: 5px;
                        font-size: 12px;
                    }

                    .dot-Holiday::before {
                        color: blue;
                    }

                    /* Example for event category 'Holiday' */
                    .dot-Business::before {
                        color: green;
                    }

                    /* Example for event category 'Meeting' */
                    .dot-Workshop::before {
                        color: green;
                    }

                    /* Example for event category 'Workshop' */

                    .fc-h-event {
                        background-color: transparent;
                    }

                    .fc .fc-more-popover .fc-popover-body {
                        min-width: 220px;
                        padding: 10px;
                        background: white;
                    }

                    .fc .fc-day-today:not(.fc-col-header-cell) .fc-popover-body {
                        background-color: white;
                    }
                </style>
                <script src="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>
                <script>
                    let eventsUrl = "{{ route('v1.dashboard.events') }}";
                    window.events = @json($events);
                </script>
                <script>
                    let eventDetailsModalInstance;
                    
                    document.addEventListener("DOMContentLoaded", function () {
                        var isRtl = false;
                        let k = isRtl ? "rtl" : "ltr";
                        {
                            var w = document.getElementById("calendar");
                            let t = document.querySelector(".app-calendar-sidebar");
                            var x = document.getElementById("addEventSidebar");
                            let n = document.querySelector(".app-overlay"),
                                a = document.querySelector(".offcanvas-title");
                            var T = document.querySelector(".btn-toggle-sidebar");
                            let l = document.getElementById("addEventBtn"),
                                i = document.querySelector(".btn-delete-event"),
                                r = document.querySelector(".btn-cancel"),
                                d = document.getElementById("eventTitle"),
                                o = document.getElementById("eventStartDate"),
                                s = document.getElementById("eventEndDate"),
                                c = document.getElementById("eventURL"),
                                u = document.getElementById("eventLocation"),
                                v = document.getElementById("eventDescription"),
                                m = document.querySelector(".allDay-switch"),
                                p = document.querySelector(".select-all");
                            var D,
                                P,
                                M = Array.from(document.querySelectorAll(".input-filter")),
                                A = document.querySelector(".inline-calendar");
                            let g = {
                                Business: "primary",
                                Holiday: "success",
                                Personal: "danger",
                                Family: "warning",
                                ETC: "info",
                            },
                                f = $("#eventLabel"),
                                h = $("#eventGuests"),
                                b = events,
                                y = !1,
                                E = null,
                                e = null,
                                L = new bootstrap.Offcanvas(x);
                            function q(e) {
                                return e.id
                                    ? "<span class='badge badge-dot bg-" +
                                    $(e.element).data("label") +
                                    " me-2'></span>" +
                                    e.text
                                    : e.text;
                            }
                            function B(e) {
                                return e.id
                                    ? `
                <div class='d-flex flex-wrap align-items-center'>
                  <div class='avatar avatar-xs me-2'>
                    <img src='${assetsPath}img/avatars/${$(e.element).data("avatar")}'
                      alt='avatar' class='rounded-circle' />
                  </div>
                  ${e.text}
                </div>`
                                    : e.text;
                            }
                            function I() {
                                var e = document.querySelector(".fc-sidebarToggle-button");
                                for (
                                    e.classList.remove("fc-button-primary"),
                                    e.classList.add("d-lg-none", "d-inline-block", "ps-0");
                                    e.firstChild;

                                )
                                    e.firstChild.remove();
                                e.setAttribute("data-bs-toggle", "sidebar"),
                                    e.setAttribute("data-overlay", ""),
                                    e.setAttribute("data-target", "#app-calendar-sidebar"),
                                    e.insertAdjacentHTML(
                                        "beforeend",
                                        '<i class="icon-base bx bx-menu icon-lg text-heading"></i>'
                                    );
                            }
                            let S = new Calendar(w, {
                                initialView: "dayGridMonth",
                                eventContent: function (arg) {

                                    let dot = document.createElement('span');
                                     if (arg.event.extendedProps.event_status == 'JR') {
                                        dot.style.backgroundColor = '#75E166';
                                    } else {
                                        dot.style.backgroundColor = ' #8A89E1';
                                    }

                                    dot.style.width = '10px';
                                    dot.style.height = '10px';
                                    dot.style.borderRadius = '50%';
                                    dot.style.display = 'inline-block';
                                    dot.style.marginRight = '5px';
                                    let title = document.createElement('span');
                                    title.innerText = arg.event.title;

                                    let customButton = document.createElement('button');
                                    customButton.innerText = arg.event.title;
                                    customButton.style.background = 'transparent';
                                    customButton.style.color = 'black';
                                    customButton.style.border = 'none';
                                    customButton.style.padding = '5px 10px';
                                    customButton.style.cursor = 'pointer';

                                    // Handle click event
                                    customButton.onclick = function (event) {
                                        event.stopPropagation(); // Prevent FullCalendar from navigating
                                        let eventStatus = arg.event.extendedProps.event_status; // ✅ Fetch event_status

                                        if (eventStatus === "JR") {
                                            let eventId = arg.event.id; // Get event ID
                                            let url = `/v1/job-assignment-form/view/${eventId}/yes?fr=main`; // Replace with your target URL

                                            // Redirect to the new page
                                            window.location.href = url;
                                        } else {

                                            let eventId = arg.event.id;
                                            let modal = document.getElementById('bookingModal');
                                            let modalTitle = modal.querySelector('.modal-title');
                                            let modalBody = modal.querySelector('.modal-body');

                                            // Update modal title with the event ID
                                            modalTitle.innerText = `Vehicle Booking Details`;

                                            // Show loading text while fetching data
                                            modalBody.innerHTML = `<p>Loading details...</p>`;

                                            // Show the modal
                                            var myModal = new bootstrap.Modal(modal);
                                            myModal.show();

                                            // Fetch event details from API
                                            fetch(`/v1/vehicle-bookings/${eventId}/modal`) // Replace with your API URL
                                                .then(response => response.json())
                                                .then(data => {
                                                    // Populate modal with fetched data
                                                    modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5"><strong>Vehicle</strong></div>
                                <div class="col-md-1">:</div>
                                <div class="col-md-6"><span id="bookingVehicle">${data.vehicle.name}</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-5"><strong>Start Date</strong></div>
                                <div class="col-md-1">:</div>
                                <div class="col-md-6"><span id="bookingStart">${data.start_at}</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-5"><strong>End Date</strong></div>
                                <div class="col-md-1">:</div>
                                <div class="col-md-6"><span id="bookingEnd">${data.end_at}</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-5"><strong>Purposes</strong></div>
                                <div class="col-md-1">:</div>
                                <div class="col-md-6"><span id="bookingPurpose">${data.purposes}</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-5"><strong>Job Assignment</strong></div>
                                <div class="col-md-1">:</div>
                                <div class="col-md-6"><span id="bookingJob">${data.job_assignment ? data.job_assignment.scope_of_work : "N/A"}</span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-5"><strong>Created By</strong></div>
                                <div class="col-md-1">:</div>
                                <div class="col-md-6"><span id="bookingCreator">${data.creator.name}</span></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <img id="bookingImage" src="/${data.vehicle.path_image}" alt="Vehicle Image" class="img-fluid rounded"
                                style="max-width: 300px;">
                        </div>
                    </div>
                `;
                                                })
                                                .catch(error => {
                                                    modalBody.innerHTML = `<p style="color: red;">Error fetching details. Please try again.</p>`;
                                                    console.error("Error fetching event data:", error);
                                                });
                                        }

                                    };

                                    return { domNodes: [dot, customButton] };
                                },


                                events: function (e, t) {
                                    let n = (() => {
                                        let t = [],
                                            e = [].slice.call(
                                                document.querySelectorAll(".input-filter:checked")
                                            );
                                        return (
                                            e.forEach((e) => {
                                                t.push(e.getAttribute("data-value"));
                                            }),
                                            t
                                        );
                                    })();
                                    t(
                                        b.filter(function (e) {
                                            return n.includes(
                                                e.extendedProps.calendar.toLowerCase()
                                            );
                                        })
                                    );
                                },
                                plugins: [
                                    dayGridPlugin,
                                    interactionPlugin,
                                    listPlugin,
                                    timegridPlugin,
                                ],
                                editable: !0,
                                dragScroll: !0,
                                dayMaxEvents: 2,
                                eventResizableFromStart: !0,
                                customButtons: { sidebarToggle: { text: "Sidebar" } },
                                headerToolbar: {
                                    start: "sidebarToggle, prev,next, title",
                                    end: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
                                },
                                direction: k,
                                initialDate: new Date(),
                                navLinks: !0,
                                eventClassNames: function ({ event: e }) {
                                    return ["bg-label-" + g[e._def.extendedProps.calendar]];
                                    //return ["event-dot", "dot-" + e._def.extendedProps.calendar];
                                },
                                datesSet: function () {
                                    I();
                                    //highlightCurrentDate();
                                },
                                viewDidMount: function () {
                                    I();
                                    highlightCurrentDate();
                                },
                            });

                            S.render()

                        }

                        function highlightCurrentDate() {
                            eventDetailsModalInstance = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
                            const today = new Date();
                            const todayFormatted = today.toISOString().slice(0, 10); // Format as YYYY-MM-DD

                            const dayCells = document.querySelectorAll(".fc-daygrid-day"); // Select all day cells

                            dayCells.forEach((cell) => {

                                const cellDate = cell.getAttribute("data-date"); // Get the date of the cell

                                if (cellDate === todayFormatted) {
                                    cell.style.backgroundColor = "lightgray"; // Set background color
                                    cell.style.cursor = "pointer"; // Make it look clickable

                                    // Add a click event listener to show the alert
                                    cell.addEventListener("click", function () {
                                        //alert("Today's date: " + todayFormatted);
                                        //document.getElementById("eventAt").textContent = cellDate;
                                        fetchEvents(cellDate);
                                        document.getElementById("eventDate").textContent = cellDate;
                                    });
                                } else {
                                    cell.style.backgroundColor = ""; // Reset background color for other days
                                    cell.style.cursor = "pointer"; // Reset cursor
                                    // Remove any previous click listeners to avoid multiple alerts
                                    //cell.removeEventListener('click', arguments.callee);

                                    cell.addEventListener("click", function () {
                                        fetchEvents(cellDate);
                                        document.getElementById("eventDate").textContent = cellDate;
                                    });
                                }
                            });
                        }
                    });

                    function fetchEvents(eventAt) {
                        const $eventsLoadingSpinner = $('#events-loading-spinner');
                        const $eventsDisplayArea = $('#events-display-area');
                        const $noEventsMessage = $('#no-events-message');

                        // Reset modal content
                        $eventsDisplayArea.empty().addClass('d-none');
                        $noEventsMessage.addClass('d-none');
                        $eventsLoadingSpinner.removeClass('d-none'); // Show spinner

                        // Show the modal
                        eventDetailsModalInstance.show();

                        $.ajax({
                            url: `/v1/dashboard/events?date=${eventAt}`, // Replace with your actual API endpoint for events
                            type: 'GET',
                            success: function(response) {
                                $eventsLoadingSpinner.addClass('d-none'); // Hide spinner

                                if (response.success && response.events.length > 0) {
                                    $eventsDisplayArea.removeClass('d-none');
                                    $.each(response.events, function(index, event) {
                                        // Append each event to the display area
                                        if(event.type=='Vehicle Booking'){
                                            $eventsDisplayArea.append(`
                                                <div class="card mb-2">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><a class="dropdown-item view-booking"
                                                            id="vehiclebookingid"
                                                            data-id="${event.id.replace("booking_", "")}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#bookingModal">${event.title}</a></h5>
                                                        <p class="card-text">
                                                            <strong>Time:</strong> ${event.time || 'N/A'}<br>
                                                            
                                                        </p>
                                                    </div>
                                                </div>
                                            `);
                                        } else 
                                        {
                                            $eventsDisplayArea.append(`
                                                <div class="card mb-2">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><a href="/v1/job-assignment-form/view/${event.real_id}/yes">${event.title}</a></h5>
                                                        <p class="card-text">
                                                            <strong>Time:</strong> ${event.time || 'N/A'}<br>
                                                            
                                                        </p>
                                                    </div>
                                                </div>
                                            `);
                                        }
                                        
                                    });
                                } else {
                                    $noEventsMessage.removeClass('d-none'); // Show no events message
                                }
                            },
                            error: function(xhr) {
                                $eventsLoadingSpinner.addClass('d-none'); // Hide spinner
                                $eventsDisplayArea.addClass('d-none'); // Hide display area
                                $noEventsMessage.addClass('d-none'); // Hide no events message

                                let errorMessage = 'Failed to load events. Please try again.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                $eventsDisplayArea.html(`<div class="alert alert-danger">${errorMessage}</div>`).removeClass('d-none');
                                console.error('Error fetching events:', xhr.responseText);
                            }
                        });
                    }

                    function fetchEventsOld(eventAt) {
                        fetch(`/v1/dashboard/eventsbydate/${eventAt}`) // Replace with your actual API URL
                            .then((response) => response.json()) // Convert response to JSON
                            .then((data) => {
                                let eventListDiv = document.getElementById("eventList");
                                eventListDiv.innerHTML = ""; // Clear existing content

                                if (data.length === 0) {
                                    eventListDiv.innerHTML = "<p>No events found.</p>";
                                    return;
                                }

                                let eventHtml = `
                <table class="table" width="300px">
                    <tbody>`;

                                data.forEach((event) => {
                                    if (event.is_vehicle_require == 99) {
                                        eventHtml += `
                    <tr>
                        <td><a  class="dropdown-item view-booking"
                id="vehiclebookingid"
                data-id="${event.id}"
                data-bs-toggle="modal"
                data-bs-target="#bookingModal">${event.title}</a></td>

                    </tr>`;
                                    } else {
                                        eventHtml += `
                    <tr>
                        <td><a  class="dropdown-item" href="/v1/job-assignment-form/view/${event.id}/yes"><small>${event.title}</small></a></td>

                    </tr>`;
                                    }
                                });

                                eventHtml += `
                    </tbody>
                </table>`;

                                eventListDiv.innerHTML = eventHtml;
                            })
                            .catch((error) => console.error("Error fetching events:", error));
                    }

                </script>
                <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="bookingModalLabel">Vehicle Booking Detail</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Vehicle Image -->

                                    <!-- Booking Details -->
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-5"><strong>Vehicle</strong></div>
                                            <div class="col-md-1">:</div>
                                            <div class="col-md-6"><span id="bookingVehicle"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5"><strong>Start Date</strong></div>
                                            <div class="col-md-1">:</div>
                                            <div class="col-md-6"><span id="bookingStart"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5"><strong>End Date</strong></div>
                                            <div class="col-md-1">:</div>
                                            <div class="col-md-6"><span id="bookingEnd"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5"><strong>Purposes</strong></div>
                                            <div class="col-md-1">:</div>
                                            <div class="col-md-6"><span id="bookingPurpose"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5"><strong>Job Assignment</strong></div>
                                            <div class="col-md-1">:</div>
                                            <div class="col-md-6"><span id="bookingJob"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5"><strong>Created By</strong></div>
                                            <div class="col-md-1">:</div>
                                            <div class="col-md-6"><span id="bookingCreator"></span></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <img id="bookingImage" src="" alt="Vehicle Image" class="img-fluid rounded"
                                            style="max-width: 300px;">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <x-booking-modal />
                <!-- /Calendar & Modal -->


            </div>
            <!-- /Calendar Sidebar -->
        </div>
    </div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailsModalLabel">Events for <span id="eventDate"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="events-list-container">
                    <!-- Events will be loaded here -->
                    <div class="text-center py-5" id="events-loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading events...</p>
                    </div>
                    <div id="events-display-area" class="d-none">
                        <!-- Event items will be appended here -->
                    </div>
                    <div id="no-events-message" class="alert alert-info text-center d-none">
                        No events scheduled for this date.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@endsection
