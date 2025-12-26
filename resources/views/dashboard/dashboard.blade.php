@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <link rel="stylesheet" href="../assets/vendor/libs/fullcalendar/fullcalendar.css" />   

    <link rel="stylesheet" href="../assets/vendor/css/pages/app-calendar.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
/* Gunakan selector yang lebih spesifik agar mengalahkan style bawaan FullCalendar */
.fc-event.bg-custom-danger, 
.bg-custom-danger {
    background-color: #045eb8ff !important;
    border-color: #045eb8ff !important;
    color: white !important;
}

.fc-event.bg-custom-warning, 
.bg-custom-warning {
    background-color: #ffbe4d !important;
    border-color: #ffbe4d !important;
    color: white !important;
}

.custom-danger {
  background-color: #8bc2f9ff !important; /* red shade */
  color: #fff !important;              /* text color */
}

.custom-warning {
  background-color: #b1b0b0ff !important; /* red shade */
  color: #fff !important;              /* text color */
}




        /* Singapore Public Holiday */
.phsingapore {
    background-color: #045eb8 !important;
    border-color: #045eb8 !important;
    color: #ffffff !important;
}

/* Malaysia Public Holiday */
.phmalaysia {
    background-color: #757373 !important;
    border-color: #757373 !important;
    color: #ffffff !important;
}

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
        
        <main>
    <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4" id="groupCardsContainer">
        <!-- Cards will be dynamically inserted here -->
    </div>
</div>

<!-- MODAL (reused for all groups) -->
<div class="modal fade" id="groupNotificationModal" tabindex="-1" aria-labelledby="groupNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupNotificationModalLabel">
                    Unread <span id="modal-group-name"></span> (<span id="modal-notification-count">0</span>)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="list-group list-group-flush" id="modal-notification-list"></ul>
                <div id="no-notifications-message" class="text-center py-4 text-muted" style="display: none;">
                    No new group notifications.
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="mark-group-all-read">Mark All as Read</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // --- SIMULATED DATA ---
    let groupNotifications = @json($groupNotifications); 
    let groupNotifications1 = [
        { id: 1, group_name: "Job Requisition", message: "New policy update on WFH hours.", time: "1 hour ago", url: "/v1/management-memo/detail/10",bgcolor:"bg-primary" },
        { id: 2, group_name: "Job Requisition", message: "Reminder: Townhall meeting tomorrow.", time: "45 minutes ago", url: "/v1/management-memo/detail/11",bgcolor:"bg-primary" },
        { id: 3, group_name: "Submit Claim", message: "Holiday schedule draft released.", time: "15 minutes ago", url: "/v1/management-memo/detail/12",bgcolor:"bg-info" },
        { id: 4, group_name: "Management Memo", message: "New policy update on WFH hours.", time: "1 hour ago", url: "/v1/management-memo/detail/10",bgcolor:"bg-warning" },
        { id: 5, group_name: "Employee Handbook", message: "Reminder: Townhall meeting tomorrow.", time: "45 minutes ago", url: "/v1/management-memo/detail/11",bgcolor:"bg-secondary" },
    ];
    // --- END SIMULATED DATA ---

    const $container = $("#groupCardsContainer");
    const $modalList = $("#modal-notification-list");
    const $modalGroupName = $("#modal-group-name");
    const $modalCount = $("#modal-notification-count");
    const $noNotifMsg = $("#no-notifications-message");

    // --- 1. Group notifications by group_name ---

const grouped = {};
const colorgrouped = {}; // This object is no longer strictly necessary for card rendering but kept for completeness.
//const $container = $('#notification-cards-container'); // Assuming you have a container element to append cards to
const ICON_MAP = {
    "Management Memo": 'bx-home',
    "Employee Handbook": 'bx-users',
    "Submit Claim":'bx-gift',
    "Job Requisition": 'bx-briefcase'
};

groupNotifications.forEach(n => {
    if (n.group_name.toLowerCase() === 'personal') {
        n.bgcolor = 'bg-custom-danger';
    }
    // 1. Group by group_name
    if (!grouped[n.group_name]) {
        grouped[n.group_name] = []; // Initialize array if not exists
    }
    grouped[n.group_name].push(n);

    // 2. Group by bgcolor (Corrected and optional for this specific task)
    if (!colorgrouped[n.bgcolor]) {
        colorgrouped[n.bgcolor] = []; // Initialize array if not exists
    }
    colorgrouped[n.bgcolor].push(n);
});

// Clear the container before rendering new cards
$container.empty();


// --- 2. Render a card for each group ---
Object.keys(grouped).forEach(groupName => {
    const notifications = grouped[groupName];
    const count = notifications.length;
    const iconClass = ICON_MAP[groupName] || 'bx-bell';

    // Ambil bgcolor, jika tidak ada (untuk 'Personal' yang dipaksa di atas), 
    // kita beri default sesuai groupName
    // Gabungkan logika penentuan warna di sini (HAPUS DUPLIKASI)
    

    // FIX 1: Retrieve the bgcolor class from the first notification in the group.
    // We assume the first notification represents the color for the entire group.
    // If n.bgcolor is undefined, it defaults to 'bg-secondary' (a safe Bootstrap class).
    const cardBgColorClass = (notifications[0] && notifications[0].bgcolor) || 'bg-secondary';
    
    // FIX 2: Check if count is 0. If so, apply a different style (e.g., light background)
    // and prevent the modal from opening by removing data attributes.
    const cardClass = (count > 0) 
        ? `${cardBgColorClass} text-white cursor-pointer group-card` 
        : `bg-light text-muted group-card`;
    
    const dataAttributes = (count > 0) 
        ? `data-group="${groupName}" data-bs-toggle="modal" data-bs-target="#groupNotificationModal"`
        : `data-group="${groupName}"`; // Only data-group remains if count is 0

    $container.append(`
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card ${cardClass}" ${dataAttributes}>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bx ${iconClass} me-2" style="font-size: 24px;"></i>
                            <h5 class="fw-bold d-inline-block mb-0 text-white">${groupName}</h5>
                        </div>
                        <div class="text-end">
                            <h2 class="mb-0 text-white">${count}</h2>
                            <small>Unread</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);
});
    // --- 3. Handle click on group card to populate modal ---
    $(document).on("click", ".group-card", function() {
        const groupName = $(this).data("group");
        const notifications = grouped[groupName] || [];

        $modalGroupName.text(groupName);
        $modalCount.text(notifications.length);
        $modalList.empty();

        if (notifications.length === 0) {
            $noNotifMsg.show();
        } else {
            $noNotifMsg.hide();
            notifications.forEach(notif => {
                $modalList.append(`
                    <li class="list-group-item list-group-item-action d-flex align-items-center">
                        <div class="flex-grow-1">
                            <a href="${notif.url}?notif=${notif.id}" class="text-dark d-block">
                                <strong>${notif.message}</strong><br>
                                <small class="text-muted">${notif.time}</small>
                            </a>
                        </div>
                        <i class="bx bx-message-dots text-warning ms-3" style="font-size: 20px;"></i>
                    </li>
                `);
            });
        }

        // Attach handler for "Mark All as Read"
        $("#mark-group-all-read").off("click").on("click", function() {
            //grouped[groupName] = [];
            //$("#groupNotificationModal").modal("hide");
            
            $.ajax({
        url: "{{ route('notifications.mark-group-read') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            group_name: groupName
        },
        success: function(response) {
            if (response.success) {
                // 1. Kosongkan data di variabel lokal agar saat diklik lagi sudah kosong
                grouped[groupName] = [];
                
                // 2. Tutup modal
                $("#groupNotificationModal").modal("hide");
                
                // 3. Update angka di Card Dashboard menjadi 0
                const $card = $container.find(`[data-group="${groupName}"]`);
                $card.find('h2').text(0);
                
                // 4. Ubah tampilan Card menjadi "Muted" (opsional, sesuai logika FIX 2 Anda)
                $card.removeClass('bg-primary bg-success bg-info bg-warning text-white cursor-pointer')
                     .addClass('bg-light text-muted')
                     .removeAttr('data-bs-toggle')
                     .removeAttr('data-bs-target');
                $container.find(`[data-group="${groupName}"]`).closest('.col-lg-3').hide();
                
                // Tampilkan notifikasi sukses (opsional)
                // alert('All ' + groupName + ' notifications marked as read');
            }
        },
        error: function(err) {
            console.error("Error marking as read:", err);
            alert("Failed to mark notifications as read.");
        }
    });
            //$container.find(`[data-group="${groupName}"] h2`).text(0);
        });
    });
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

        <div class="row mb-3">
    <div class="col-md-6">
        <div class="d-flex align-items-center mb-2">
            <div class="me-2 bg-primary" style="width: 20px; height: 20px;"></div>
            <div><small class="fw-bold">Vehicle Booking</small></div>
        </div>
        <div class="d-flex align-items-center mb-2">
            <div class="me-2 bg-success" style="width: 20px; height: 20px;"></div>
            <div><small class="fw-bold">Job Requisition Form</small></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center">
                <div class="me-2" style="width: 20px; height: 20px; background-color: #045eb8ff;"></div>
                <div><small class="fw-bold">Singapore Public Holiday</small></div>
            </div>
            <a href="{{ route('v1.dashboard.event-history') }}?month={{ now()->month }}&year={{ now()->year }}" class="btn btn-outline-primary btn-sm py-0" style="font-size: 0.75rem;">
                <i class="bx bx-history me-1"></i> Event History
            </a>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center">
                <div class="me-2" style="width: 20px; height: 20px; background-color: #757373ff;"></div>
                <div><small class="fw-bold">Malaysia Public Holiday</small></div>
            </div>
            
        </div>
    </div>
</div>
<div class="container mt-4 mb-4">
  <form class="d-flex align-items-end gap-3">
    <!-- Month Select -->
    <div>
      <label for="filterMonth" class="form-label">Month</label>
      <select id="filterMonth" class="form-select bg-white">
        <option value="">Month</option>
        @for ($m = 1; $m <= 12; $m++)
          <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
          </option>
        @endfor
      </select>
    </div>

    <!-- Year Select -->
    <div>
      <label for="filterYear" class="form-label">Year</label>
      <select id="filterYear" class="form-select bg-white">
        <option value="">Year</option>
        @php
          $currentYear = date('Y');
          $nextYear = $currentYear + 1;
          $minYear = 2025;
        @endphp
        @for ($year = $currentYear; $year <= $nextYear; $year++)
          @if ($year >= $minYear)
            <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
              {{ $year }}
            </option>
          @endif
        @endfor
      </select>
    </div>

    <!-- Clear Button -->
    <div>
      <button type="button" id="clearSessionBtn" class="btn btn-outline-warning" disabled>
  Clear
</button>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const clearBtn = document.getElementById("clearSessionBtn");

    // Check if localStorage has values
    const hasCalendarYear = localStorage.getItem("calendarYear") !== null;
    const hasCalendarMonth = localStorage.getItem("calendarMonth") !== null;

    if (hasCalendarYear || hasCalendarMonth) {
      clearBtn.removeAttribute("disabled"); // enable button
    }

    clearBtn.addEventListener("click", function() {
      // Remove specific keys
      localStorage.removeItem("calendarYear");
      localStorage.removeItem("calendarMonth");

      // Disable button again after clearing
      clearBtn.setAttribute("disabled", "true");

      // Reset dropdowns to current date (optional)
      const now = new Date();
      document.getElementById("filterYear").value = now.getFullYear();
      document.getElementById("filterMonth").value = now.getMonth() + 1;
    });
  });
</script>
    </div>
  </form>
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

                    .bg-label-danger {
                        height: 40px;
                    }
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
                    .dot-Personal::before {
                        color: red;
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
                                Personal: "custom-danger",
                                Family: "custom-warning",
                                ETC: "info",
                                PHSingapore: "danger",
                                PHMalaysia: "danger"
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
                            let storedDate = localStorage.getItem('calendarDefaultDate');
                            
                            let S = new Calendar(w, {
                                initialDate: storedDate ? storedDate : new Date(),
                                initialView: "dayGridMonth",
                                eventContent: function (arg) {

                                    // public holiday
                                    if (arg.event.extendedProps.type === 'public_holiday') {
        let label = document.createElement('div');
        label.style.fontSize = '11px';
        label.style.padding = '2px 4px';
        label.style.color = '#fff';
        label.style.backgroundColor = arg.event.backgroundColor;
        label.style.borderRadius = '4px';
        label.innerText = arg.event.title;

        return { domNodes: [label] };
    }
                                    // end public holiday

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
                                                const currentDate = S.view.currentStart;


                                            const currentYear = currentDate.getFullYear();
                                            const currentMonth = currentDate.getMonth() + 1;

                                            // Save to localStorage
                                            localStorage.setItem('calendarYear', currentYear);
                                            localStorage.setItem('calendarMonth', currentMonth);

                                            let eventId = arg.event.id; // Get event ID
                                            let url = `/v1/job-assignment-form/view/${eventId}/yes?fr=main`; // Replace with your target URL

                                            // Redirect to the new page
                                            window.location.href = url;
                                        } else if (eventStatus === "VB") {

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
                                        } else if (eventStatus === "PH") {
                                            let eventId = arg.event.id;
                                            let modal = document.getElementById('publicholidayModal');
                                            let modalTitle = modal.querySelector('.modal-title');
                                            let modalBody = modal.querySelector('.modal-body');

                                            // Update modal title with the event ID
                                            modalTitle.innerText = `Public Holiday Details`;

                                            // Show loading text while fetching data
                                            modalBody.innerHTML = `<p>Loading details...</p>`;

                                            // Show the modal
                                            var myModal = new bootstrap.Modal(modal);
                                            myModal.show();

                                            // Fetch event details from API
                                            fetch(`/v1/leave-application/public-holiday/${eventId}/modal`) // Replace with your API URL
                                                .then(response => response.json())
                                                .then(data => {
                                                    // Populate modal with fetched data
                                                    modalBody.innerHTML = `
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-4 fw-bold">Title</div>
            <div class="col-8">: ${data.title}</div>
        </div>

        <div class="row mb-2">
            <div class="col-4 fw-bold">Public Holiday Date</div>
            <div class="col-8">: ${data.leave_date}</div>
        </div>

        <div class="row mb-2">
            <div class="col-4 fw-bold">Country</div>
            <div class="col-8">: ${data.country}</div>
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
                                moreLinkClick: function(info) {
                                    // Prevent default behavior (no navigation, no popover)
                                    return false;
                                },
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
                                    if (e._def.extendedProps.calendar !== 'Personal' && e._def.extendedProps.calendar !== 'Family') {
                                        return ['bg-label-' + g[e._def.extendedProps.calendar]];
                                    } else {
                                        return g[e._def.extendedProps.category];
                                    }

                                    //return ["bg-label-" + g[e._def.extendedProps.calendar]];
                                    //return ["event-dot", "dot-" + e._def.extendedProps.calendar];
                                },
                                datesSet: function (info) {
                                    let currentYear = info.view.currentStart.getFullYear();
                                    let currentMonth = info.view.currentStart.getMonth() + 1;
                                    // Update your dropdowns
                                    document.getElementById('filterYear').value = currentYear;
                                    document.getElementById('filterMonth').value = currentMonth;

                                    I();
                                    highlightCurrentDate();
                                    
                                },
                                viewDidMount: function (info) {
                                    I();
                                    highlightCurrentDate();
                                },
                            });

                            S.render()
                            //let savedYear = localStorage.getItem('calendarYear');
                            //let savedMonth = localStorage.getItem('calendarMonth');
                            let savedYear = localStorage.getItem('calendarYear');
                            let savedMonth = localStorage.getItem('calendarMonth');

                            if (savedYear && savedMonth) {
                                const date = new Date(savedYear, savedMonth - 1, 1);
                                S.gotoDate(date);

                                document.getElementById('filterYear').value = savedYear;
                                document.getElementById('filterMonth').value = savedMonth;
                            }


                            

                            document.getElementById('filterMonth').addEventListener('change', applyFilter);
                            document.getElementById('filterYear').addEventListener('change', applyFilter);

                            function applyFilter() {
                                const month = document.getElementById('filterMonth').value;
                                const year = document.getElementById('filterYear').value;

                                if (!month || !year) return;

                                const date = new Date(year, month - 1, 1);
                                S.gotoDate(date);
                            }

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

                <div class="modal fade" id="publicholidayModal" tabindex="-1" aria-labelledby="publicholidayModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="publicholidayModalLabel">Public Holiday Detail</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Vehicle Image -->

                                    <!-- Booking Details -->
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-5"><strong>Public Holiday</strong></div>
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
