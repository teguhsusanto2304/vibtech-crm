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
                                    " me-2'> </span>" +
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
                                },
                                dateClick: function (e) {
                                    e = moment(e.date).format("YYYY-MM-DD");
                                    F(),
                                        L.show(),
                                        a && (a.innerHTML = "Add Event"),
                                        (l.innerHTML = "Add"),
                                        l.classList.remove("btn-update-event"),
                                        l.classList.add("btn-add-event"),
                                        i.classList.add("d-none"),
                                        (o.value = e),
                                        (s.value = e);
                                },
                                eventClick: function (e) {
                                    alert(initialDate);
                                    (e = e),
                                        (E = e.event).url &&
                                        (e.jsEvent.preventDefault(),
                                            window.open(E.url, "_blank")),
                                        L.show(),
                                        a && (a.innerHTML = "Update Event"),
                                        (l.innerHTML = "Update"),
                                        l.classList.add("btn-update-event"),
                                        l.classList.remove("btn-add-event"),
                                        i.classList.remove("d-none"),
                                        (d.value = E.title),
                                        D.setDate(E.start, !0, "Y-m-d"),
                                        !0 === E.allDay ? (m.checked = !0) : (m.checked = !1),
                                        null !== E.end
                                            ? P.setDate(E.end, !0, "Y-m-d")
                                            : P.setDate(E.start, !0, "Y-m-d"),
                                        f.val(E.extendedProps.calendar).trigger("change"),
                                        void 0 !== E.extendedProps.location &&
                                        (u.value = E.extendedProps.location),
                                        void 0 !== E.extendedProps.guests &&
                                        h.val(E.extendedProps.guests).trigger("change"),
                                        void 0 !== E.extendedProps.description &&
                                        (v.value = E.extendedProps.description);
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
                            function F() {
                                (s.value = ""),
                                    (c.value = ""),
                                    (o.value = ""),
                                    (d.value = ""),
                                    (u.value = ""),
                                    (m.checked = !1),
                                    h.val("").trigger("change"),
                                    (v.value = "");
                            }
                            S.render(),
                                I(),
                                (A = document.getElementById("eventForm")),
                                FormValidation.formValidation(A, {
                                    fields: {
                                        eventTitle: {
                                            validators: {
                                                notEmpty: { message: "Please enter event title " },
                                            },
                                        },
                                        eventStartDate: {
                                            validators: {
                                                notEmpty: { message: "Please enter start date " },
                                            },
                                        },
                                        eventEndDate: {
                                            validators: {
                                                notEmpty: { message: "Please enter end date " },
                                            },
                                        },
                                    },
                                    plugins: {
                                        trigger: new FormValidation.plugins.Trigger(),
                                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                                            eleValidClass: "",
                                            rowSelector: function (e, t) {
                                                return ".form-control-validation";
                                            },
                                        }),
                                        submitButton: new FormValidation.plugins.SubmitButton(),
                                        autoFocus: new FormValidation.plugins.AutoFocus(),
                                    },
                                })
                                    .on("core.form.valid", function () {
                                        y = !0;
                                    })
                                    .on("core.form.invalid", function () {
                                        y = !1;
                                    }),
                                T &&
                                T.addEventListener("click", (e) => {
                                    r.classList.remove("d-none");
                                }),
                                l.addEventListener("click", (e) => {
                                    var t, n;
                                    l.classList.contains("btn-add-event")
                                        ? y &&
                                        ((n = {
                                            id: S.getEvents().length + 1,
                                            title: d.value,
                                            start: o.value,
                                            end: s.value,
                                            startStr: o.value,
                                            endStr: s.value,
                                            display: "block",
                                            extendedProps: {
                                                location: u.value,
                                                guests: h.val(),
                                                calendar: f.val(),
                                                description: v.value,
                                            },
                                        }),
                                            c.value && (n.url = c.value),
                                            m.checked && (n.allDay = !0),
                                            (n = n),
                                            b.push(n),
                                            S.refetchEvents(),
                                            L.hide())
                                        : y &&
                                        ((n = {
                                            id: E.id,
                                            title: d.value,
                                            start: o.value,
                                            end: s.value,
                                            url: c.value,
                                            extendedProps: {
                                                location: u.value,
                                                guests: h.val(),
                                                calendar: f.val(),
                                                description: v.value,
                                            },
                                            display: "block",
                                            allDay: !!m.checked,
                                        }),
                                            ((t = n).id = parseInt(t.id)),
                                            (b[b.findIndex((e) => e.id === t.id)] = t),
                                            S.refetchEvents(),
                                            L.hide());
                                }),
                                i.addEventListener("click", (e) => {
                                    var t;
                                    (t = parseInt(E.id)),
                                        (b = b.filter(function (e) {
                                            return e.id != t;
                                        })),
                                        S.refetchEvents(),
                                        L.hide();
                                }),
                                x.addEventListener("hidden.bs.offcanvas", function () {
                                    F();
                                }),
                                T.addEventListener("click", (e) => {
                                    a && (a.innerHTML = "Add Event"),
                                        (l.innerHTML = "Add"),
                                        l.classList.remove("btn-update-event"),
                                        l.classList.add("btn-add-event"),
                                        i.classList.add("d-none"),
                                        t.classList.remove("show"),
                                        n.classList.remove("show");
                                }),
                                p &&
                                p.addEventListener("click", (e) => {
                                    e.currentTarget.checked
                                        ? document
                                            .querySelectorAll(".input-filter")
                                            .forEach((e) => (e.checked = 1))
                                        : document
                                            .querySelectorAll(".input-filter")
                                            .forEach((e) => (e.checked = 0)),
                                        S.refetchEvents();
                                }),
                                M &&
                                M.forEach((e) => {
                                    e.addEventListener("click", () => {
                                        document.querySelectorAll(".input-filter:checked")
                                            .length <
                                            document.querySelectorAll(".input-filter").length
                                            ? (p.checked = !1)
                                            : (p.checked = !0),
                                            S.refetchEvents();
                                    });
                                }),
                                e.config.onChange.push(function (e) {
                                    S.changeView(S.view.type, moment(e[0]).format("YYYY-MM-DD")),
                                        I(),
                                        t.classList.remove("show"),
                                        n.classList.remove("show");
                                });
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
        <table class="table">
            <tbody>`;

                                data.forEach((event) => {
                                    if (event.is_vehicle_require == 1) {
                                        eventHtml += `
            <tr>
                <td>${event.title}</td>
                <td>
                <div class="dropdown" >
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                <div class="dropdown-menu">
                  <a  class="dropdown-item" href="/v1/job-assignment-form/view/${event.id}/yes"><small>Detail</small></a>
                  <a class="dropdown-item" href="/v1/job-assignment-form/${event.id}/vehicle-booking"><small>Vehicle Booking</small></a>
                </div>
              </div>

                </td>
            </tr>`;
                                    } else {
                                        eventHtml += `
            <tr>
                <td><small>${event.title}</small></td>
                <td>
                <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base bx bx-dots-vertical-rounded"></i></button>
                <div class="dropdown-menu">
                  <a  class="dropdown-item" href="/v1/job-assignment-form/view/${event.id}/yes">Detail</a>
                </div>
              </div>

                </td>
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
                <!-- /Calendar & Modal -->


            </div>
            <!-- /Calendar Sidebar -->
        </div>
    </div>






    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@endsection
