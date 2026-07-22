        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-profile">
              <a  class="nav-link">
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
			
            <li class="nav-item">
              <a class="nav-link" href="{{ url('/dashboard') }}">
                <i class="menu-icon typcn typcn-document-text"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
             <li class="nav-item">
              <a class="nav-link" href="{{ url('/smsnotifications') }}">
                <i class="menu-icon typcn typcn-document-text"></i>
                <span class="menu-title">SMS Notifications</span>
              </a>
            </li>
			<?php if(Auth::user()->id == 72 || Auth::user()->id == 50 ){ ?>
			<?php }else{ ?>
			<li class="nav-item">
              <a class="nav-link" data-toggle="collapse" href="#ui-areports" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Admin Reports</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ui-areports">
                <ul class="nav flex-column sub-menu">
                   <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/users/vaccine/report') }}">Users Vaccine Reports</a>
                  </li>
				  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/applicants/view') }}">All Applicants(name /Company/ID/department)</a>
                  </li>
				  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/new-applicants/view') }}">All new applicants</a>
                  </li>
				  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/applicants-without_id/view') }}">Applicants Without ID's</a>
                  </li>
				  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/user-lst_login_logout/view') }}">Applicants Last Login's</a>
                  </li>
				  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/inactive/employees/view') }}">Inactive employees</a>
                  </li>
				  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/users/view') }}">All Users</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/users/id/view') }}">All Users with ID</a>
                  </li>
				  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/users/sign_signout/view') }}">All Sign In/Sign Out Time</a>
                  </li>
				  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/users/all_app_timesheet/view') }}">All Approved TimeSheets</a>
                  </li>
				  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/supervisor/users/view') }}">All Supervisors Assigned Users</a>
                  </li>
				   <li class="nav-item">
                    <a class="nav-link" href="{{ url('/all/payperiod/search') }}">Search By PayPeriod</a>
                  </li>
				   <li class="nav-item">
                    <a class="nav-link" href="{{ url('/user/payroll/report') }}">Payroll report</a>
                  </li>
				  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/user/finace/report') }}">Finace report</a>
                  </li>
                </ul>
              </div>
            </li>
			<?php } ?>
			<li style="display:none" class="nav-item">
              <a class="nav-link" href="{{ url('/user/add_billrate') }}">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Add Billed rate($)</span>
				 <i class="menu-arrow"></i>
              </a>
            </li>
			<li class="nav-item">
              <a class="nav-link" data-toggle="collapse" href="#ui-payperiod" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Payperiods</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ui-payperiod">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('payperiods.index') }}">All Payperiods</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('payperiods.create') }}">Add Payperiod</a>
                  </li>
                </ul>
              </div>
            </li>
            <li  class="nav-item">
              <a class="nav-link" data-toggle="collapse" href="#ui-vaccation" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Vaccation Time</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ui-vaccation">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('vaccations.index') }}">All Vaccation</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('vaccations.create') }}">Add Vaccation</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('/approve/vaccation/hours') }}">Approve Vaccation</a>
                  </li>
                </ul>
              </div>
            </li>
			<li class="nav-item">
              <a class="nav-link" data-toggle="collapse" href="#ui-holiday" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Holidays</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ui-holiday">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('holidays.index') }}">All Holidays</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('holidays.create') }}">Add Holiday</a>
                  </li>
                </ul>
              </div>
            </li>
              <?php if(Auth::user()->admin_permissions == null && Auth::user()->admin_permissions != 1 ){ ?>
			<li class="nav-item">
              <a class="nav-link" data-toggle="collapse" href="#ui-company" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Companies</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ui-company">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('companies.index') }}">All Companies</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('companies.create') }}">Add Company</a>
                  </li>
                </ul>
              </div>
            </li>
          <?php } ?>
			<li class="nav-item">
              <a class="nav-link" data-toggle="collapse" href="#ui-house" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Houses</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ui-house">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('houses.index') }}">All Houses</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('houses.create') }}">Add House</a>
                  </li>
                </ul>
              </div>
            </li>
              <?php if(Auth::user()->admin_permissions == null && Auth::user()->admin_permissions != 1 ){ ?>
			<li class="nav-item">
              <a class="nav-link" data-toggle="collapse" href="#ui-dept" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Departments</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ui-dept">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('department.index') }}">All Department</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('department.create') }}">Add Department</a>
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
              <a class="nav-link" data-toggle="collapse" href="#ui-manager" aria-expanded="false" aria-controls="ui-basic">
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
              <a class="nav-link" data-toggle="collapse" href="#ui-notifcation" aria-expanded="false" aria-controls="ui-basic">
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
			<?php if(Auth::user()->id == 72 || Auth::user()->id == 50 ){ ?>
			<?php }else{ ?>
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