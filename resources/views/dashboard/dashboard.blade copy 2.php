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
                                <h5 class="offcanvas-title" id="addEventSidebarLabel">Detail</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                                    <div class="mb-6 form-control-validation">
                                        <label class="form-label" for="eventTitle">Date Selected</label>
                                        <input type="text" class="form-control" id="eventDate" name="eventDate"
                                           readonly/>
                                    </div>
                                    <h4>Activities</h4>
                                    <div id="eventList"></div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <script src="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>
                    <script>
                        let eventsUrl = "{{ route('v1.dashboard.events') }}";
                        window.events = @json($events);
                    </script>
                    <script src="{{ asset('assets/js/app-calendar-events.js') }}"></script>



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


                </div>
                <!-- /Calendar Sidebar -->
            </div>
        </div>

    </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@endsection
