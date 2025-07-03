<!-- Menu -->

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('v1.dashboard') }}" class="app-brand-link">
            <img src="{{ asset('assets/img/logo.png') }}" width="170px" height="70px">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>
    <style>
        .no-bullet {
            list-style: none;
            /* Removes bullet points */
            position: relative;
            display: flex;
            align-items: center;
            flex: 0 1 auto;
            margin: 0;
            padding: 0;
            /* Optional: Removes extra padding */
        }

        .menu-link.active {
            background-color: #5356ff5c !important;
            /* Change this to your preferred color */
            color: white !important;
            /* Change text color if needed */
            border-radius: 5px;
            /* Optional: Adds rounded corners */
        }
    </style>

    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1" style="margin-top: 2rem">
        <!-- Dashboards -->
        <li class="menu-item">
            <a href="{{ route('v1.dashboard')}}" class="menu-link">
                <i class="menu-icon">
                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 35.15 35.15">
                        <defs>
                            <style>
                                .cls-1 {
                                    fill: #fff;
                                }
                            </style>
                        </defs>
                        <path id="Path" class="cls-1"
                            d="M13.51,18.89H1.31c-.72,0-1.31-.59-1.31-1.31V1.31C0,.59.59,0,1.31,0h12.2c.72,0,1.31.59,1.31,1.31v16.26c0,.72-.59,1.31-1.31,1.31ZM1.4,17.49h12.02V1.4H1.4v16.08Z" />
                        <path id="Path-2" class="cls-1"
                            d="M13.51,35.15H1.31c-.72,0-1.31-.59-1.31-1.31v-8.13c0-.72.59-1.31,1.31-1.31h12.2c.72,0,1.31.59,1.31,1.31v8.13c0,.72-.59,1.31-1.31,1.31ZM1.4,33.75h12.02v-7.95H1.4v7.95Z" />
                        <path id="Path-3" class="cls-1"
                            d="M33.84,35.15h-12.2c-.72,0-1.31-.59-1.31-1.31v-16.26c0-.72.59-1.31,1.31-1.31h12.2c.72,0,1.31.59,1.31,1.31v16.26c0,.72-.59,1.31-1.31,1.31ZM21.73,33.75h12.02v-16.08h-12.02v16.08Z" />
                        <path id="Path-4" class="cls-1"
                            d="M33.84,14.15h-12.2c-.67.05-1.26-.46-1.31-1.13,0,0,0-.01,0-.02V1.15c.04-.67.62-1.19,1.29-1.15,0,0,.01,0,.02,0h12.2c.67-.05,1.26.46,1.31,1.13,0,0,0,.01,0,.02v11.86c-.04.67-.62,1.19-1.29,1.15,0,0-.01,0-.02,0ZM21.73,12.75h12.02V1.4h-12.02v11.35Z" />
                    </svg>
                </i>
                <div class="text-truncate" data-i18n="Dashboards">Staff Calendar</div>
            </a>
        </li>

        @if(
                    auth()->user()->can('view-job-requisition') ||
                    auth()->user()->can('view-leave-application') ||
                    auth()->user()->can('view-vehicle-booking') ||
                    auth()->user()->can('view-referral-program') ||
                    auth()->user()->can('view-submit-claim')
                )
                <li class="menu-item {{ request()->routeIs(
                'v1.job-assignment-form',
                'v1.job-assignment-form.create',
                'v1.job-assignment-form.list',
                'leave-application',
                'v1.vehicle-bookings',
                'referral-program',
                'submit-claim'
            ) ? 'active open' : '' }}">
                    <a href="{{ route('dashboard') }}" class="menu-link  menu-toggle">
                        <i class="menu-icon">
                            <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 35.15 33.23">
                                <defs>
                                    <style>
                                        .cls-task {
                                            fill: none;
                                            stroke: #fff;
                                            stroke-miterlimit: 10;
                                            stroke-width: 1.4px;
                                        }
                                    </style>
                                </defs>
                                <path id="Path" class="cls-task"
                                    d="M1.27.7h32.6c.32,0,.57.26.58.57v11.51c0,.32-.26.57-.58.57H1.27c-.32,0-.57-.26-.57-.57V1.27c0-.32.26-.57.57-.57Z" />
                                <path id="Path-2" class="cls-task"
                                    d="M1.27,19.88h32.6c.32,0,.57.26.58.57v11.51c0,.32-.26.57-.58.57H1.27c-.32,0-.57-.26-.57-.57v-11.51c0-.32.26-.57.57-.57Z" />
                            </svg>
                        </i>
                        <div class="text-truncate" data-i18n="Dashboards">Staff Task</div>
                    </a>
                    <ul class="menu-sub">
                        @can('view-job-requisition')
                            <li class="no-bullet">
                                <a href="{{ route('v1.job-assignment-form') }}"
                                    class="menu-link {{ request()->routeIs('v1.job-assignment-form', 'v1.job-assignment-form.create', 'v1.job-assignment-form.list') ? 'active' : '' }}">
                                    <div class="text-truncate" data-i18n="Analytics">Job Requisition Form</div>
                                </a>
                            </li>
                        @endcan
                        @can('view-leave-application')
                            <li class="no-bullet">
                                <a href="{{ route('leave-application') }}"
                                    class="menu-link  {{ request()->routeIs('leave-application') ? 'active' : '' }}">
                                    <div class="text-truncate" data-i18n="Analytics">Leave Application</div>
                                </a>
                            </li>
                        @endcan
                        @can('view-vehicle-booking')
                            <li class="no-bullet">
                                <a href="{{ route('v1.vehicle-bookings')}}"
                                    class="menu-link {{ request()->routeIs('v1.vehicle-bookings', 'v1.vehicle-bookings.create', 'v1.vehicle-bookings.list') ? 'active' : '' }}">
                                    <div class="text-truncate" data-i18n="Analytics">Vehicle Booking</div>
                                </a>
                            </li>
                        @endcan
                        @can('view-referral-program')
                            <li class="no-bullet">
                                <a href="{{ route('referral-program') }}" class="menu-link">
                                    <div class="text-truncate" data-i18n="Analytics">Referral Program</div>
                                </a>
                            </li>
                        @endcan
                        @can('view-submit-claim')
                            <li class="no-bullet">
                                <a href="{{ route('submit-claim')}}" class="menu-link">
                                    <div class="text-truncate" data-i18n="Analytics">Submit Claim</div>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
        @endif

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon">
                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 35 35">
                        <defs>
                            <style>
                                .cls-staff {
                                    stroke-miterlimit: 10;
                                }

                                .cls-staff,
                                .cls-2 {
                                    fill: none;
                                    stroke: #fff;
                                }
                            </style>
                        </defs>
                        <rect id="Rectangle" class="cls-staff" x=".5" y=".5" width="34" height="34" rx="3" ry="3" />
                        <path id="Path" class="cls-2" d="M15.5,25.1h4v4.4h-4v-4.4Z" />
                        <path id="Path-2" class="cls-2" d="M15.5,7.5h4v13.2h-4V7.5Z" />
                    </svg>
                </i>
                <div class="text-truncate" data-i18n="Dashboards" title="Staff Information Hub">Staff Information Hub
                </div>
            </a>

            <ul class="menu-sub">
                <li class="no-bullet">
                    <a href="{{ route('v1.management-memo.list')}}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Management Memo</div>
                    </a>
                </li>
                <li class="no-bullet">
                    <a href="{{ route('v1.employee-handbooks.list')}}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Employee Handbook</div>
                    </a>
                </li>
                @can('view-whistleblowing-policy')
                    <li class="no-bullet">
                        <a href="{{ route('v1.whistleblowing-policy')}}" class="menu-link">
                            <div class="text-truncate" data-i18n="Analytics">Whistleblowing Policy</div>
                        </a>
                    </li>
                @endcan
                @can('view-getting-started')
                    <li class="no-bullet">
                        <a href="{{ route('v1.getting-started')}}" class="menu-link">
                            <div class="text-truncate" data-i18n="Analytics">Getting Started</div>
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
        @can('view-client-database')
            <li class="menu-item">
                <a href="{{ route('v1.client-database') }}" class="menu-link">
                    <i class="menu-icon">
                        <svg width="30px" height="30px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">

                            <defs>

                                <style>
                                    .cls-client-database {
                                        fill: none;
                                        stroke: #a5a3a3;
                                        stroke-linecap: round;
                                        stroke-linejoin: round;
                                        stroke-width: 2px;
                                    }
                                </style>

                            </defs>

                            <title />

                            <g data-name="79-users" id="_79-users">

                                <circle class="cls-client-database" cx="16" cy="13" r="5" />

                                <path class="cls-client-database" d="M23,28A7,7,0,0,0,9,28Z" />

                                <path class="cls-client-database" d="M24,14a5,5,0,1,0-4-8" />

                                <path class="cls-client-database" d="M25,24h6a7,7,0,0,0-7-7" />

                                <path class="cls-client-database" d="M12,6a5,5,0,1,0-4,8" />

                                <path class="cls-client-database" d="M8,17a7,7,0,0,0-7,7H7" />

                            </g>

                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards">Client Database</div>
                </a>
            </li>
        @endcan

        @can('view-project-management')
            <li class="menu-item">
                <a href="{{ route('v1.project-management') }}" class="menu-link">
                    <i class="menu-icon">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 3L5 3C3.89543 3 3 3.89543 3 5L3 19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="#a5a3a3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10.4 6H6.6C6.26863 6 6 6.26863 6 6.6L6 17.4C6 17.7314 6.26863 18 6.6 18H10.4C10.7314 18 11 17.7314 11 17.4V6.6C11 6.26863 10.7314 6 10.4 6Z" stroke="#a5a3a3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.4 6H13.6C13.2686 6 13 6.26863 13 6.6V13.4C13 13.7314 13.2686 14 13.6 14H17.4C17.7314 14 18 13.7314 18 13.4V6.6C18 6.26863 17.7314 6 17.4 6Z" stroke="#a5a3a3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards">Project Management</div>
                </a>
            </li>
        @endcan

        @if(auth()->user()->can('view-mass-emailer')  || auth()->user()->can('view-social-media-scheduler') || auth()->user()->can('view-event-generation'))
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon">
                        <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44.8 37.35">
                            <defs>
                                <style>
                                    .cls-marketing {
                                        fill: #fff;
                                    }
                                </style>
                            </defs>
                            <path id="Path_2" data-name="Path 2" class="cls-marketing"
                                d="M23.47,20.72s-.09.01-.14,0l-.1-.05c-4.65-2.28-9.6-3.91-14.7-4.83H2.57C1.15,15.84,0,14.68,0,13.26c0-.01,0-.02,0-.03v-4.67c.02-1.41,1.16-2.53,2.57-2.53h6c5.09-.92,10.02-2.55,14.66-4.83l.1-.07c.17-.08.38,0,.46.17.08.17,0,.38-.17.46h0l-.1.04c-4.71,2.32-9.71,3.97-14.88,4.9H2.57c-1.02.01-1.85.84-1.87,1.86v4.67c0,1.04.83,1.89,1.87,1.9,0,0,0,0,0,0h6c5.19.93,10.22,2.58,14.94,4.9l.1.05c.17.08.25.28.17.45-.06.12-.18.2-.32.2h0Z" />
                            <path id="Path_3" data-name="Path 3" class="cls-marketing"
                                d="M8.57,15.82c-.19,0-.34-.15-.34-.34h0V6.38c0-.19.16-.35.35-.35s.35.16.35.35h0v9.1c0,.19-.15.34-.34.34,0,0,0,0-.01,0Z" />
                            <path id="Path_4" data-name="Path 4" class="cls-marketing"
                                d="M9.37,25.21h-3.16c-.75,0-1.41-.48-1.63-1.2l-2.03-8.48c-.04-.18.07-.36.24-.41.19-.04.37.07.42.25l2.03,8.45c.13.42.53.7.97.7h3.16c.25,0,.45-.2.44-.45,0-.17-.1-.32-.24-.4l-.53-.28c-.27-.15-.48-.4-.56-.7l-1.65-7.18c-.04-.19.08-.37.26-.41.18-.04.37.07.41.26,0,0,0,0,0,0l1.64,7.17c.04.1.11.18.21.23l.55.29c.56.29.78.97.49,1.53-.2.38-.59.62-1.01.61h0Z" />
                            <path id="Path_5" data-name="Path 5" class="cls-marketing"
                                d="M24.98,14.52c-.19.02-.37-.12-.39-.31-.02-.19.11-.36.3-.39,1.19-.45,1.96-1.62,1.91-2.89.08-1.3-.7-2.49-1.91-2.95-.18-.06-.28-.25-.22-.43.05-.17.23-.27.41-.23,1.52.53,2.51,2,2.42,3.61.1,1.61-.89,3.09-2.42,3.62l-.09-.03Z" />
                            <path id="Path_6" data-name="Path 6" class="cls-marketing"
                                d="M32.97,11.23h-4.37c-.19,0-.35-.16-.35-.35s.16-.35.35-.35h4.37c.19,0,.35.16.35.35s-.16.35-.35.35h0Z" />
                            <path id="Path_7" data-name="Path 7" class="cls-marketing"
                                d="M28.37,8.46c-.13,0-.25-.07-.31-.18-.08-.17-.02-.38.15-.47l3.98-2.1c.17-.08.38-.02.47.15.09.17.02.37-.15.46l-3.98,2.1c-.05.02-.11.04-.16.04Z" />
                            <path id="Path_8" data-name="Path 8" class="cls-marketing"
                                d="M32.35,16.11c-.06,0-.11-.01-.16-.04l-3.98-2.1c-.17-.09-.23-.3-.15-.47.09-.17.3-.23.46-.14,0,0,0,0,0,0l3.98,2.1c.17.08.24.29.16.46-.06.12-.18.2-.32.19Z" />
                            <path id="Path_9" data-name="Path 9" class="cls-marketing"
                                d="M10.1,37.34c-.19,0-.34-.15-.34-.34v-7.94h-2.97v7.94c0,.19-.16.35-.35.35-.19,0-.35-.16-.35-.35h0v-8.28c0-.19.15-.34.34-.34h3.66c.19,0,.34.15.34.34v8.28c0,.19-.15.34-.34.34h0Z" />
                            <path id="Path_10" data-name="Path 10" class="cls-marketing"
                                d="M16.65,37.34c-.19,0-.34-.15-.34-.34v-13.47h-2.97v13.47c0,.19-.16.35-.35.35s-.35-.16-.35-.35v-13.81c0-.19.15-.34.34-.34,0,0,0,0,0,0h3.65c.19,0,.35.15.35.34,0,0,0,0,0,0v13.81c0,.19-.15.34-.34.34h0Z" />
                            <path id="Path_11" data-name="Path 11" class="cls-marketing"
                                d="M23.21,37.34c-.19,0-.35-.15-.35-.34v-10.74h-2.97v10.74c0,.19-.16.35-.35.35-.19,0-.35-.16-.35-.35h0v-11.09c0-.19.15-.34.34-.34h3.66c.19,0,.34.15.34.34v11.09c0,.18-.15.34-.33.34Z" />
                            <path id="Path_12" data-name="Path 12" class="cls-marketing"
                                d="M29.76,37.34c-.19,0-.34-.15-.34-.34v-6.57h-2.97v6.56c0,.19-.16.35-.35.35s-.35-.16-.35-.35v-6.91c0-.19.15-.34.34-.34h3.66c.19,0,.34.15.34.34v6.91c0,.19-.15.34-.34.34Z" />
                            <path id="Path_13" data-name="Path 13" class="cls-marketing"
                                d="M36.31,37.34c-.19,0-.34-.15-.34-.34v-16.59h-2.97v16.59c0,.19-.16.35-.35.35-.19,0-.35-.16-.35-.35v-16.94c0-.19.15-.34.34-.34,0,0,0,0,0,0h3.65c.19,0,.35.15.35.34,0,0,0,0,0,0v16.94c0,.19-.15.34-.34.34Z" />
                            <path id="Path_14" data-name="Path 14" class="cls-marketing"
                                d="M42.87,37.34c-.19,0-.35-.15-.35-.34,0,0,0,0,0,0V9.27h-2.97v27.73c0,.19-.16.35-.35.35s-.35-.16-.35-.35V8.92c0-.19.15-.35.34-.35,0,0,0,0,0,0h3.66c.19,0,.34.15.34.34,0,0,0,0,0,0v28.07c0,.18-.15.34-.33.34Z" />
                            <path id="Path_15" data-name="Path 15" class="cls-marketing"
                                d="M44.45,37.34H4.86c-.19,0-.35-.16-.35-.35s.16-.35.35-.35h39.59c.19,0,.35.16.35.35s-.16.35-.35.35Z" />
                            <path id="Path_1" data-name="Path 1" class="cls-marketing"
                                d="M24.24,21.87h0c-.6,0-1.08-.49-1.08-1.08V1c.05-.61.59-1.05,1.19-1,.53.05.95.47,1,1v19.79c0,.6-.49,1.09-1.08,1.09,0,0-.01,0-.02,0ZM24.24.6c-.22,0-.41.17-.42.39,0,0,0,0,0,0v19.79c0,.22.18.4.4.4h0c.22,0,.4-.18.4-.4V1c0-.21-.17-.39-.38-.4Z" />
                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards" title="Staff Information Hub">Marketing </div>
                </a>

                <ul class="menu-sub">
                    @can('view-mass-emailer')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Mass Emailer</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-social-media-scheduler')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Social Media Scheduler</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-event-generation')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Event Generation</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        @if(
                auth()->user()->can('view-shipping-status') ||
                auth()->user()->can('view-account-receivable') ||
                auth()->user()->can('view-human-resource') ||
                auth()->user()->can('view-procurement')
            )
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon">
                        <svg id="Layer_12" data-name="Layer 12" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 387.86 386.04">
                            <defs>
                                <style>
                                    .cls-admin {
                                        fill: #fff;
                                    }
                                </style>
                            </defs>
                            <path class="cls-admin"
                                d="M277.56,215.14c-22.42,0-40.6,18.18-40.6,40.6s18.18,40.6,40.6,40.6,40.6-18.18,40.6-40.6-18.18-40.6-40.6-40.6ZM277.56,284.84c-15.76,0-28.48-12.73-28.48-28.48s12.73-28.48,28.48-28.48,28.48,12.73,28.48,28.48-12.73,28.48-28.48,28.48Z" />
                            <path class="cls-admin"
                                d="M381.19,5.45c-3.64-3.64-8.48-5.45-13.94-5.45H100.6c-1.21,0-3.03,0-4.24.61-9.09,1.82-16.36,10.3-16.36,20v152.72c-20,8.48-34.54,28.48-34.54,51.51,0,15.15,6.06,32.12,15.76,45.45l-45.45,16.36c-.61,0-1.21.61-1.21.61-9.09,6.06-14.54,16.97-14.54,27.88v64.85c0,3.64,2.42,6.06,6.06,6.06h190.29c3.64,0,6.06-2.42,6.06-6.06v-63.63c0-11.51-5.45-21.82-15.15-28.48-.61,0-.61-.61-1.21-.61l-45.45-15.76c7.27-9.7,12.73-21.82,14.54-33.33h47.88l-2.42.61c-3.03.61-4.85,3.03-4.85,6.06v23.64c0,3.03,1.82,5.45,4.85,6.06l11.51,1.82c1.21,4.24,3.03,8.48,4.85,12.12l-6.67,9.7c-1.82,2.42-1.21,5.45.61,7.88l16.36,16.97c1.82,1.82,5.45,2.42,7.88.61l9.7-6.67c4.24,1.82,7.88,3.64,12.12,4.85l1.82,11.51c.61,3.03,3.03,4.85,6.06,4.85h23.64c3.03,0,5.45-1.82,6.06-4.85l1.82-11.51c4.24-1.21,8.48-3.03,12.12-4.85l9.7,6.67c2.42,1.82,5.45,1.21,7.88-.61l16.97-16.97c1.82-1.82,2.42-5.45.61-7.88l-6.67-9.7c2.42-4.24,3.64-7.88,4.85-12.12l11.51-1.82c3.03-.61,4.85-3.03,4.85-6.06v-23.64c0-3.03-1.82-5.45-4.85-6.06l-1.82-1.21h16.36c10.91,0,20-9.09,20-20V20c0-5.45-2.42-10.91-6.67-14.54h0ZM92.12,20c0-4.24,3.64-7.88,7.88-7.88h267.26c1.21,0,1.82,0,3.03.61s1.82,1.21,2.42,1.82c1.21,1.21,2.42,3.64,2.42,6.06v22.42H92.12v-23.03ZM181.2,298.17c6.06,4.24,9.09,10.91,9.09,17.57v58.18H12.12v-58.79c0-7.27,3.64-13.33,9.09-17.57l49.09-18.18c9.09,8.48,20,13.94,31.51,13.94s21.82-5.45,30.91-13.33l48.48,18.18ZM101.21,281.81c-23.03,0-43.63-32.12-43.63-57.57s19.39-43.63,43.63-43.63,43.63,19.39,43.63,43.63c0,26.06-20.61,57.57-43.63,57.57ZM346.65,262.41l-10.3,1.82c-2.42.61-4.24,2.42-4.85,4.85-1.21,5.45-3.64,10.91-6.67,15.76-1.21,1.82-1.21,4.85,0,6.67l6.06,8.48-9.7,9.7-8.48-6.06c-1.82-1.21-4.85-1.21-6.67,0-4.85,3.03-10.3,5.45-16.36,6.67-2.42.61-4.24,2.42-4.85,4.85l-1.82,10.3h-13.33l-1.82-10.3c-.61-2.42-2.42-4.24-4.85-4.85-6.06-1.21-10.91-3.64-16.36-6.67-1.82-1.21-4.85-1.21-6.67,0l-8.48,6.06-9.7-9.7,6.06-8.48c1.21-1.82,1.21-4.85,0-6.67-3.03-4.85-5.45-10.3-6.67-16.36-.61-2.42-2.42-4.24-4.85-4.85l-10.3-1.82v-13.33l10.3-1.82c2.42-.61,4.24-2.42,4.85-4.85,1.21-5.45,3.64-10.91,6.67-16.36,1.21-1.82,1.21-4.85,0-6.67l-6.06-8.48,9.7-9.7,8.48,6.06c1.82,1.21,4.85,1.21,6.67,0,4.85-3.03,10.3-5.45,16.36-6.67,2.42-.61,4.24-2.42,4.85-4.85l1.82-10.3h13.33l1.82,10.3c.61,2.42,2.42,4.24,4.85,4.85,6.06,1.21,11.51,3.64,16.36,6.67,1.82,1.21,4.85,1.21,6.67,0l8.48-6.06,9.7,9.7-6.06,8.48c-1.21,1.82-1.21,4.85,0,6.67,3.03,4.85,5.45,10.3,6.67,16.36.61,2.42,2.42,4.24,4.85,4.85l10.3,1.82v13.94ZM375.74,217.57c0,4.24-3.64,7.88-7.88,7.88h-29.09c-.61-.61-.61-1.21-1.21-1.82l6.67-9.7c1.82-2.42,1.21-5.45-.61-7.88l-16.97-16.36c-1.82-1.82-5.45-2.42-7.88-.61l-9.7,6.67c-3.64-1.82-7.88-3.64-12.12-4.85l-1.82-11.51c-.61-3.03-3.03-4.85-6.06-4.85h-23.64c-3.03,0-5.45,1.82-6.06,4.85l-1.82,11.51c-4.24,1.21-8.48,3.03-12.12,4.85l-9.7-6.67c-2.42-1.82-5.45-1.21-7.88.61l-16.36,16.36c-1.82,1.82-2.42,5.45-.61,7.88l6.67,9.7c-.61.61-.61,1.82-1.21,2.42-.61-.61-1.82-.61-2.42-.61h-56.97v-1.21c0-30.91-24.85-55.75-55.75-55.75-3.03,0-6.06.61-9.09.61V53.94h283.62v163.63Z" />
                            <path class="cls-admin"
                                d="M126.66,21.21h-8.48c-3.64,0-6.06,2.42-6.06,6.06s2.42,6.06,6.06,6.06h8.48c3.64,0,6.06-2.42,6.06-6.06,0-3.03-3.03-6.06-6.06-6.06Z" />
                            <path class="cls-admin"
                                d="M166.05,21.21h-8.48c-3.64,0-6.06,2.42-6.06,6.06s2.42,6.06,6.06,6.06h8.48c3.64,0,6.06-2.42,6.06-6.06,0-3.03-3.03-6.06-6.06-6.06Z" />
                            <path class="cls-admin"
                                d="M205.44,21.21h-8.48c-3.64,0-6.06,2.42-6.06,6.06s2.42,6.06,6.06,6.06h8.48c3.64,0,6.06-2.42,6.06-6.06,0-3.03-3.03-6.06-6.06-6.06Z" />
                            <path class="cls-admin"
                                d="M350.29,21.21h-109.09c-3.64,0-6.06,2.42-6.06,6.06s2.42,6.06,6.06,6.06h109.69c3.64,0,6.06-2.42,6.06-6.06-.61-3.03-3.03-6.06-6.67-6.06h0Z" />
                            <path class="cls-admin"
                                d="M126.05,86.06h216.35c3.64,0,6.06-2.42,6.06-6.06s-2.42-6.06-6.06-6.06H126.05c-3.64,0-6.06,2.42-6.06,6.06s2.42,6.06,6.06,6.06Z" />
                            <path class="cls-admin"
                                d="M126.05,122.42h216.35c3.64,0,6.06-2.42,6.06-6.06s-2.42-6.06-6.06-6.06H126.05c-3.64,0-6.06,2.42-6.06,6.06,0,3.03,2.42,6.06,6.06,6.06Z" />
                            <path class="cls-admin"
                                d="M126.05,158.17h216.35c3.64,0,6.06-2.42,6.06-6.06s-2.42-6.06-6.06-6.06H126.05c-3.64,0-6.06,2.42-6.06,6.06,0,3.03,2.42,6.06,6.06,6.06Z" />
                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards" title="Staff Information Hub">Admin </div>
                </a>
                <ul class="menu-sub">
                    @can('view-shipping-status')
                        <li class="no-bullet">
                            <a href="{{ route('shipping-status')}}" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Shipping/Delivery Status</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-account-receivable')
                        <li class="no-bullet">
                            <a href="{{ route('account-receivable-list')}}" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Account Receivable</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-human-resource')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Human Resource</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-procurement')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Procurement</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        @if(
                auth()->user()->can('view-create-sales-quotation') ||
                auth()->user()->can('view-download-purchase-order') ||
                auth()->user()->can('view-order-status') ||
                auth()->user()->can('view-invoices')
            )
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon">
                        <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 219 245.11">
                            <defs>
                                <style>
                                    .cls-sales {
                                        fill: #fff;
                                    }
                                </style>
                            </defs>
                            <path class="cls-sales"
                                d="M36.62,57.91c-.51-2.05.74-4.13,2.79-4.64,2.05-.51,4.13.74,4.64,2.79,1.22,4.88,6.24,6.52,10.18,6.41,3.43-.08,6.63-1.27,8.15-3.01.83-.96,1.13-2.06.96-3.47-.29-2.36-1.7-5.25-10.14-6.64-12.2-2-15.04-8.34-15.28-13.3-.33-6.86,4.43-12.6,11.84-14.29.06-.01.13-.03.19-.04v-2.51c0-2.11,1.71-3.83,3.83-3.83s3.83,1.71,3.83,3.83v2.48c4.9,1,9.6,4.03,12.03,9.82.82,1.95-.1,4.2-2.05,5.01-1.95.82-4.2-.1-5.01-2.05-2.06-4.9-7.07-6.16-11.13-5.24-2.96.67-6.07,2.72-5.89,6.45.06,1.28.23,4.69,8.87,6.11,10.09,1.66,15.64,6.12,16.51,13.27.43,3.56-.53,6.81-2.77,9.4-2.38,2.76-6.2,4.67-10.56,5.37v2.39c0,2.11-1.71,3.83-3.83,3.83s-3.83-1.71-3.83-3.83v-2.45c-6.66-1.25-11.78-5.67-13.32-11.86h0ZM8.05,45.72C8.05,20.51,28.56,0,53.77,0s45.72,20.51,45.72,45.72-20.51,45.72-45.72,45.72S8.05,70.93,8.05,45.72ZM15.71,45.72c0,20.99,17.07,38.06,38.06,38.06s38.06-17.07,38.06-38.06S74.76,7.66,53.77,7.66,15.71,24.73,15.71,45.72ZM151.5,95.4c2.11,0,3.83,1.71,3.83,3.83v116.83c0,2.11-1.71,3.83-3.83,3.83h-29.4c-2.11,0-3.83-1.71-3.83-3.83v-116.83c0-2.11,1.71-3.83,3.83-3.83h29.4ZM147.67,103.06h-21.74v109.17h21.74v-109.17ZM96.91,126.27c2.11,0,3.83,1.71,3.83,3.83v85.96c0,2.11-1.71,3.83-3.83,3.83h-29.4c-2.11,0-3.83-1.71-3.83-3.83v-85.96c0-2.11,1.71-3.83,3.83-3.83h29.4ZM93.08,133.92h-21.74v78.31h21.74v-78.31ZM209.93,71.47v144.59c0,2.11-1.71,3.83-3.83,3.83h-29.4c-2.11,0-3.83-1.71-3.83-3.83V71.47c0-2.11,1.71-3.83,3.83-3.83h29.4c2.12,0,3.83,1.71,3.83,3.83h0ZM202.27,75.3h-21.74v136.93h21.74V75.3ZM42.31,155.82c2.11,0,3.83,1.71,3.83,3.83v56.41c0,2.11-1.71,3.83-3.83,3.83H12.9c-2.11,0-3.83-1.71-3.83-3.83v-56.41c0-2.11,1.71-3.83,3.83-3.83h29.4ZM38.48,163.48h-21.74v48.75h21.74v-48.75ZM9.81,135.63c.42,0,.84-.07,1.26-.21,35.1-12.22,68.41-27.26,99-44.7,32.66-18.62,63.14-40.49,90.7-65.07l-1.85,12.73c-.3,2.09,1.15,4.04,3.24,4.34.19.03.37.04.56.04,1.87,0,3.51-1.37,3.79-3.28l3.39-23.3c.17-1.14-.19-2.29-.96-3.13s-1.89-1.3-3.04-1.24l-23.52,1.28c-2.11.11-3.73,1.92-3.62,4.03s1.92,3.72,4.03,3.62l12.77-.69c-52.58,46.86-115.45,83.23-187.01,108.15-2,.7-3.05,2.88-2.36,4.88.55,1.58,2.03,2.57,3.62,2.57h0ZM219,230.17v11.1c0,2.11-1.71,3.83-3.83,3.83H3.83c-2.11,0-3.83-1.71-3.83-3.83v-11.1c0-2.11,1.71-3.83,3.83-3.83h211.35c2.11,0,3.83,1.71,3.83,3.83h0ZM211.34,234H7.66v3.44h203.69v-3.44h0Z" />
                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards" title="Staff Information Hub">Sales </div>
                </a>
                @can('view-create-sales-quotation')
                    <ul class="menu-sub">
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Create Sales Quotation</div>
                            </a>
                        </li>
                @endcan
                    @can('view-download-purchase-order')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Download Purchase Order</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-order-status')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Order Status</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-invoices')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Invoices & Payment Status</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        @if(
                auth()->user()->can('view-receive-job-order') ||
                auth()->user()->can('view-internal-job-planing-form') ||
                auth()->user()->can('view-schedule-job')
            )
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon">
                        <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 226.73 226.72">
                            <defs>
                                <style>
                                    .cls-operations {
                                        fill: #fff;
                                    }
                                </style>
                            </defs>
                            <path class="cls-operations"
                                d="M76.47,107.62c-23.51,0-42.63,19.12-42.63,42.63s19.12,42.65,42.63,42.65,42.63-19.13,42.63-42.65-19.12-42.63-42.63-42.63h0ZM76.47,186.48c-19.96,0-36.21-16.25-36.21-36.22s16.24-36.21,36.21-36.21,36.21,16.24,36.21,36.21-16.24,36.22-36.21,36.22ZM130.88,178.94c1.76-3.34,3.22-6.85,4.34-10.47l15.65-6.05c1.24-.48,2.06-1.67,2.06-3v-18.3c0-1.33-.82-2.52-2.06-3l-15.65-6.05c-1.12-3.61-2.57-7.12-4.34-10.47l6.78-15.35c.54-1.21.27-2.63-.67-3.57l-12.93-12.95c-.94-.94-2.35-1.2-3.58-.67l-15.35,6.79c-3.35-1.77-6.86-3.23-10.47-4.35l-6.06-15.65c-.48-1.24-1.67-2.05-3-2.05h-18.29c-1.33,0-2.52.82-3,2.05l-6.06,15.65c-3.62,1.12-7.13,2.58-10.47,4.35l-15.35-6.79c-1.22-.54-2.63-.27-3.58.67l-12.93,12.95c-.94.94-1.2,2.35-.67,3.57l6.78,15.35c-1.77,3.35-3.22,6.86-4.34,10.47l-15.65,6.05c-1.24.48-2.06,1.67-2.06,3v18.3c0,1.33.82,2.52,2.06,3l15.65,6.05c1.12,3.62,2.57,7.13,4.34,10.47l-6.78,15.35c-.54,1.21-.27,2.63.67,3.57l12.93,12.95c.94.94,2.36,1.21,3.58.67l15.35-6.79c3.36,1.77,6.87,3.23,10.47,4.35l6.06,15.64c.48,1.23,1.67,2.05,3,2.05h18.29c1.33,0,2.52-.82,3-2.05l6.06-15.64c3.58-1.12,7.1-2.57,10.47-4.35l15.35,6.79c1.22.54,2.63.27,3.58-.67l12.93-12.95c.94-.94,1.2-2.35.67-3.57l-6.78-15.35ZM121.08,204.7l-14.78-6.54c-.92-.41-1.99-.36-2.87.13-3.85,2.15-7.9,3.84-12.05,5.01-.97.28-1.76.99-2.12,1.93l-5.84,15.06h-13.89l-5.84-15.06c-.37-.94-1.15-1.66-2.12-1.93-4.18-1.17-8.23-2.86-12.04-5-.89-.5-1.95-.55-2.87-.14l-14.78,6.54-9.82-9.83,6.53-14.78c.41-.93.36-1.99-.14-2.87-2.13-3.79-3.8-7.84-4.99-12.04-.28-.97-.99-1.76-1.93-2.12l-15.07-5.83v-13.9l15.07-5.83c.95-.37,1.66-1.16,1.94-2.13,1.17-4.19,2.85-8.24,4.99-12.04.5-.88.55-1.95.14-2.87l-6.53-14.78,9.82-9.83,14.78,6.54c.93.41,1.99.36,2.88-.14,3.8-2.14,7.85-3.82,12.04-5,.97-.28,1.76-.99,2.12-1.93l5.84-15.07h13.89l5.84,15.07c.37.94,1.15,1.66,2.12,1.93,4.19,1.18,8.24,2.86,12.04,5,.89.5,1.95.55,2.88.14l14.78-6.54,9.82,9.83-6.53,14.78c-.41.93-.36,1.99.14,2.87,2.13,3.8,3.81,7.85,4.99,12.04.28.97.99,1.76,1.94,2.13l15.07,5.83v13.9l-15.07,5.83c-.95.37-1.66,1.15-1.93,2.12-1.18,4.21-2.86,8.26-4.99,12.04-.5.88-.55,1.95-.14,2.87l6.53,14.78-9.82,9.83h-.01ZM38.52,57.98c10.74,0,19.48-8.73,19.48-19.46s-8.74-19.47-19.48-19.47-19.46,8.74-19.46,19.47,8.73,19.46,19.46,19.46ZM38.52,25.47c7.2,0,13.05,5.85,13.05,13.05s-5.85,13.03-13.05,13.03-13.03-5.85-13.03-13.03,5.85-13.05,13.03-13.05ZM2.07,45.93l6.8,2.63c.45,1.32.98,2.6,1.59,3.83l-2.95,6.68c-.54,1.21-.27,2.63.67,3.57l6.23,6.23c.94.94,2.35,1.2,3.57.67l6.68-2.95c1.23.61,2.51,1.14,3.83,1.59l2.63,6.8c.48,1.24,1.67,2.06,3,2.06h8.82c1.33,0,2.52-.82,3-2.05l2.64-6.81c1.32-.45,2.6-.98,3.83-1.59l6.68,2.95c1.22.54,2.63.27,3.57-.67l6.23-6.23c.94-.94,1.2-2.35.67-3.57l-2.95-6.68c.61-1.23,1.14-2.51,1.59-3.83l6.8-2.63c1.24-.48,2.06-1.67,2.06-3v-8.82c0-1.33-.82-2.52-2.05-3l-6.81-2.64c-.45-1.32-.98-2.6-1.59-3.83l2.95-6.67c.54-1.21.27-2.63-.67-3.57l-6.23-6.25c-.94-.94-2.35-1.21-3.57-.67l-6.68,2.95c-1.23-.61-2.51-1.14-3.83-1.59l-2.64-6.81C45.45.82,44.25,0,42.93,0h-8.82C32.78,0,31.59.82,31.11,2.06l-2.63,6.8c-1.32.45-2.6.98-3.83,1.59l-6.68-2.95c-1.22-.54-2.63-.27-3.57.67l-6.23,6.25c-.94.94-1.2,2.35-.67,3.57l2.95,6.67c-.61,1.23-1.14,2.51-1.59,3.83l-6.81,2.64c-1.23.48-2.05,1.67-2.05,3v8.82c0,1.33.82,2.52,2.06,3h0ZM6.44,36.31l6.2-2.4c.94-.36,1.65-1.15,1.93-2.12.54-1.91,1.3-3.75,2.26-5.43.5-.89.55-1.95.14-2.88l-2.69-6.08,3.12-3.13,6.09,2.69c.93.41,1.99.36,2.88-.14,1.69-.95,3.52-1.71,5.43-2.26.97-.28,1.76-.99,2.12-1.93l2.39-6.19h4.42l2.4,6.2c.36.94,1.15,1.65,2.12,1.93,1.92.54,3.75,1.3,5.43,2.26.88.5,1.95.55,2.88.14l6.09-2.69,3.12,3.13-2.69,6.08c-.41.93-.36,2,.14,2.88.95,1.69,1.71,3.52,2.26,5.43.28.97.99,1.76,1.93,2.12l6.2,2.4v4.42l-6.19,2.39c-.94.36-1.66,1.15-1.93,2.12-.54,1.91-1.3,3.75-2.26,5.43-.5.88-.55,1.95-.14,2.88l2.69,6.09-3.12,3.12-6.09-2.69c-.93-.41-1.99-.36-2.88.14-1.69.95-3.52,1.71-5.43,2.26-.97.28-1.75.99-2.12,1.93l-2.4,6.2h-4.42l-2.39-6.19c-.36-.94-1.15-1.66-2.12-1.93-1.92-.54-3.75-1.3-5.43-2.26-.89-.5-1.95-.55-2.88-.14l-6.09,2.69-3.12-3.12,2.69-6.09c.41-.93.36-2-.14-2.88-.95-1.69-1.71-3.52-2.26-5.43-.28-.97-.99-1.76-1.93-2.12l-6.19-2.39v-4.42h0ZM175.89,25.7c-13.86,0-25.14,11.28-25.14,25.14s11.28,25.15,25.14,25.15,25.15-11.28,25.15-25.15-11.28-25.14-25.15-25.14ZM175.89,69.56c-10.32,0-18.71-8.4-18.71-18.72s8.39-18.71,18.71-18.71,18.72,8.39,18.72,18.71-8.4,18.72-18.72,18.72ZM224.68,41.9l-9.66-3.74c-.67-2.06-1.5-4.07-2.5-6l4.19-9.48c.54-1.21.27-2.63-.67-3.57l-8.4-8.42c-.94-.94-2.36-1.21-3.57-.67l-9.5,4.19c-1.92-.98-3.93-1.81-5.99-2.48l-3.74-9.67c-.48-1.24-1.67-2.06-3-2.06h-11.91c-1.33,0-2.52.82-3,2.06l-3.74,9.67c-2.07.67-4.08,1.5-6,2.48l-9.48-4.19c-1.22-.54-2.63-.27-3.57.67l-8.42,8.42c-.94.94-1.2,2.36-.67,3.57l4.19,9.48c-.98,1.93-1.81,3.94-2.48,6l-9.67,3.74c-1.24.48-2.06,1.67-2.06,3v11.91c0,1.33.82,2.52,2.06,3l9.67,3.74c.67,2.07,1.5,4.07,2.48,5.99l-4.19,9.5c-.54,1.22-.27,2.63.67,3.57l8.42,8.4c.94.94,2.35,1.2,3.57.67l9.48-4.19c1.94.99,3.95,1.83,6,2.5l3.74,9.66c.48,1.24,1.67,2.05,3,2.05h11.91c1.33,0,2.52-.82,3-2.05l3.74-9.67c2.05-.67,4.06-1.5,5.99-2.49l9.49,4.19c1.22.54,2.63.27,3.57-.67l8.4-8.4c.94-.94,1.2-2.36.67-3.57l-4.19-9.49c.99-1.94,1.83-3.94,2.49-5.99l9.67-3.74c1.24-.48,2.05-1.67,2.05-3v-11.91c0-1.33-.82-2.52-2.05-3h0ZM220.3,54.6l-9.07,3.51c-.95.37-1.66,1.15-1.93,2.12-.74,2.62-1.8,5.17-3.15,7.58-.5.88-.55,1.95-.14,2.87l3.94,8.91-5.29,5.29-8.91-3.94c-.93-.41-1.99-.36-2.87.14-2.41,1.36-4.96,2.42-7.58,3.15-.97.28-1.76.99-2.12,1.93l-3.51,9.07h-7.5l-3.51-9.07c-.37-.95-1.16-1.66-2.13-1.93-2.63-.73-5.18-1.79-7.59-3.15-.89-.5-1.95-.55-2.88-.14l-8.9,3.94-5.3-5.29,3.94-8.91c.41-.93.36-1.99-.14-2.87-1.34-2.38-2.4-4.93-3.14-7.58-.28-.97-.99-1.76-1.93-2.12l-9.08-3.51v-7.5l9.08-3.51c.95-.37,1.66-1.16,1.94-2.13.74-2.65,1.8-5.21,3.14-7.59.5-.88.55-1.95.14-2.88l-3.94-8.9,5.3-5.3,8.9,3.94c.93.41,1.99.36,2.88-.14,2.38-1.34,4.93-2.4,7.59-3.14.98-.28,1.77-.99,2.13-1.94l3.51-9.08h7.5l3.51,9.08c.36.95,1.15,1.66,2.12,1.93,2.65.75,5.2,1.8,7.58,3.14.88.5,1.95.55,2.87.14l8.91-3.94,5.29,5.3-3.94,8.9c-.41.93-.36,1.99.14,2.88,1.36,2.41,2.42,4.97,3.15,7.59.27.97.99,1.76,1.93,2.13l9.07,3.51v7.5ZM188.22,168.75c-10.73,0-19.46,8.73-19.46,19.46s8.73,19.46,19.46,19.46,19.46-8.73,19.46-19.46-8.73-19.46-19.46-19.46ZM188.22,201.25c-7.19,0-13.03-5.85-13.03-13.03s5.85-13.03,13.03-13.03,13.03,5.85,13.03,13.03-5.85,13.03-13.03,13.03ZM224.68,180.81l-6.8-2.63c-.45-1.31-.98-2.59-1.59-3.84l2.95-6.67c.54-1.21.27-2.63-.67-3.57l-6.23-6.25c-.94-.94-2.36-1.21-3.58-.67l-6.67,2.95c-1.25-.61-2.53-1.15-3.84-1.58l-2.63-6.8c-.48-1.24-1.67-2.06-3-2.06h-8.82c-1.33,0-2.52.82-3,2.05l-2.64,6.81c-1.3.44-2.58.97-3.83,1.58l-6.67-2.95c-1.22-.54-2.63-.27-3.57.67l-6.25,6.25c-.94.94-1.2,2.36-.67,3.57l2.95,6.67c-.61,1.24-1.15,2.52-1.59,3.84l-6.8,2.63c-1.24.48-2.06,1.67-2.06,3v8.82c0,1.33.82,2.52,2.06,3l6.8,2.63c.45,1.31.98,2.59,1.59,3.84l-2.95,6.67c-.54,1.22-.27,2.63.67,3.58l6.25,6.23c.94.94,2.35,1.2,3.57.67l6.67-2.95c1.25.62,2.53,1.16,3.83,1.59l2.64,6.8c.48,1.23,1.67,2.05,3,2.05h8.82c1.33,0,2.52-.82,3-2.06l2.63-6.79c1.3-.44,2.58-.97,3.84-1.6l6.67,2.95c1.21.54,2.63.27,3.57-.67l6.23-6.23c.94-.94,1.2-2.36.67-3.57l-2.95-6.67c.61-1.24,1.15-2.52,1.59-3.84l6.8-2.63c1.24-.48,2.06-1.67,2.06-3v-8.82c0-1.33-.82-2.52-2.06-3h0ZM220.3,190.42l-6.19,2.39c-.95.36-1.66,1.15-1.94,2.12-.53,1.9-1.29,3.73-2.26,5.45-.5.88-.55,1.95-.14,2.87l2.68,6.08-3.12,3.12-6.08-2.68c-.93-.41-1.99-.36-2.87.14-1.76.99-3.59,1.75-5.43,2.25-.98.27-1.78.99-2.14,1.94l-2.39,6.19h-4.42l-2.4-6.2c-.37-.95-1.16-1.66-2.14-1.93-1.84-.51-3.67-1.27-5.42-2.26-.88-.5-1.95-.55-2.87-.14l-6.08,2.69-3.13-3.12,2.68-6.08c.41-.93.36-1.99-.14-2.87-.96-1.72-1.73-3.56-2.26-5.45-.28-.97-.99-1.76-1.94-2.12l-6.19-2.39v-4.42l6.19-2.39c.95-.36,1.66-1.15,1.94-2.12.53-1.9,1.29-3.73,2.26-5.45.5-.88.55-1.95.14-2.87l-2.68-6.08,3.13-3.13,6.08,2.69c.92.41,1.99.36,2.87-.14,1.73-.97,3.55-1.73,5.42-2.24.98-.27,1.77-.99,2.14-1.94l2.4-6.21h4.42l2.4,6.21c.37.95,1.16,1.67,2.14,1.94,1.88.52,3.7,1.27,5.43,2.24.88.5,1.95.55,2.87.14l6.08-2.68,3.12,3.13-2.69,6.08c-.41.93-.36,1.99.14,2.87.96,1.72,1.73,3.56,2.26,5.45.28.97.99,1.76,1.94,2.12l6.19,2.39v4.42h0ZM97.24,135.35c1.25,1.25,1.25,3.29,0,4.54l-25.3,25.3c-.6.6-1.42.94-2.27.94s-1.67-.34-2.27-.94l-11.69-11.71c-1.25-1.26-1.25-3.29,0-4.54,1.26-1.25,3.29-1.25,4.54,0l9.42,9.44,23.03-23.03c1.25-1.25,3.29-1.25,4.54,0Z" />
                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards" title="Staff Information Hub">Operations </div>
                </a>
                <ul class="menu-sub">
                    @can('view-receive-job-order')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Receive Job Order</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-internal-job-planing-form')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Internal Job Planing Form</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-schedule-job')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Schedule Job</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        @if(
                auth()->user()->can('view-create-sales-quotation-system-project') ||
                auth()->user()->can('view-receive-job-order-system-project') ||
                auth()->user()->can('view-internal-job-planing-form-system-project') ||
                auth()->user()->can('view-schedule-job-system-project') ||
                auth()->user()->can('view-invoicing-status') ||
                auth()->user()->can('view-payment-status')
            )
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon">
                        <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 288 288">
                            <defs>
                                <style>
                                    .cls-project {
                                        fill: #fff;
                                    }
                                </style>
                            </defs>
                            <path class="cls-project"
                                d="M46.45,185.81h46.45c7.68,0,13.94-6.25,13.94-13.94s-6.25-13.94-13.94-13.94h-46.45c-7.68,0-13.94,6.25-13.94,13.94s6.25,13.94,13.94,13.94ZM46.45,167.23h46.45c2.56,0,4.65,2.08,4.65,4.65s-2.08,4.65-4.65,4.65h-46.45c-2.56,0-4.65-2.08-4.65-4.65s2.08-4.65,4.65-4.65Z" />
                            <path class="cls-project"
                                d="M274.06,102.19c7.68,0,13.94-6.25,13.94-13.94V13.94c0-7.68-6.25-13.94-13.94-13.94h-32.52c-4.37,0-8.53,2.09-11.14,5.57l-9.76,13.01h-39.48c-7.68,0-13.94,6.25-13.94,13.94v18.58h-42.04C122.81,22.53,98.85,0,69.68,0,38.94,0,13.94,25,13.94,55.74s22.53,53.13,51.1,55.51v28.21C27.66,140.51,0,154.07,0,171.87v88.26c0,18.1,35.9,27.87,69.68,27.87s69.68-9.77,69.68-27.87v-27.87h28.11c2.39,31.13,28.42,55.74,60.15,55.74,33.3,0,60.39-27.09,60.39-60.39s-24.61-57.76-55.74-60.15v-65.27h41.81ZM274.06,92.9h-92.9c-2.56,0-4.65-2.09-4.65-4.65v-41.81h102.19v41.81c0,2.56-2.08,4.65-4.65,4.65ZM181.16,27.87h44.13l12.55-16.73c.87-1.16,2.26-1.85,3.71-1.85h32.52c2.56,0,4.65,2.09,4.65,4.65v23.23h-102.19v-4.65c0-2.56,2.08-4.65,4.65-4.65ZM23.23,55.74c0-4.85.76-9.53,2.14-13.94h17.84c-.86,4.34-1.4,8.97-1.4,13.94s.54,9.6,1.41,13.94h-17.85c-1.38-4.4-2.14-9.08-2.14-13.94ZM51.1,55.74c0-5.01.64-9.64,1.64-13.94h33.89c.99,4.29,1.63,8.92,1.63,13.94s-.64,9.64-1.64,13.94h-33.89c-.99-4.29-1.63-8.92-1.63-13.94ZM69.69,11.01c3.61,3.67,9.77,11,14.02,21.5h-28.03c4.25-10.48,10.41-17.82,14.01-21.5ZM83.68,78.97c-4.25,10.48-10.41,17.82-14.01,21.5-3.61-3.67-9.77-11-14.02-21.5h28.03ZM93.68,78.97h16.17c-6.05,10.41-16,18.25-27.86,21.52,4.06-5.24,8.57-12.44,11.7-21.52h0ZM116.13,55.74c0,4.85-.75,9.53-2.14,13.94h-17.84c.86-4.34,1.4-8.97,1.4-13.94s-.54-9.6-1.41-13.94h17.84c1.39,4.4,2.15,9.08,2.15,13.94ZM109.84,32.52h-16.17c-3.12-9.08-7.63-16.28-11.7-21.52,11.86,3.27,21.81,11.11,27.86,21.52ZM57.37,11c-4.06,5.24-8.57,12.44-11.7,21.52h-16.17c6.05-10.41,16-18.25,27.86-21.52ZM29.51,78.97h16.17c3.12,9.08,7.63,16.28,11.7,21.52-11.86-3.27-21.81-11.11-27.86-21.52ZM69.68,148.65c35.59,0,60.39,12.24,60.39,23.23s-24.8,23.23-60.39,23.23-60.39-12.24-60.39-23.23,24.8-23.23,60.39-23.23ZM9.29,188.51c11.82,9.63,33.9,15.87,60.39,15.87s48.57-6.24,60.39-15.87v15.87c0,7.58-23.52,18.58-60.39,18.58s-60.39-11-60.39-18.58v-15.87ZM9.29,218.82c12.85,8.8,37.12,13.44,60.39,13.44s47.53-4.64,60.39-13.44v13.44c0,7.58-23.52,18.58-60.39,18.58s-60.39-11-60.39-18.58v-13.44ZM69.68,278.71c-36.86,0-60.39-11-60.39-18.58v-13.44c12.85,8.8,37.12,13.44,60.39,13.44s47.53-4.64,60.39-13.44v13.44c0,7.58-23.52,18.58-60.39,18.58ZM192.08,264.25c4.25-13.46,16.56-22.71,30.89-22.71h9.29c14.34,0,26.65,9.25,30.89,22.71-9.21,8.93-21.73,14.46-35.54,14.46s-26.33-5.53-35.54-14.46ZM227.61,232.26c-10.25,0-18.58-8.33-18.58-18.58s8.33-18.58,18.58-18.58,18.58,8.33,18.58,18.58-8.33,18.58-18.58,18.58ZM278.71,227.61c0,10.54-3.21,20.35-8.71,28.5-4.81-10.19-13.47-17.92-23.99-21.55,5.79-5.11,9.47-12.57,9.47-20.88,0-15.37-12.5-27.87-27.87-27.87s-27.87,12.5-27.87,27.87c0,8.31,3.68,15.77,9.48,20.88-10.52,3.63-19.18,11.36-23.99,21.55-5.5-8.15-8.71-17.95-8.71-28.5,0-28.17,22.92-51.1,51.1-51.1s51.1,22.92,51.1,51.1ZM222.97,167.46c-29.59,2.27-53.24,25.92-55.51,55.51h-28.11v-51.1c0-17.8-27.66-31.36-65.03-32.41v-28.21c27.03-2.25,48.62-23.83,50.86-50.86h42.04v27.87c0,7.68,6.25,13.94,13.94,13.94h41.81v65.27Z" />
                            <path class="cls-project" d="M260.13,18.58h9.29v9.29h-9.29v-9.29Z" />
                            <path class="cls-project" d="M241.55,18.58h9.29v9.29h-9.29v-9.29Z" />
                            <path class="cls-project" d="M185.81,74.32h9.29v9.29h-9.29v-9.29Z" />
                            <path class="cls-project" d="M204.39,74.32h9.29v9.29h-9.29v-9.29Z" />
                            <path class="cls-project" d="M222.97,74.32h9.29v9.29h-9.29v-9.29Z" />
                            <path class="cls-project" d="M74.32,51.1h9.29v9.29h-9.29v-9.29Z" />
                            <path class="cls-project" d="M55.74,51.1h9.29v9.29h-9.29v-9.29Z" />
                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards" title="Staff Information Hub">System Project
                    </div>
                </a>
                <ul class="menu-sub">
                    @can('view-create-sales-quotation-system-project')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Create Sales Quotation</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-receive-job-order-system-project')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Receive Job Order</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-internal-job-planing-form-system-project')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Internal Job Planing Form</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-schedule-job-system-project')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Schedule Job</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-invoicing-status')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Invoicing Status</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-payment-status')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Payment Status</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        @if(
                auth()->user()->can('view-create-sales-quotation-it') ||
                auth()->user()->can('view-receive-job-order-it') ||
                auth()->user()->can('view-internal-job-planing-form-it') ||
                auth()->user()->can('view-schedule-job-it') ||
                auth()->user()->can('view-invoicing-status-it') ||
                auth()->user()->can('view-payment-status-it')
            )
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon">
                        <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220.6 220.6">
                            <defs>
                                <style>
                                    .cls-it {
                                        fill: #fff;
                                    }
                                </style>
                            </defs>
                            <path class="cls-it"
                                d="M202.81,0H17.79C7.98,0,0,7.98,0,17.79v131.65c0,9.81,7.98,17.79,17.79,17.79h66.58c2.91,4.16,4.58,9.12,4.58,14.23,0,3.71-.88,7.35-2.45,10.67h-15.34c-9.81,0-17.79,7.98-17.79,17.79v7.12c0,1.96,1.59,3.56,3.56,3.56h106.74c1.97,0,3.56-1.59,3.56-3.56v-7.12c0-9.81-7.98-17.79-17.79-17.79h-15.34c-1.58-3.32-2.45-6.96-2.45-10.67,0-5.18,1.62-10.04,4.56-14.23h66.6c9.81,0,17.79-7.98,17.79-17.79V17.79c0-9.81-7.98-17.79-17.79-17.79ZM17.79,7.12h185.02c5.88,0,10.67,4.79,10.67,10.67v99.62h-7.12V21.35c0-3.92-3.19-7.12-7.12-7.12H21.35c-3.92,0-7.12,3.19-7.12,7.12v96.07h-7.12V17.79c0-5.88,4.79-10.67,10.67-10.67ZM139.36,53.38c2.97-2.27,6.64-3.57,10.45-3.57,7.34,0,13.93,4.76,16.4,11.84.5,1.43,1.85,2.39,3.36,2.39h29.68v7.12h-29.72c-1.51,0-2.86.95-3.36,2.38-2.15,6.1-7.46,10.66-13.54,11.63-4.84.78-9.59-.5-13.29-3.34,7.58-.3,13.66-6.56,13.66-14.22s-6.06-13.9-13.63-14.22h0ZM199.25,56.93h-27.29c-4.02-8.61-12.63-14.23-22.15-14.23-8.74,0-16.89,4.78-21.26,12.47-.63,1.1-.62,2.45.02,3.55s1.81,1.77,3.08,1.77h7.11c3.92,0,7.12,3.19,7.12,7.12s-3.19,7.12-7.12,7.12h-7.12c-1.26,0-2.44.68-3.07,1.78-.64,1.1-.64,2.45-.02,3.55,4.46,7.83,12.52,12.47,21.26,12.47,1.3,0,2.61-.1,3.93-.31,7.8-1.24,14.69-6.6,18.16-13.92h27.34v39.14H21.35V21.35h177.9v35.58ZM160.11,209.92v3.56H60.49v-3.56c0-5.88,4.79-10.67,10.67-10.67h78.28c5.88,0,10.67,4.79,10.67,10.67ZM126.43,192.13h-32.26c1.21-3.42,1.9-7.01,1.9-10.67,0-4.97-1.19-9.83-3.37-14.23h35.2c-2.18,4.4-3.37,9.28-3.37,14.23,0,3.66.69,7.26,1.9,10.67ZM202.81,160.11H17.79c-5.88,0-10.67-4.79-10.67-10.67v-24.91h206.36v24.91c0,5.88-4.79,10.67-10.67,10.67Z" />
                            <path class="cls-it"
                                d="M110.3,131.65c-5.88,0-10.67,4.79-10.67,10.67s4.79,10.67,10.67,10.67,10.67-4.79,10.67-10.67-4.79-10.67-10.67-10.67ZM110.3,145.88c-1.96,0-3.56-1.6-3.56-3.56s1.59-3.56,3.56-3.56,3.56,1.6,3.56,3.56-1.59,3.56-3.56,3.56Z" />
                            <path class="cls-it"
                                d="M46.25,78.28h4.92l1.43,2.86-5.3,5.3c-1.39,1.39-1.39,3.64,0,5.03l10.67,10.67c1.39,1.39,3.64,1.39,5.03,0l5.3-5.3,2.86,1.43v4.92c0,1.96,1.59,3.56,3.56,3.56h14.23c1.97,0,3.56-1.59,3.56-3.56v-4.92l2.86-1.43,5.3,5.3c1.39,1.39,3.64,1.39,5.03,0l10.67-10.67c1.39-1.39,1.39-3.64,0-5.03l-5.3-5.3,1.43-2.86h4.92c1.97,0,3.56-1.59,3.56-3.56v-14.23c0-1.96-1.59-3.56-3.56-3.56h-4.92l-1.43-2.86,5.3-5.3c1.39-1.39,1.39-3.64,0-5.03l-10.67-10.67c-1.39-1.39-3.64-1.39-5.03,0l-5.3,5.3-2.86-1.43v-4.92c0-1.96-1.59-3.56-3.56-3.56h-14.23c-1.97,0-3.56,1.59-3.56,3.56v4.92l-2.86,1.43-5.3-5.3c-1.39-1.39-3.64-1.39-5.03,0l-10.67,10.67c-1.39,1.39-1.39,3.64,0,5.03l5.3,5.3-1.43,2.86h-4.92c-1.97,0-3.56,1.59-3.56,3.56v14.23c0,1.96,1.59,3.56,3.56,3.56ZM49.81,64.04h3.56c1.35,0,2.58-.76,3.18-1.97l3.56-7.12c.68-1.37.42-3.02-.67-4.11l-4.6-4.6,5.64-5.64,4.6,4.6c1.09,1.09,2.74,1.35,4.11.67l7.12-3.56c1.21-.6,1.97-1.84,1.97-3.18v-3.56h7.12v3.56c0,1.35.76,2.58,1.97,3.18l7.12,3.56c1.37.68,3.02.42,4.11-.67l4.6-4.6,5.64,5.64-4.6,4.6c-1.09,1.08-1.35,2.74-.67,4.11l3.56,7.12c.6,1.21,1.84,1.97,3.18,1.97h3.56v7.12h-3.56c-1.35,0-2.58.76-3.18,1.97l-3.56,7.12c-.68,1.37-.42,3.02.67,4.11l4.6,4.6-5.64,5.64-4.6-4.6c-1.09-1.08-2.74-1.34-4.11-.67l-7.12,3.56c-1.21.6-1.97,1.84-1.97,3.18v3.56h-7.12v-3.56c0-1.35-.76-2.58-1.97-3.18l-7.12-3.56c-1.37-.68-3.02-.42-4.11.67l-4.6,4.6-5.64-5.64,4.6-4.6c1.09-1.08,1.35-2.74.67-4.11l-3.56-7.12c-.6-1.21-1.84-1.97-3.18-1.97h-3.56v-7.12Z" />
                            <path class="cls-it"
                                d="M81.83,85.39c9.81,0,17.79-7.98,17.79-17.79s-7.98-17.79-17.79-17.79-17.79,7.98-17.79,17.79,7.98,17.79,17.79,17.79ZM81.83,56.93c5.88,0,10.67,4.79,10.67,10.67s-4.79,10.67-10.67,10.67-10.67-4.79-10.67-10.67,4.79-10.67,10.67-10.67Z" />
                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards" title="Staff Information Hub">IT </div>
                </a>
                <ul class="menu-sub">
                    @can('view-create-sales-quotation-it')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Create Sales Quotation</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-receive-job-order-it')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Receive Job Order</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-internal-job-planing-form-it')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Internal Job Planing Form</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-schedule-job-it')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Schedule Job</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-invoicing-status-it')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Invoicing Status</div>
                            </a>
                        </li>
                    @endcan
                    @can('view-payment-status-it')
                        <li class="no-bullet">
                            <a href="index.html" class="menu-link">
                                <div class="text-truncate" data-i18n="Analytics">Payment Status</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        @php
                $msg_unread = DB::table('message_reads')
                    ->join('messages', 'message_reads.message_id', '=', 'messages.id')
                    ->where('message_reads.user_id', auth()->user()->id)
                    ->whereNull('message_reads.read_at')
                    ->select('messages.chat_group_id', DB::raw('count(*) as unread_count'))
                    ->groupBy('messages.chat_group_id')
                    ->pluck('unread_count', 'messages.chat_group_id');
                $count_group = 0;
                foreach ($msg_unread as $row) {
                    $count_group++;
                }
            @endphp

        <li class="menu-item {{ request()->routeIs(
                'inbox',
                'chat-groups'
            ) ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 34 27">
                        <path
                            d="M2.7,2.7h28.6c1.1,0,2,0.9,2,2v16.6c0,1.1-0.9,2-2,2H19.5l-5.6,4.2c-0.3,0.2-0.7,0.2-1,0l-5.6-4.2H2.7c-1.1,0-2-0.9-2-2V4.7c0-1.1,0.9-2,2-2z"
                            stroke="#fff" stroke-width="1.4" fill="none" />
                    </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards" title="Staff Information Hub">Chat </div>
                    <span class="badge bg-danger rounded-pill">0</span>
                </a>
                <ul class="menu-sub">
                    <li class="no-bullet">
                            <a href="{{ route('inbox')}}" class="menu-link {{ request()->routeIs(
                                        'inbox'
                                    ) ? 'active open' : '' }}">
                                <div class="text-truncate" data-i18n="Analytics">Individual Chat </div>
                                <span class="badge bg-danger rounded-pill">0</span>
                            </a>
                    </li>  
                    <li class="no-bullet">
                            <a href="{{ route('chat-groups') }}" class="menu-link {{ request()->routeIs(
                                        'chat-groups'
                                    ) ? 'active open' : '' }}">
                                <div class="text-truncate" data-i18n="Analytics">Group Chat </div>
                               @if($count_group > 0)
                                <span class="badge bg-danger">{{ $count_group }}</span>
                            @endif
                            </a>
                    </li>                   
                </ul>
        </li>
        

       @if(auth()->user()->can('view-role') || auth()->user()->can('view-user') || auth()->user()->can('view-permission'))
                <li class="menu-item {{ request()->routeIs(
                'v1.users',
                'v1.users.create',
                'v1.users.edit',
                'v1.roles',
                'v1.roles.create',
                'v1.roles.edit',
                'v1.permissions'
            ) ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon">
                            <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 288 288">
                                <defs>
                                    <style>
                                        .cls-project {
                                            fill: #fff;
                                        }
                                    </style>
                                </defs>
                                <path class="cls-project"
                                    d="M46.45,185.81h46.45c7.68,0,13.94-6.25,13.94-13.94s-6.25-13.94-13.94-13.94h-46.45c-7.68,0-13.94,6.25-13.94,13.94s6.25,13.94,13.94,13.94ZM46.45,167.23h46.45c2.56,0,4.65,2.08,4.65,4.65s-2.08,4.65-4.65,4.65h-46.45c-2.56,0-4.65-2.08-4.65-4.65s2.08-4.65,4.65-4.65Z" />
                                <path class="cls-project"
                                    d="M274.06,102.19c7.68,0,13.94-6.25,13.94-13.94V13.94c0-7.68-6.25-13.94-13.94-13.94h-32.52c-4.37,0-8.53,2.09-11.14,5.57l-9.76,13.01h-39.48c-7.68,0-13.94,6.25-13.94,13.94v18.58h-42.04C122.81,22.53,98.85,0,69.68,0,38.94,0,13.94,25,13.94,55.74s22.53,53.13,51.1,55.51v28.21C27.66,140.51,0,154.07,0,171.87v88.26c0,18.1,35.9,27.87,69.68,27.87s69.68-9.77,69.68-27.87v-27.87h28.11c2.39,31.13,28.42,55.74,60.15,55.74,33.3,0,60.39-27.09,60.39-60.39s-24.61-57.76-55.74-60.15v-65.27h41.81ZM274.06,92.9h-92.9c-2.56,0-4.65-2.09-4.65-4.65v-41.81h102.19v41.81c0,2.56-2.08,4.65-4.65,4.65ZM181.16,27.87h44.13l12.55-16.73c.87-1.16,2.26-1.85,3.71-1.85h32.52c2.56,0,4.65,2.09,4.65,4.65v23.23h-102.19v-4.65c0-2.56,2.08-4.65,4.65-4.65ZM23.23,55.74c0-4.85.76-9.53,2.14-13.94h17.84c-.86,4.34-1.4,8.97-1.4,13.94s.54,9.6,1.41,13.94h-17.85c-1.38-4.4-2.14-9.08-2.14-13.94ZM51.1,55.74c0-5.01.64-9.64,1.64-13.94h33.89c.99,4.29,1.63,8.92,1.63,13.94s-.64,9.64-1.64,13.94h-33.89c-.99-4.29-1.63-8.92-1.63-13.94ZM69.69,11.01c3.61,3.67,9.77,11,14.02,21.5h-28.03c4.25-10.48,10.41-17.82,14.01-21.5ZM83.68,78.97c-4.25,10.48-10.41,17.82-14.01,21.5-3.61-3.67-9.77-11-14.02-21.5h28.03ZM93.68,78.97h16.17c-6.05,10.41-16,18.25-27.86,21.52,4.06-5.24,8.57-12.44,11.7-21.52h0ZM116.13,55.74c0,4.85-.75,9.53-2.14,13.94h-17.84c.86-4.34,1.4-8.97,1.4-13.94s-.54-9.6-1.41-13.94h17.84c1.39,4.4,2.15,9.08,2.15,13.94ZM109.84,32.52h-16.17c-3.12-9.08-7.63-16.28-11.7-21.52,11.86,3.27,21.81,11.11,27.86,21.52ZM57.37,11c-4.06,5.24-8.57,12.44-11.7,21.52h-16.17c6.05-10.41,16-18.25,27.86-21.52ZM29.51,78.97h16.17c3.12,9.08,7.63,16.28,11.7,21.52-11.86-3.27-21.81-11.11-27.86-21.52ZM69.68,148.65c35.59,0,60.39,12.24,60.39,23.23s-24.8,23.23-60.39,23.23-60.39-12.24-60.39-23.23,24.8-23.23,60.39-23.23ZM9.29,188.51c11.82,9.63,33.9,15.87,60.39,15.87s48.57-6.24,60.39-15.87v15.87c0,7.58-23.52,18.58-60.39,18.58s-60.39-11-60.39-18.58v-15.87ZM9.29,218.82c12.85,8.8,37.12,13.44,60.39,13.44s47.53-4.64,60.39-13.44v13.44c0,7.58-23.52,18.58-60.39,18.58s-60.39-11-60.39-18.58v-13.44ZM69.68,278.71c-36.86,0-60.39-11-60.39-18.58v-13.44c12.85,8.8,37.12,13.44,60.39,13.44s47.53-4.64,60.39-13.44v13.44c0,7.58-23.52,18.58-60.39,18.58ZM192.08,264.25c4.25-13.46,16.56-22.71,30.89-22.71h9.29c14.34,0,26.65,9.25,30.89,22.71-9.21,8.93-21.73,14.46-35.54,14.46s-26.33-5.53-35.54-14.46ZM227.61,232.26c-10.25,0-18.58-8.33-18.58-18.58s8.33-18.58,18.58-18.58,18.58,8.33,18.58,18.58-8.33,18.58-18.58,18.58ZM278.71,227.61c0,10.54-3.21,20.35-8.71,28.5-4.81-10.19-13.47-17.92-23.99-21.55,5.79-5.11,9.47-12.57,9.47-20.88,0-15.37-12.5-27.87-27.87-27.87s-27.87,12.5-27.87,27.87c0,8.31,3.68,15.77,9.48,20.88-10.52,3.63-19.18,11.36-23.99,21.55-5.5-8.15-8.71-17.95-8.71-28.5,0-28.17,22.92-51.1,51.1-51.1s51.1,22.92,51.1,51.1ZM222.97,167.46c-29.59,2.27-53.24,25.92-55.51,55.51h-28.11v-51.1c0-17.8-27.66-31.36-65.03-32.41v-28.21c27.03-2.25,48.62-23.83,50.86-50.86h42.04v27.87c0,7.68,6.25,13.94,13.94,13.94h41.81v65.27Z" />
                                <path class="cls-project" d="M260.13,18.58h9.29v9.29h-9.29v-9.29Z" />
                                <path class="cls-project" d="M241.55,18.58h9.29v9.29h-9.29v-9.29Z" />
                                <path class="cls-project" d="M185.81,74.32h9.29v9.29h-9.29v-9.29Z" />
                                <path class="cls-project" d="M204.39,74.32h9.29v9.29h-9.29v-9.29Z" />
                                <path class="cls-project" d="M222.97,74.32h9.29v9.29h-9.29v-9.29Z" />
                                <path class="cls-project" d="M74.32,51.1h9.29v9.29h-9.29v-9.29Z" />
                                <path class="cls-project" d="M55.74,51.1h9.29v9.29h-9.29v-9.29Z" />
                            </svg>
                        </i>
                        <div class="text-truncate" data-i18n="Dashboards" title="Staff Information Hub">Master Data</div>
                    </a>
                    @can('view-user')
                            <ul class="menu-sub">
                                <li class="no-bullet">
                                    <a href="{{ route('v1.users')}}" class="menu-link {{ request()->routeIs(
                            'v1.users',
                            'v1.users.create',
                            'v1.users.edit'
                        ) ? ' active' : '' }}">
                                        <div class="text-truncate" data-i18n="Analytics">User Management</div>
                                    </a>
                                </li>
                            </ul>
                    @endcan
                    @can('view-role')
                        <ul class="menu-sub">
                            <li class="no-bullet">
                                <a href="{{ route('v1.roles')}}" class="menu-link">
                                    <div class="text-truncate" data-i18n="Analytics">User Role</div>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    @can('view-permission')
                        <ul class="menu-sub">
                            <li class="no-bullet">
                                <a href="{{ route('v1.permissions')}}" class="menu-link">
                                    <div class="text-truncate" data-i18n="Analytics">Permission</div>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    @can('view-department')
                        <ul class="menu-sub">
                            <li class="no-bullet">
                                <a href="{{ route('v1.departments')}}" class="menu-link">
                                    <div class="text-truncate" data-i18n="Analytics">Department</div>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    @can('view-department')
                        <ul class="menu-sub">
                            <li class="no-bullet">
                                <a href="{{ route('v1.position-levels')}}" class="menu-link">
                                    <div class="text-truncate" data-i18n="Analytics">Position Level</div>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    @can('view-department')
                        <ul class="menu-sub">
                            <li class="no-bullet">
                                <a href="{{ route('v1.vehicles.list')}}" class="menu-link">
                                    <div class="text-truncate" data-i18n="Analytics">Vehicle</div>
                                </a>
                            </li>
                        </ul>
                    @endcan
                    @can('view-configuration')
                        <ul class="menu-sub">
                            <li class="no-bullet">
                                <a href="{{ route('v1.configuration')}}" class="menu-link">
                                    <div class="text-truncate" data-i18n="Analytics">Configuration</div>
                                </a>
                            </li>
                        </ul>
                    @endcan
                </li>
        @endif

    </ul>
</aside>
<!-- / Menu -->

<script>
    let userIdMenu = "{{ auth()->user()->name }}";
    let recipientIdMenu = "";
    let socketMenu;

    function updateChatBadge() {
        fetch(`{{ env('CHAT_URL') }}/unread-count/${userIdMenu}`)
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.menu-link .badge');
                const baseTitle = "Vibtech Genesis Staff Portal"; // 👈 Replace with your default page title

                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = "inline-block";
                    document.title = `(${data.count}) ${baseTitle}`;
                } else {
                    badge.style.display = "none";
                    document.title = baseTitle;
                }
            })
            .catch(err => console.error("Error fetching unread count:", err));
    }


    // Function to start WebSocket connection
    function startWebSocketMenu() {
        socketMenu = new WebSocket(`{{ env('WEBSOCKET_CHAT_URL') }}/ws1/${userIdMenu}`);

        socketMenu.onopen = function () {
            console.log("Connected to WebSocket server");
            updateChatBadge();
        };

        socketMenu.onmessage = function (event) {
            const messageData = JSON.parse(event.data);

            if (messageData.type === "chat") {
                updateChatBadge();
            } else if (messageData.type === "online_users") {
                //updateOnlineUsersListMenu(messageData.users);
                //updateOfflineUsersListMenu(messageData.user_offline);
            }
        };

        socketMenu.onclose = function () {
            console.log("WebSocket connection closed");
        };
    }

    // Function to update online users list
    function updateOnlineUsersListMenu(users) {
        const userList = document.getElementById("online-users-list");
        userList.innerHTML = "";
        let userLogin = "{{ auth()->user()->name }}";

        users.forEach(user => {
            if (user != userLogin) {
                const li = document.createElement("li");
                li.textContent = user;
                li.classList.add("user");
                li.onclick = () => {
                    document.getElementById("recipient").value = user;
                    recipientIdMenu = user;
                    loadPreviousMessages();

                    let recipientLabel = document.getElementById("recipientLabel");
                    recipientLabel.textContent = user;
                };
                userList.appendChild(li);
            }
        });
    }

    function updateOfflineUsersListMenu(users) {
        const userList = document.getElementById("offline-users-list");
        userList.innerHTML = "";
        let userLogin = "{{ auth()->user()->name }}";

        users.forEach(user => {
            if (user != userLogin) {
                const li = document.createElement("li");
                li.textContent = user;
                li.classList.add("user");
                li.onclick = () => {
                    document.getElementById("recipient").value = user;
                    recipientId = user;
                    loadPreviousMessages();

                    let recipientLabel = document.getElementById("recipientLabel");
                    recipientLabel.textContent = user;
                };
                userList.appendChild(li);
            }
        });
    }

    // Start WebSocket when the page loads
    startWebSocketMenu();
</script>
