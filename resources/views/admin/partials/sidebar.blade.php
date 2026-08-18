<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
        <a class="nav-link">
            <div class="profile-image">
                <div class="dot-indicator bg-success"></div>
            </div>
            <div class="text-wrapper">
                <p class="profile-name">{{ Auth::user()->name }}</p>
                <p class="designation">Admin</p>
            </div>
        </a>
    </li>
    <li class="nav-item nav-category">Main Menu</li>
    <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/dashboard') }}">
            <i class="menu-icon typcn typcn-document-text"></i>
            <span class="menu-title">Dashboard</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('smsnotifications') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/smsnotifications') }}">
            <i class="menu-icon typcn typcn-document-text"></i>
            <span class="menu-title">SMS Notifications</span>
        </a>
    </li>
      <?php if(Auth::user()->id == 72 || Auth::user()->id == 50 ) { ?> <?php } else { ?> 
      @php 
        $adminReportsActive = request()->is
        ( 'all/users/vaccine/report', 'all/applicants/view', 'all/new-applicants/view',
        'all/applicants-without_id/view', 'all/user-lst_login_logout/view', 'inactive/employees/view', 'all/users/view',
        'all/users/id/view', 'all/users/sign_signout/view', 'all/users/all_app_timesheet/view',
        'all/supervisor/users/view', 'all/payperiod/search', 'user/payroll/report', 'user/finace/report' ); 
      @endphp
      <li class="nav-item {{ $adminReportsActive ? 'active' : '' }}">
          <a
            class="nav-link"
            data-toggle="collapse"
            href="#ui-areports"
            aria-expanded="{{ $adminReportsActive ? 'true' : 'false' }}"
            aria-controls="ui-areports">
            <i class="menu-icon typcn typcn-coffee"></i>
            <span class="menu-title">Admin Reports</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse {{ $adminReportsActive ? 'show' : '' }}" id="ui-areports">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/users/vaccine/report') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/users/vaccine/report') }}">
                    Users Vaccine Reports
                  </a>
              </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/applicants/view') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/applicants/view') }}">
                    All Applicants(name /Company/ID/department)
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/new-applicants/view') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/new-applicants/view') }}">
                    All new applicants
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/applicants-without_id/view') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/applicants-without_id/view') }}">
                    Applicants Without ID's
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/user-lst_login_logout/view') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/user-lst_login_logout/view') }}">
                    Applicants Last Login's
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('inactive/employees/view') ? 'active-submenu' : '' }}"
                    href="{{ url('/inactive/employees/view') }}">
                    Inactive employees
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/users/view') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/users/view') }}">
                    All Users
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/users/id/view') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/users/id/view') }}">
                    All Users with ID
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/users/sign_signout/view') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/users/sign_signout/view') }}">
                    All Sign In/Sign Out Time
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/users/all_app_timesheet/view') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/users/all_app_timesheet/view') }}">
                    All Approved TimeSheets
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/supervisor/users/view') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/supervisor/users/view') }}">
                    All Supervisors Assigned Users
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('all/payperiod/search') ? 'active-submenu' : '' }}"
                    href="{{ url('/all/payperiod/search') }}">
                    Search By PayPeriod
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('user/payroll/report') ? 'active-submenu' : '' }}"
                    href="{{ url('/user/payroll/report') }}">
                    Payroll report
                  </a>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link {{ request()->is('user/finace/report') ? 'active-submenu' : '' }}"
                    href="{{ url('/user/finace/report') }}">
                    Finace report
                  </a>
                </li>
            </ul>
        </div>
      </li>
      <?php } ?>
      <li style="display: none" class="nav-item">
        <a class="nav-link" href="{{ url('/user/add_billrate') }}">
            <i class="menu-icon typcn typcn-coffee"></i>
            <span class="menu-title">Add Billed rate($)</span>
            <i class="menu-arrow"></i>
        </a>
      </li>
      @php 
        $payperiodActive = request()->is('payperiods') || request()->is('payperiods/create') ||
        request()->is('payperiods/*/edit'); $vaccationActive = request()->routeIs('vaccations.*') ||
        request()->is('approve/vaccation/hours'); $holidayActive = request()->routeIs('holidays.*'); $companyActive =
        request()->routeIs('companies.*'); 
      @endphp 
      <li class="nav-item {{ $payperiodActive ? 'active' : '' }}">
        <a
          class="nav-link"
          data-toggle="collapse"
          href="#ui-payperiod"
          aria-expanded="{{ $payperiodActive ? 'true' : 'false' }}"
          aria-controls="ui-payperiod">
          <i class="menu-icon typcn typcn-coffee"></i>
          <span class="menu-title">Payperiods</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ $payperiodActive ? 'show' : '' }}" id="ui-payperiod">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a
                class="nav-link {{ request()->is('payperiods/create') ? 'active-submenu' : '' }}"
                href="{{ route('payperiods.create') }}">
                Add Payperiod
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link {{ (request()->is('payperiods') || request()->is('payperiods/*/edit')) ? 'active-submenu' : '' }}"
                href="{{ route('payperiods.index') }}">
                All Payperiods
              </a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item {{ $vaccationActive ? 'active' : '' }}">
        <a
          class="nav-link"
          data-toggle="collapse"
          href="#ui-vaccation"
          aria-expanded="{{ $vaccationActive ? 'true' : 'false' }}"
          aria-controls="ui-vaccation">
          <i class="menu-icon typcn typcn-coffee"></i>
          <span class="menu-title">Vaccation Time</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ $vaccationActive ? 'show' : '' }}" id="ui-vaccation">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a
                class="nav-link {{ request()->routeIs('vaccations.create') ? 'active-submenu' : '' }}"
                href="{{ route('vaccations.create') }}">
                Add Vaccation
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link {{ request()->routeIs('vaccations.index') || request()->is('vaccations/*/edit') ? 'active-submenu' : '' }}"
                href="{{ route('vaccations.index') }}">
                All Vaccations
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link {{ request()->is('approve/vaccation/hours') ? 'active-submenu' : '' }}"
                href="{{ url('/approve/vaccation/hours') }}">
                Approve Vaccations
              </a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item {{ $holidayActive ? 'active' : '' }}">
        <a
          class="nav-link"
          data-toggle="collapse"
          href="#ui-holiday"
          aria-expanded="{{ $holidayActive ? 'true' : 'false' }}"
          aria-controls="ui-holiday">
          <i class="menu-icon typcn typcn-coffee"></i>
          <span class="menu-title">Holidays</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ $holidayActive ? 'show' : '' }}" id="ui-holiday">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a
                class="nav-link {{ request()->routeIs('holidays.create') ? 'active-submenu' : '' }}"
                href="{{ route('holidays.create') }}">
                Add Holiday
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link {{ request()->routeIs('holidays.index') || request()->is('holidays/*/edit') ? 'active-submenu' : '' }}"
                href="{{ route('holidays.index') }}">
                All Holidays
              </a>
            </li>
          </ul>
        </div>
      </li>
      <?php if(Auth::user()->admin_permissions == null && Auth::user()->admin_permissions != 1 ) { ?>
      <li class="nav-item {{ $companyActive ? 'active' : '' }}">
        <a
          class="nav-link"
          data-toggle="collapse"
          href="#ui-company"
          aria-expanded="{{ $companyActive ? 'true' : 'false' }}"
          aria-controls="ui-company">
          <i class="menu-icon typcn typcn-coffee"></i>
          <span class="menu-title">Companies</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ $companyActive ? 'show' : '' }}" id="ui-company">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a
                class="nav-link {{ request()->routeIs('companies.create') ? 'active-submenu' : '' }}"
                href="{{ route('companies.create') }}">
                Add Company
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link {{ request()->routeIs('companies.index') || request()->is('companies/*/edit') ? 'active-submenu' : '' }}"
                href="{{ route('companies.index') }}">
                All Companies
              </a>
            </li>
          </ul>
        </div>
      </li>
      <?php } ?>
      @php
        $houseActive = request()->routeIs('houses.*');
      @endphp
      <li class="nav-item {{ $houseActive ? 'active' : '' }}"> 
        <a class="nav-link" data-toggle="collapse" href="#ui-house" aria-expanded="{{ $houseActive ? 'true' : 'false' }}" aria-controls="ui-house"> 
          <i class="menu-icon typcn typcn-coffee"></i> 
            <span class="menu-title">
              Houses
            </span> 
          <i class="menu-arrow"></i> 
        </a> 
        <div class="collapse {{ $houseActive ? 'show' : '' }}" id="ui-house"> 
          <ul class="nav flex-column sub-menu"> 
            <li class="nav-item"> 
              <a class="nav-link {{ request()->routeIs('houses.create') ? 'active-submenu' : '' }}" href="{{ route('houses.create') }}">
                Add House
              </a> 
            </li> 
            <li class="nav-item"> 
              <a class="nav-link {{ request()->routeIs('houses.index') || request()->is('houses/*/edit') ? 'active-submenu' : '' }}" href="{{ route('houses.index') }}">
                All Houses
              </a> 
            </li> 
          </ul> 
        </div> 
      </li>
      <?php if(Auth::user()->admin_permissions == null && Auth::user()->admin_permissions != 1 ) { ?>
      @php
        $departmentActive = request()->routeIs('department.*');
      @endphp
      <li class="nav-item {{ $departmentActive ? 'active' : '' }}"> 
        <a class="nav-link" data-toggle="collapse" href="#ui-dept" aria-expanded="{{ $departmentActive ? 'true' : 'false' }}" aria-controls="ui-dept"> 
          <i class="menu-icon typcn typcn-coffee"></i> 
          <span class="menu-title">
            Departments
          </span> 
          <i class="menu-arrow"></i> 
        </a> 
        <div class="collapse {{ $departmentActive ? 'show' : '' }}" id="ui-dept"> 
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> 
              <a class="nav-link {{ request()->routeIs('department.create') ? 'active-submenu' : '' }}" href="{{ route('department.create') }}">
                Add Department
              </a> 
            </li> 
            <li class="nav-item"> 
              <a class="nav-link {{ request()->routeIs('department.index') || request()->is('department/*/edit') ? 'active-submenu' : '' }}" href="{{ route('department.index') }}">
                All Departments
              </a> 
            </li> 
          </ul> 
        </div> 
      </li>
      <?php } ?>
      <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
              <i class="menu-icon typcn typcn-coffee"></i>
              <span class="menu-title">Users</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="ui-basic">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                      <a class="nav-link" href="{{ route('users.index') }}">All Users</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="{{ route('users.create') }}">Add User</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="{{URL('/timesheetapproval')}}"> Timesheet Approval</a>
                  </li>
              </ul>
          </div>
      </li>
      <?php if(Auth::user()->admin_permissions == null && Auth::user()->admin_permissions != 1 ){ ?>
      <li class="nav-item">
          <a
              class="nav-link"
              data-toggle="collapse"
              href="#ui-manager"
              aria-expanded="false"
              aria-controls="ui-basic"
          >
              <i class="menu-icon typcn typcn-coffee"></i>
              <span class="menu-title">Case Managers</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="ui-manager">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                      <a class="nav-link" href="{{ route('casemanagers.index') }}">All Case Managers</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="{{ route('users.create') }}">Add Case Manager</a>
                  </li>
              </ul>
          </div>
      </li>
      <?php } ?>
      <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#ui-susers" aria-expanded="false" aria-controls="ui-basic">
              <i class="menu-icon typcn typcn-coffee"></i>
              <span class="menu-title">Supervisors</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="ui-susers">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                      <a class="nav-link" href="{{ route('supervisors.index') }}">All Supervisors</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="{{ route('users.create') }}?u=supervisor">Add Supervisor</a>
                  </li>
              </ul>
          </div>
      </li>
      <li class="nav-item">
          <a
              class="nav-link"
              data-toggle="collapse"
              href="#ui-notifcation"
              aria-expanded="false"
              aria-controls="ui-basic"
          >
              <i class="menu-icon typcn typcn-coffee"></i>
              <span class="menu-title">Notitfcation Employees</span>
              <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="ui-notifcation">
              <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                      <a class="nav-link" href="{{ route('notifications.index') }}">All Notitfcations</a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="{{ route('notifications.create') }}">Add Notitfcation</a>
                  </li>
              </ul>
          </div>
      </li>

      <li class="nav-item">
          <a class="nav-link" href="{{ url('/lists-issue') }}">
              <i class="menu-icon typcn typcn-coffee"></i>
              <span class="menu-title">List Of Issue</span>
              <i class="menu-arrow"></i>
          </a>
      </li>
      <?php if(Auth::user()->id == 72 || Auth::user()->id == 50 ){ ?> <?php }else{ ?>
      <li class="nav-item">
          <a class="nav-link" href="{{ url('/profile/resetpassword') }}">
              <i class="menu-icon typcn typcn-coffee"></i>
              <span class="menu-title">Reset Password</span>
              <i class="menu-arrow"></i>
          </a>
      </li>
      <?php } ?>
      <li class="nav-item">
          <a class="nav-link" href="{{ url('/admin-logout') }}">
              <i class="menu-icon typcn typcn-coffee"></i>
              <span class="menu-title">Sign Out</span>
              <i class="menu-arrow"></i>
          </a>
      </li>
      <!--li style="color: #3aff3a;" class="nav-item nav-category">Tech support  1-844-255-3487  option 1 and 1</li>
    <li style="color: #3aff3a;" class="nav-item nav-category">Human Resources  1-844-255-3487  option 1 and 2</li>
    <li style="color: #3aff3a;" class="nav-item nav-category">Accounts and Payroll issues 1-844-255-3487  option 1 and 3</li-->
  </ul>
</nav>
