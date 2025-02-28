@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <link rel="stylesheet" href="../assets/vendor/libs/fullcalendar/fullcalendar.css" />

    <link rel="stylesheet" href="../assets/vendor/css/pages/app-calendar.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
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



            <div class="card app-calendar-wrapper ">
                <div class="row g-0">

                    <!-- Calendar Sidebar -->
                    <div class="col app-calendar-sidebar border-end" id="app-calendar-sidebar" style="display:none;">
                        <div class="border-bottom p-6 my-sm-0 mb-4">
                            <button class="btn btn-primary btn-toggle-sidebar w-100" data-bs-toggle="offcanvas"
                                data-bs-target="#addEventSidebar" aria-controls="addEventSidebar">
                                <i class="icon-base bx bx-plus icon-16px me-2"></i>
                                <span class="align-middle">Add Event</span>
                            </button>
                        </div>
                        <div class="px-3 pt-2">
                            <!-- inline calendar (flatpicker) -->
                            <div class="inline-calendar"></div>
                        </div>
                        <hr class="mb-6 mx-n4 mt-3" />
                        <div class="px-6 pb-2">
                            <!-- Filter -->
                            <div>
                                <h5>Event Filters</h5>
                            </div>

                            <div class="form-check form-check-secondary mb-5 ms-2">
                                <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all"
                                    checked />
                                <label class="form-check-label" for="selectAll">View All</label>
                            </div>

                            <div class="app-calendar-events-filter text-heading">
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
                                <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                                    <div class="mb-6 form-control-validation">
                                        <label class="form-label" for="eventTitle">Title</label>
                                        <input type="text" class="form-control" id="eventTitle" name="eventTitle"
                                            placeholder="Event Title" />
                                    </div>
                                    <div class="mb-6">
                                        <label class="form-label" for="eventLabel">Label</label>
                                        <select class="select2 select-event-label form-select" id="eventLabel"
                                            name="eventLabel">
                                            <option data-label="primary" value="Business" selected>Business</option>
                                            <option data-label="danger" value="Personal">Personal</option>
                                            <option data-label="warning" value="Family">Family</option>
                                            <option data-label="success" value="Holiday">Holiday</option>
                                            <option data-label="info" value="ETC">ETC</option>
                                        </select>
                                    </div>
                                    <div class="mb-6 form-control-validation">
                                        <label class="form-label" for="eventStartDate">Start Date</label>
                                        <input type="text" class="form-control" id="eventStartDate" name="eventStartDate"
                                            placeholder="Start Date" />
                                    </div>
                                    <div class="mb-6 form-control-validation">
                                        <label class="form-label" for="eventEndDate">End Date</label>
                                        <input type="text" class="form-control" id="eventEndDate" name="eventEndDate"
                                            placeholder="End Date" />
                                    </div>
                                    <div class="mb-6">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input allDay-switch"
                                                id="allDaySwitch" />
                                            <label class="form-check-label" for="allDaySwitch">All Day</label>
                                        </div>
                                    </div>
                                    <div class="mb-6">
                                        <label class="form-label" for="eventURL">Event URL</label>
                                        <input type="url" class="form-control" id="eventURL" name="eventURL"
                                            placeholder="https://www.google.com" />
                                    </div>
                                    <div class="mb-4 select2-primary">
                                        <label class="form-label" for="eventGuests">Add Guests</label>
                                        <select class="select2 select-event-guests form-select" id="eventGuests"
                                            name="eventGuests" multiple>
                                            <option data-avatar="1.png" value="Jane Foster">Jane Foster</option>
                                            <option data-avatar="3.png" value="Donna Frank">Donna Frank</option>
                                            <option data-avatar="5.png" value="Gabrielle Robertson">Gabrielle Robertson
                                            </option>
                                            <option data-avatar="7.png" value="Lori Spears">Lori Spears</option>
                                            <option data-avatar="9.png" value="Sandy Vega">Sandy Vega</option>
                                            <option data-avatar="11.png" value="Cheryl May">Cheryl May</option>
                                        </select>
                                    </div>
                                    <div class="mb-6">
                                        <label class="form-label" for="eventLocation">Location</label>
                                        <input type="text" class="form-control" id="eventLocation" name="eventLocation"
                                            placeholder="Enter Location" />
                                    </div>
                                    <div class="mb-6">
                                        <label class="form-label" for="eventDescription">Description</label>
                                        <textarea class="form-control" name="eventDescription"
                                            id="eventDescription"></textarea>
                                    </div>
                                    <div class="d-flex justify-content-sm-between justify-content-start mt-6 gap-2">
                                        <div class="d-flex">
                                            <button type="submit" id="addEventBtn"
                                                class="btn btn-primary btn-add-event me-4">Add</button>
                                            <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                                                data-bs-dismiss="offcanvas">Cancel</button>
                                        </div>
                                        <button class="btn btn-label-danger btn-delete-event d-none">Delete</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script src="../assets/vendor/libs/fullcalendar/fullcalendar.js"></script>
                    <script>
                        let eventsUrl = "{{ route('v1.dashboard.events') }}";
                    </script>
                    <script src="../assets/js/app-calendar-events.js"></script>

                    <!-- Bootstrap Modal -->
                    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Event</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>ID:</strong> <span id="eventId"></span></p>
                                    <p><strong>Title:</strong> <span id="eventTitle"></span></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="../assets/js/app-calendar.js"></script>
                    <!-- /Calendar & Modal -->
                    <!-- Calendar Sidebar -->
                    <div class="col app-calendar-sidebar" id="app-calendar-sidebar"
                        style="margin-left: 20px;margin-top:20px;">
                        <div class="card" style="background-color: #f5f6fa;">
                            <div class="card-header">
                                <div class="panel-header">
                                    <p>Date Selected: <label id="eventAt"></label></p>
                                </div>
                            </div>
                            <div class="card-body">

                                <style>
                                    .activities-container {
                                        display: flex;
                                        flex-direction: column;
                                    }

                                    .activities-container h4 {
                                        margin-bottom: 10px;
                                    }

                                    .activity-item {
                                        background-color: #e0e0ff;
                                        /* Example color, adjust as needed */
                                        padding: 5px 10px;
                                        margin-bottom: 5px;
                                        border-radius: 3px;
                                    }

                                    .activity-item-onleave {
                                        background-color: #baeb0c;
                                        /* Example color, adjust as needed */
                                        padding: 5px 10px;
                                        margin-bottom: 5px;
                                        border-radius: 3px;
                                    }

                                    .activity-item-event {
                                        background-color: #7aec9e;
                                        /* Example color, adjust as needed */
                                        padding: 5px 10px;
                                        margin-bottom: 5px;
                                        border-radius: 3px;
                                    }

                                    .activity-item-ph {
                                        background-color: #f59870;
                                        /* Example color, adjust as needed */
                                        padding: 5px 10px;
                                        margin-bottom: 5px;
                                        border-radius: 3px;
                                        color: #fff;
                                    }
                                </style>
                                <div class="panel-content" style="display: flex;
          flex-direction: column; ">

                                    <h4>Activities</h4>
                                    <div id="eventList" class="activity-item"></div>
                                    <script>
                                        function showEventModal(id, title) {
                                            document.getElementById("eventId").innerText = id;
                                            document.getElementById("eventTitle").innerText = title;
                                            new bootstrap.Modal(document.getElementById('eventModal')).show();
                                        }
                                    </script>


                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /Calendar Sidebar -->
            </div>
        </div>

    </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@endsection
