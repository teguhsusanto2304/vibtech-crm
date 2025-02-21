<!-- Navbar -->
<nav
class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
id="layout-navbar" >
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none" >
  <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
    <i class="bx bx-menu bx-md"></i>
  </a>
</div>

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse" >
  <!-- Search -->
  <div class="navbar-nav align-items-center" >
    <div class="nav-item d-flex align-items-center">
      <i class="bx bx-search bx-md"></i>
      <input
        type="text"
        class="form-control border-0 shadow-none ps-1 ps-sm-2"
        placeholder="Search..."
        aria-label="Search..." />
    </div>
  </div>
  <!-- /Search -->

  <ul class="navbar-nav flex-row align-items-center ms-auto">
    <!-- Place this tag where you want the button to render. -->

    <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a
        class="nav-link dropdown-toggle hide-arrow p-0"
        href="javascript:void(0);"
        data-bs-toggle="dropdown">
        <i class="bx bx-bell bx-md" style="color: #fff;font-size:24px; margin-right:25px;"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      <li>
        <div class="dropdown-divider my-1"></div>
      </li>
      <li>
        <a class="dropdown-item" href="#">
          <small style="color: green;">Your claim has been approved [12:31]</small>
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="#">
          <small style="color: red;">Your claim has been rejected [07:45]</small>
        </a>
      </li>
      <li>
        <div class="dropdown-divider my-1"></div>
      </li>
      <a class="dropdown-item" href="#">
        <small style="color: orange;">Helen was published a new memo [2d 07:55]</small>
      </a>
    </ul>
  </li>

    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a
        class="nav-link dropdown-toggle hide-arrow p-0"
        href="javascript:void(0);"
        data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="{{ asset('assets/img/photos/'.auth()->user()->path_image) }}" alt class="w-px-40 h-auto rounded-circle" />
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="#">
            <div class="d-flex">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online">
                  <img src="{{ asset('assets/img/photos/'.auth()->user()->path_image) }}" alt class="w-px-40 h-auto rounded-circle" />
                </div>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                <small class="text-muted">{{ auth()->user()->position }}</small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('profile') }}">
            <i class="bx bx-user bx-md me-3"></i><span>My Profile</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="#"> <i class="bx bx-cog bx-md me-3"></i><span>Settings</span> </a>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="bx bx-power-off bx-md me-3"></i><span>Log Out</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>
</nav>

<!-- / Navbar -->
