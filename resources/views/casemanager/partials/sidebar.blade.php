        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-profile">
              <a  class="nav-link">
                <div class="profile-image">
                 
                  <div class="dot-indicator bg-success"></div>
                </div>
                <div class="text-wrapper">
                  <p class="profile-name">{{ Auth::user()->name }}</p>
                  <p class="designation">Case Manager</p>
                </div>
              </a>
            </li>
            <li class="nav-item nav-category">Main Menu</li>
            <li class="nav-item">
              <a class="nav-link" href="{{ url('manager-dashboard') }}">
                <i class="menu-icon typcn typcn-document-text"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
                  
			<li class="nav-item">
              <a class="nav-link" href="{{ route('cmusers.index') }}">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Users</span>
				 <i class="menu-arrow"></i>
              </a>
            </li>
			 <li class="nav-item">
              <a class="nav-link" href="{{ url('/casemanager/profile/resetpassword') }}">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Reset Password</span>
				 <i class="menu-arrow"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ url('/casemanager-logout') }}">
                <i class="menu-icon typcn typcn-coffee"></i>
                <span class="menu-title">Sign Out</span>
				 <i class="menu-arrow"></i>
              </a>
            </li>
			
          </ul>
        </nav>