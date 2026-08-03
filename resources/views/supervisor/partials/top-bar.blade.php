<?php
   use App\Http\Controllers\Supervisor\UserssController;
   use App\Models\User;
   use App\Models\UserManager;
   use App\Models\Company;
   $user = Auth::user()->id;
   		$companies = UserManager::where('musers_id', '=', $user)->get();
   		$company_id = array();
   		$users = User::where('id', '=', $user)->first();
   		$user_f_name = $users->first_name;
   		if(isset($companies)){
   			foreach($companies as $company){
   				$company_id[] = $company->users_id;
   			}
   		}
   		$users_id = UserManager::whereIn('users_id', $company_id)->get();
   		$user_idss = array();
   		if(isset($users_id)){
   			foreach($users_id as $users_ids){
   				$user_idss[] = $users_ids->musers_id;
   			}
   		}
   		$companiess = Company::orderBy('company', 'ASC')->get();
   		$data = User::with('companies')->whereIn('id', $user_idss)->where('role', '=', "user")->orderBy('name', 'ASC')->paginate(15);
   		
   //$user = User::where("role", "=", "supervisor")->orderBy('id', 'DESC')->paginate(45);
   //dd($data);die();?> 
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
		<div class="main-search" style="width: 52%;">
			<form action="{{ url('/suser/searchs') }}" method="POST" role="search">
				{{ csrf_field() }}
				<div class="input-group search" style="width: 100%;margin-left: 70px;">
				<input type="text" class="form-control" name="ssrch_users" placeholder="Search Users" list='listid'> 
				<datalist id='listid'>
					@if($data->count() != 0)
					@foreach ($data as $datas)
					<option <?php if(isset($ssrch_users)){if($ssrch_users == $datas->name ){ echo "selected"; }} ?> value="{{ $datas->name }}" >{{ $datas->name }}</option>
					@endforeach
					@endif
				</datalist>
				<span class="input-group-btn">
				<button type="submit" class="btn btn-default">
				<span class="glyphicon glyphicon-search"></span>
				</button>
				</span>
				</div>
			</form>
		</div>
		 <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown d-none d-xl-inline-block user-dropdown">
        <a class="nav-link dropdown-toggle user-nav" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
          @if(!empty(Auth::user()->avtar))
            <img src="{{ asset('public/assets/uploads/supervisors/'.Auth::user()->avtar) }}" alt="User" class="top-user-avatar">
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
              <img src="{{ Auth::user()->avtar ? asset('public/assets/uploads/supervisors/'.Auth::user()->avtar) : asset('public/assets/images/default.jpg') }}" class="user-avatar">
              {{ Auth::user()->name }}
            </p>
            <p class="font-weight-light text-muted mb-0">{{ Auth::user()->email }}</p>
          </div>
          <a href="{{ url('/supervisor/edit-profile') }}" class="dropdown-item {{ request()->is('supervisor/edit-profile') ? 'active' : '' }}">
            Edit Profile
            <span class="badge badge-pill badge-danger"></span>
            <i class="dropdown-item-icon ti-dashboard"></i>
          </a>
          <a href="{{ url('/supervisor/change-password') }}" class="dropdown-item {{ request()->is('supervisor/change-password') ? 'active' : '' }}">
            Change Password
            <i class="dropdown-item-icon ti-power-off"></i>
          </a>
          <a href="{{ url('/supervisor-logout') }}" class="dropdown-item">
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