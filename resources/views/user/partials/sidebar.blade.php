<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a  class="nav-link">
        <div class="profile-image">
          <div class="dot-indicator bg-success"></div>
        </div>
        <div class="text-wrapper">
          <p class="profile-name">{{ Auth::user()->name }}</p>
          <p class="designation">Employee ID: <strong>{{ Auth::user()->emp_id }}</strong></p>
        </div>
      </a>
    </li>
    <li class="nav-item nav-category">Main Menu</li>
    <li class="nav-item">
      <a class="nav-link" href="{{ url('user-dashboard') }}">
        <i class="menu-icon typcn typcn-document-text"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" >
        <span class="menu-title">Total Paycheck<br>${{ $total_pay }}</span>
      </a>
    </li>
    <li class="nav-item">
      <a  class="nav-link" >
        <span class="menu-title">Pay Period Start/End <br>{{ $last_payperiod }}</span>
      </a>
    </li>
    <?php if(Auth::user()->status == 1) { ?>
    <li class="nav-item">
      <a class="nav-link {{ request()->is('time-sheets*') ? 'active' : '' }}"
        href="{{ route('time-sheets.index') }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Manage TimeSheets</span>
        <i class="menu-arrow"></i>
      </a>
    </li>
    <?php } else { ?>
    <li class="nav-item">
      <a class="nav-link" >
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Manage TimeSheets is<br> disable for you!</span>
        <!--<i class="menu-arrow"></i>-->
      </a>
    </li>
    <?php } ?>	
    <li class="nav-item {{ request()->routeIs('list-issue.*') ? 'active' : '' }}">
      <a class="nav-link"
        data-toggle="collapse"
        href="#ui-company"
        aria-expanded="{{ request()->routeIs('list-issue.*') ? 'true' : 'false' }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Issues</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ request()->routeIs('list-issue.*') ? 'show' : '' }}" id="ui-company">
        <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a href="{{ route('list-issue.index') }}"
                class="nav-link {{ request()->routeIs('list-issue.index') ? 'active-submenu' : '' }}">
                All Issues
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('list-issue.create') }}"
                class="nav-link {{ request()->routeIs('list-issue.create') ? 'active-submenu' : '' }}">
                Add Issue
              </a>
          </li>
        </ul>
      </div>
    </li>
    <!--<li class="nav-item">
      <a class="nav-link" href="{{ url('/my/name/edit') }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Update name</span>
        <i class="menu-arrow"></i>
      </a>
    </li> -->
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/enter-vaccation') }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Vaccations</span>
        <i class="menu-arrow"></i>
        </a>
    </li>
    <!--li class="nav-item">
      <a class="nav-link" href="{{ url('/my/driving-license/upload') }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Upload Drivers Licesne</span>
        <i class="menu-arrow"></i>
      </a>
    </li>
    <li  class="nav-item">
      <a class="nav-link" href="{{ url('/my/covid-report/upload') }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Upload Covid Report</span>
        <i class="menu-arrow"></i>
      </a>
    </li-->
    <!-- <li class="nav-item">
      <a class="nav-link" href="{{ url('/change-password') }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Reset Password</span>
        <i class="menu-arrow"></i>
      </a>
    </li> -->
    <li class="nav-item">
      <a class="nav-link" href="{{ url('/user-logout') }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Sign Out</span>
        <i class="menu-arrow"></i>
      </a>
    </li>
    <!--li style="color: rgb(255 255 255);" class="nav-item nav-category">Tech support  1-844-255-3487  option 1 and 1</li>
    <li style="color: rgb(255 255 255);" class="nav-item nav-category">Human Resources  1-844-255-3487  option 1 and 2</li>
    <li style="color: rgb(255 255 255);" class="nav-item nav-category">Accounts and Payroll issues 1-844-255-3487  option 1 and 3</li-->
  </ul>
</nav>