<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
    <a class="navbar-brand brand-logo" >
      <img src="{{ asset('public/assets/images/logo-main.png') }}" alt="logo" /> 
    </a>
    <a class="navbar-brand brand-logo-mini" >
      <img src="{{ asset('public/assets/images/logo-main.png') }}" alt="logo" /> 
    </a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center">
    <!--h2 style="font-size: 25px;">Call 1-844-255-3487</h2-->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown d-none d-xl-inline-block user-dropdown">
        <a class="nav-link dropdown-toggle user-nav" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
          @if(!empty(Auth::user()->avtar))
            <img src="{{ asset('public/assets/uploads/users/'.Auth::user()->avtar) }}" alt="User" class="top-user-avatar">
          @else
            <img src="{{ asset('public/assets/images/default.jpg') }}" alt="Default" class="top-user-avatar">
          @endif
          <p class="mb-1 font-weight-semibold user-name">
            {{ Auth::user()->name }}
          </p>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <p class="mb-1 mt-3 font-weight-semibold user-info">
              <img src="{{ Auth::user()->avtar ? asset('public/assets/uploads/users/'.Auth::user()->avtar) : asset('public/assets/images/default.jpg') }}" class="user-avatar">
              {{ Auth::user()->name }}
            </p>
            <p class="font-weight-light text-muted mb-0">{{ Auth::user()->email }}</p>
          </div>
          <a href="{{ url('/edit-profile') }}" class="dropdown-item {{ request()->is('edit-profile') ? 'active' : '' }}">
            Edit Profile
            <span class="badge badge-pill badge-danger"></span>
            <i class="dropdown-item-icon ti-dashboard"></i>
          </a>
          <a href="{{ url('/change-password') }}" class="dropdown-item {{ request()->is('change-password') ? 'active' : '' }}">
            Change Password
            <i class="dropdown-item-icon ti-power-off"></i>
          </a>
          <a href="{{ url('/user-logout') }}" class="dropdown-item">
            Sign Out
            <i class="dropdown-item-icon ti-power-off"></i>
          </a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>