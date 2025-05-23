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
                        <div class="offcanvas-body">
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
                <div class="col app-calendar-sidebar border-end" id="app-calendar-sidebar">
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
                                    highlightCurrentDate();
                                },
                                viewDidMount: function () {
                                    I();
                                    highlightCurrentDate();
                                },
                            });

                            S.render()

                        }

                        function highlightCurrentDate() {
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



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@endsection
