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
                    <a href="" class="menu-link  menu-toggle">
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
                            <li class="no-bullet">
                                <a href="{{ route('v1.leave-application') }}"
                                    class="menu-link  {{ request()->routeIs('leave-application') ? 'active' : '' }}">
                                    <div class="text-truncate" data-i18n="Analytics">Leave Application</div>
                                </a>
                            </li>
                    
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

        @if(auth()->user()->can('view-sales-forecast'))
            <li class="menu-item">
                <a href="" class="menu-link  menu-toggle">
                    <i class="menu-icon">
                        <svg width="30px" height="30px" viewBox="-0.5 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.7003 17.1099V18.22C12.7003 18.308 12.6829 18.395 12.6492 18.4763C12.6156 18.5576 12.5662 18.6316 12.504 18.6938C12.4418 18.7561 12.3679 18.8052 12.2867 18.8389C12.2054 18.8725 12.1182 18.8899 12.0302 18.8899C11.9423 18.8899 11.8551 18.8725 11.7738 18.8389C11.6925 18.8052 11.6187 18.7561 11.5565 18.6938C11.4943 18.6316 11.4449 18.5576 11.4113 18.4763C11.3776 18.395 11.3602 18.308 11.3602 18.22V17.0801C10.9165 17.0072 10.4917 16.8468 10.1106 16.6082C9.72943 16.3695 9.39958 16.0573 9.14023 15.6899C9.04577 15.57 8.99311 15.4226 8.99023 15.27C8.99148 15.1842 9.00997 15.0995 9.04459 15.021C9.0792 14.9425 9.12927 14.8718 9.19177 14.813C9.25428 14.7542 9.32794 14.7087 9.40842 14.679C9.4889 14.6492 9.57455 14.6359 9.66025 14.6399C9.74504 14.6401 9.82883 14.6582 9.90631 14.6926C9.98379 14.7271 10.0532 14.7773 10.1102 14.8401C10.4326 15.2576 10.8657 15.5763 11.3602 15.76V13.21C10.0302 12.69 9.36023 11.9099 9.36023 10.8999C9.38027 10.3592 9.5928 9.84343 9.9595 9.44556C10.3262 9.04769 10.8229 8.79397 11.3602 8.72998V7.62988C11.3602 7.5419 11.3776 7.45482 11.4113 7.37354C11.4449 7.29225 11.4943 7.21847 11.5565 7.15625C11.6187 7.09403 11.6925 7.04466 11.7738 7.01099C11.8551 6.97732 11.9423 6.95996 12.0302 6.95996C12.1182 6.95996 12.2054 6.97732 12.2867 7.01099C12.3679 7.04466 12.4418 7.09403 12.504 7.15625C12.5662 7.21847 12.6156 7.29225 12.6492 7.37354C12.6829 7.45482 12.7003 7.5419 12.7003 7.62988V8.71997C13.0724 8.77828 13.4289 8.91103 13.7485 9.11035C14.0681 9.30967 14.3442 9.57137 14.5602 9.87988C14.6555 9.99235 14.7117 10.1329 14.7202 10.28C14.7229 10.3662 14.7084 10.4519 14.6776 10.5325C14.6467 10.613 14.6002 10.6867 14.5406 10.749C14.481 10.8114 14.4096 10.8613 14.3306 10.8958C14.2516 10.9303 14.1665 10.9487 14.0802 10.95C13.99 10.9475 13.9013 10.9257 13.8202 10.886C13.7391 10.8463 13.6675 10.7897 13.6102 10.72C13.3718 10.4221 13.0575 10.1942 12.7003 10.0601V12.3101L12.9503 12.4099C14.2203 12.9099 15.0103 13.63 15.0103 14.77C14.9954 15.3808 14.7481 15.9629 14.3189 16.3977C13.8897 16.8325 13.3108 17.0871 12.7003 17.1099ZM11.3602 11.73V10.0999C11.1988 10.1584 11.0599 10.2662 10.963 10.408C10.8662 10.5497 10.8162 10.7183 10.8203 10.8899C10.8173 11.0676 10.8669 11.2424 10.963 11.3918C11.0591 11.5413 11.1973 11.6589 11.3602 11.73ZM13.5502 14.8C13.5502 14.32 13.2203 14.03 12.7003 13.8V15.8C12.9387 15.7639 13.1561 15.6427 13.3123 15.459C13.4685 15.2752 13.553 15.0412 13.5502 14.8Z" fill="#a5a3a3"/>
                        <path d="M18 3.96997H6C4.93913 3.96997 3.92172 4.39146 3.17157 5.1416C2.42142 5.89175 2 6.9091 2 7.96997V17.97C2 19.0308 2.42142 20.0482 3.17157 20.7983C3.92172 21.5485 4.93913 21.97 6 21.97H18C19.0609 21.97 20.0783 21.5485 20.8284 20.7983C21.5786 20.0482 22 19.0308 22 17.97V7.96997C22 6.9091 21.5786 5.89175 20.8284 5.1416C20.0783 4.39146 19.0609 3.96997 18 3.96997Z" stroke="#a5a3a3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards">Sales</div>
                </a>
                <ul class="menu-sub">
                        @can('view-sales-forecast')
                            <li class="no-bullet">
                                <a href="{{ route('v1.sales-forecast') }}"
                                    class="menu-link {{ request()->routeIs('v1.job-assignment-form', 'v1.job-assignment-form.create', 'v1.job-assignment-form.list') ? 'active' : '' }}">
                                    <div class="text-truncate" data-i18n="Analytics">Sales Forecast</div>
                                </a>
                            </li>
                        @endcan
                        @can('view-sales-quotation')
                            <li class="no-bullet">
                                <a href="{{ route('leave-application') }}"
                                    class="menu-link  {{ request()->routeIs('leave-application') ? 'active' : '' }}">
                                    <div class="text-truncate" data-i18n="Analytics">Sales Quotation</div>
                                </a>
                            </li>
                        @endcan
                </ul>
            </li>
        @endif

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
        @can('view-inventory-management')
            <li class="menu-item">
                <a href="{{ route('v1.inventory-management') }}" class="menu-link">
                    <i class="menu-icon">
                        <svg width="35px" height="40px" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M24.3162 15.0513C24.111 14.9829 23.8891 14.9829 23.6838 15.0513L8.86851 19.9889C8.64603 20.063 8.463 20.2102 8.34247 20.3985L4.39805 25.4613C4.1985 25.7175 4.13573 26.0545 4.2297 26.3653C4.32367 26.6761 4.56269 26.922 4.87072 27.0246L8.19325 28.1319L8.19595 36.7634C8.19636 38.0544 9.02257 39.2003 10.2473 39.6085L23.6291 44.0691C23.7475 44.1164 23.8738 44.1406 24.0009 44.1405C24.1293 44.141 24.2569 44.1168 24.3765 44.069L37.7577 39.6086C38.9827 39.2003 39.8089 38.054 39.809 36.7628L39.8096 28.1328L43.1346 27.0246C43.4427 26.922 43.6817 26.6761 43.7757 26.3653C43.8696 26.0545 43.8069 25.7175 43.6073 25.4613L39.6117 20.3327C39.4927 20.176 39.3274 20.0542 39.1315 19.9889L24.3162 15.0513ZM9.54341 22.1112L22.346 26.378L19.6478 29.8413L6.8452 25.5745L9.54341 22.1112ZM24.0025 24.8203L35.6526 20.9376L24 17.0541L12.35 20.9367L24.0025 24.8203ZM10.196 36.7628L10.1935 28.7986L19.686 31.9622C20.088 32.0962 20.5307 31.9623 20.7911 31.6281L23.0003 28.7924L23.0001 41.7513L10.8797 37.7112C10.4715 37.5751 10.1961 37.1931 10.196 36.7628ZM37.8095 28.7993L28.3193 31.9622C27.9174 32.0962 27.4747 31.9623 27.2143 31.6281L25.0013 28.7876L25.0049 41.7514L37.1252 37.7113C37.5336 37.5752 37.809 37.1931 37.809 36.7627L37.8095 28.7993ZM28.3576 29.8413L25.6583 26.3767L38.4609 22.1099L41.1602 25.5745L28.3576 29.8413Z" fill="#a5a3a3"/>
                        </svg>
                    </i>
                    <div class="text-truncate" data-i18n="Dashboards">Inventory</div>
                </a>
            </li>
        @endcan

        @can('view-meeting-minutes')
            <li class="menu-item">
                <a href="{{ route('v1.meeting-minutes') }}" class="menu-link">
                    <i class="menu-icon">
                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M19.186 2.09c.521.25 1.136.612 1.625 1.101.49.49.852 1.104 1.1 1.625.313.654.11 1.408-.401 1.92l-7.214 7.213c-.31.31-.688.541-1.105.675l-4.222 1.353a.75.75 0 0 1-.943-.944l1.353-4.221a2.75 2.75 0 0 1 .674-1.105l7.214-7.214c.512-.512 1.266-.714 1.92-.402zm.211 2.516a3.608 3.608 0 0 0-.828-.586l-6.994 6.994a1.002 1.002 0 0 0-.178.241L9.9 14.102l2.846-1.496c.09-.047.171-.107.242-.178l6.994-6.994a3.61 3.61 0 0 0-.586-.828zM4.999 5.5A.5.5 0 0 1 5.47 5l5.53.005a1 1 0 0 0 0-2L5.5 3A2.5 2.5 0 0 0 3 5.5v12.577c0 .76.082 1.185.319 1.627.224.419.558.754.977.978.442.236.866.318 1.627.318h12.154c.76 0 1.185-.082 1.627-.318.42-.224.754-.559.978-.978.236-.442.318-.866.318-1.627V13a1 1 0 1 0-2 0v5.077c0 .459-.021.571-.082.684a.364.364 0 0 1-.157.157c-.113.06-.225.082-.684.082H5.923c-.459 0-.57-.022-.684-.082a.363.363 0 0 1-.157-.157c-.06-.113-.082-.225-.082-.684V5.5z" fill="#a5a3a3"/></svg>
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
