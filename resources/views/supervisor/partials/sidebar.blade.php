<style>
  .sidebar .nav .sub-menu .nav-item:hover{
    background: transparent !important;
  }
  .sidebar .nav .sub-menu .nav-item:hover .nav-link{
    background: transparent !important;
  }
</style>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a  class="nav-link">
        <div class="profile-image">
          <div class="dot-indicator bg-success"></div>
        </div>
        <div class="text-wrapper">
          <p class="profile-name">{{ Auth::user()->name }}</p>
          <p class="designation">Supervisor</p>
        </div>
      </a>
    </li>
    <li class="nav-item nav-category">Main Menu</li>
    <li class="nav-item">
      <a class="nav-link" href="{{ url('supervisor-dashboard') }}">
        <i class="menu-icon typcn typcn-document-text"></i>
        <span class="menu-title">
          Dashboard
        </span>
      </a>
    </li>
    <li class="nav-item {{ request()->is('all/suser/view') || request()->is('all/suser/sign_signout/view') || request()->is('suser/payroll/report') || request()->is('all/suser/payperiod/search') ? 'active' : '' }}">
      <a class="nav-link"
        data-toggle="collapse"
        href="#ui-areports"
        aria-expanded="{{ request()->is('all/suser/view') || request()->is('all/suser/sign_signout/view') || request()->is('suser/payroll/report') || request()->is('all/suser/payperiod/search') ? 'true' : 'false' }}">
      <i class="menu-icon typcn typcn-coffee"></i>
      <span class="menu-title">Supervisor Reports</span>
      <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ request()->is('all/suser/view') || request()->is('all/suser/sign_signout/view') || request()->is('suser/payroll/report') || request()->is('all/suser/payperiod/search') ? 'show' : '' }}" id="ui-areports">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a href="{{ url('/all/suser/view') }}"
              class="nav-link {{ request()->is('all/suser/view') ? 'active-submenu' : '' }}">
              All Users
            </a>
          </li> 
          <li class="nav-item">
            <a href="{{ url('/all/suser/sign_signout/view') }}"
              class="nav-link {{ request()->is('all/suser/sign_signout/view') ? 'active-submenu' : '' }}">
              All Sign In/Sign Out Time
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ url('/suser/payroll/report') }}"
              class="nav-link {{ request()->is('suser/payroll/report') ? 'active-submenu' : '' }}">
              Payroll Report
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ url('/all/suser/payperiod/search') }}"
              class="nav-link {{ request()->is('all/suser/payperiod/search') ? 'active-submenu' : '' }}">
              Search By PayPeriod
            </a>
          </li>
        </ul>
      </div>
    </li>
    <?php
    $payperiods_dates = payperiods();
    if(isset($payperiods_dates)) {
      $frm_date  = $payperiods_dates[0]['frm_date'];
      $t_date = $payperiods_dates[0]['t_date'];
    } else {
      $frm_date  = "";
      $t_date = "";
    }
      $TodayDate = new DateTime();
      $origin = new DateTime('2020-12-21');
      $interval = $origin->diff($TodayDate);
      $date_diff =  $interval->format('%a');
      if($date_diff == 0) {
        $frm_date = "2020_12_21";
        $t_date = "2021_01_03";
      }
    ?>
    <li class="nav-item {{ request()->segment(1)=='susers' && request()->segment(2)!=='time' && request()->segment(2)!=='list-issues' ? 'active' : '' }}">
      <a class="nav-link {{ request()->segment(1)=='susers' && request()->segment(2)!=='time' && request()->segment(2)!=='list-issues' ? 'active-submenu' : '' }}"
        href="{{ url('/susers') }}/{{ $frm_date }}/{{ $t_date }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Users</span>
        <i class="menu-arrow"></i>
      </a>
    </li>
    <li class="nav-item {{ request()->is('suser/approve/vaccation/hours') ? 'active' : '' }}">
      <a class="nav-link {{ request()->is('suser/approve/vaccation/hours') ? 'active-submenu' : '' }}"
        href="{{ url('/suser/approve/vaccation/hours') }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">
          Approve Vaccations
        </span>
        <i class="menu-arrow"></i>
      </a>
    </li>
    <li class="nav-item {{ request()->is('susers/list-issues*') ? 'active' : '' }}">
      <a class="nav-link" data-toggle="collapse"
        href="#ui-company" aria-expanded="{{ request()->is('susers/list-issues*') ? 'true' : 'false' }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">
          Issues
        </span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ request()->is('susers/list-issues*') ? 'show' : '' }}" id="ui-company">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link {{ request()->is('susers/list-issues') ? 'active-submenu' : '' }}" href="{{ url('susers/list-issues') }}">
              All Issue
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->is('susers/list-issues/create') ? 'active-submenu' : '' }}"
              href="{{ url('susers/list-issues/create') }}">
              Add Issue
            </a>
          </li>
        </ul>
      </div>
    </li>
    <li class="nav-item {{ request()->segment(2)=='time' ? 'active' : '' }}">
      <a class="nav-link {{ request()->segment(2)=='time' ? 'active-submenu' : '' }}"
        href="{{ url('/susers/time') }}/{{ $frm_date }}/{{ $t_date }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">
          Timesheet Approval
        </span>
        <i class="menu-arrow"></i>
      </a>
    </li>
    <?php if(Auth::user()->id == 86) { ?>
    <li class="nav-item">
      <a class="nav-link" href="{{ url('/suser/billing/report') }}">
      <i class="menu-icon typcn typcn-coffee"></i>
      <span class="menu-title">
        Billing Report
      </span>
      <i class="menu-arrow"></i>
      </a>
    </li>
    <?php } ?>
    <li class="nav-item">
      <a class="nav-link" href="{{ url('/supervisor-logout') }}">
        <i class="menu-icon typcn typcn-coffee"></i>
        <span class="menu-title">Sign Out</span>
        <i class="menu-arrow"></i>
      </a>
    </li>
    <!--<li class="nav-item">
    <a class="nav-link" href="{{ url('/supervisor/profile/resetpassword') }}">
      <i class="menu-icon typcn typcn-coffee"></i>
      <span class="menu-title">
        Reset Password
      </span>
      <i class="menu-arrow"></i>
    </a>
    </li> -->
    <!--<li class="nav-item">
      <a class="nav-link" href="{{ url('/supervisor-logout') }}">
      <i class="menu-icon typcn typcn-coffee"></i>
      <span class="menu-title">
        Sign Out
      </span>
      <i class="menu-arrow"></i>
      </a>
    </li> -->
    <!--li style="color: yellow;" class="nav-item nav-category">Tech support  1-844-255-3487  option 1 and 1</li>
    <li style="color: yellow;" class="nav-item nav-category">Human Resources  1-844-255-3487  option 1 and 2</li>
    <li style="color: yellow;" class="nav-item nav-category">Accounts and Payroll issues 1-844-255-3487  option 1 and 3</li-->
  </ul>
</nav>