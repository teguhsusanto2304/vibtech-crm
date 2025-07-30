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
                'v1.submit-claim',
                'v1.submit-claim.create',
                'v1.submit-claim.list',
                'v1.submit-claim.all',
                'v1.submit-claim.detail'
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
                                    <div class="text-truncate" data-i18n="Analytics">Job Requisition</div>
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
                        
                        @can('view-submit-claim')
                            <li class="no-bullet">
                                <a href="{{ route('v1.submit-claim')}}" class="menu-link {{ request()->routeIs(
                'v1.submit-claim',
                'v1.submit-claim.create',
                'v1.submit-claim.list',
                'v1.submit-claim.all',
                'v1.submit-claim.detail'
            ) ? 'active open' : '' }}">
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
                <div class="text-truncate" data-i18n="Dashboards" title="Staff Information Hub">Staff Hub
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
                
            </ul>
        </li>

        
            <li class="menu-item">
                <a href="{{ route('v1.staff-resources.list') }}" class="menu-link">
                    <i class="menu-icon">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" stroke="#a5a3a3" stroke-width="2" d="M12,3 L21,7.5 L12,12 L3,7.5 L12,3 Z M16.5,10.25 L21,12.5 L12,17 L3,12.5 L7.5,10.25 L7.5,10.25 M16.5,15.25 L21,17.5 L12,22 L3,17.5 L7.5,15.25 L7.5,15.25"/>
                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards">Staff Resources</div>
                </a>
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

        @can('view-meeting-minutes')
            <li class="menu-item">
                <a href="{{ route('v1.meeting-minutes') }}" class="menu-link">
                    <i class="menu-icon">
                        <svg width="30px" height="30px" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">

                        <path d="M25,26a1,1,0,0,1-1,1H8a1,1,0,0,1-1-1V5H17V3H5V26a3,3,0,0,0,3,3H24a3,3,0,0,0,3-3V13H25Z" stroke="#a5a3a3" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />

                        <path d="M27.12,2.88a3.08,3.08,0,0,0-4.24,0L17,8.75,16,14.05,21.25,13l5.87-5.87A3,3,0,0,0,27.12,2.88Zm-6.86,8.27-1.76.35.35-1.76,3.32-3.33,1.42,1.42Zm5.45-5.44-.71.7L23.59,5l.7-.71h0a1,1,0,0,1,1.42,0A1,1,0,0,1,25.71,5.71Z" stroke="#a5a3a3" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>

                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards">Meeting Minutes</div>
                </a>
            </li>
        @endcan

        

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
