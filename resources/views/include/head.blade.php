<!doctype html>

<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="asset/"
  data-template="vertical-menu-template-free"
  data-style="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', 'Vibtech CRM')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/favicon/fav.svg') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js') }} in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <style>
        .bx-search {
            color: white; /* Change this to your preferred color */
        }
        .bx-menu {
          color: white;
        }
        .text-truncate {
          color: #a5a5a5cc;
        }
      </style>
  </head>
  <script>
    (function () {
        let idleTime = 0;
        const maxIdleMinutes = 10; // 30 minutes
        const logoutUrl = "{{ route('logout') }}";

        // Reset idle timer on activity
        function resetIdleTimer() {
            idleTime = 0;
        }

        // Detect user activity
        ['mousemove', 'keydown', 'scroll', 'click'].forEach(evt =>
            window.addEventListener(evt, resetIdleTimer)
        );

        // Start interval timer to track idle time
        setInterval(() => {
            idleTime++;

            if (idleTime >= maxIdleMinutes) {
                // Auto logout after 30 minutes of no activity
                document.getElementById('autoLogoutForm').submit();
            }
        }, 60000); // check every minute
    })();
</script>

<form id="autoLogoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

