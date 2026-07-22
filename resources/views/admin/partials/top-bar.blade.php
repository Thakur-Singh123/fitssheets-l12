`<?php
use App\Http\Controllers\Admin\UserInfoController;
use App\Models\User;
$user = User::where("role", "=", "user")->orderBy('id', 'ASC')->paginate(45);
//dd($user);die();?>     
	 <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
          <a class="navbar-brand brand-logo" >
            <img src="{{ url('public/assets/images/logo-main.png') }}" alt="logo" /> </a>
          <a class="navbar-brand brand-logo-mini" >
            <img src="{{ url('public/assets/images/logo-main.png') }}" alt="logo" /> </a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center">
		<!--h2 style="font-size: 15px;">Call 1-844-255-3487</h2-->
		<div class="main-search" style="width: 52%;">
		   <form action="{{ url('/user/searchs') }}" method="POST" role="search">
               {{ csrf_field() }}
				<div class="input-group search" style="width: 100%;margin-left: 70px;">
					<input type="text" class="form-control" name="srch_users" placeholder="Search Users" list='listid'> 
							   <datalist id='listid'>
			 
									@if($user->count() != 0)
										@foreach ($user as $users)
											<option <?php if(isset($srch_users)){if($srch_users == $users->name ){ echo "selected"; }} ?> value="{{ $users->name }}" >{{ $users->name }}</option>
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
              <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                   <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->name }}</p>
                 </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                  <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->name }}</p>
                  <p class="font-weight-light text-muted mb-0">{{ Auth::user()->email }}</p>
                </div>
                <a class="dropdown-item">My Profile <span class="badge badge-pill badge-danger">1</span><i class="dropdown-item-icon ti-dashboard"></i></a>
				<?php if(Auth::user()->id == 72 || Auth::user()->id == 50 ){ ?>
			<?php }else{ ?>
				<a href="{{ url('/profile/resetpassword') }}" class="dropdown-item">Reset Password<i class="dropdown-item-icon ti-power-off"></i></a>
				<?php } ?>
                <a href="{{ url('/admin-logout') }}" class="dropdown-item">Sign Out<i class="dropdown-item-icon ti-power-off"></i></a>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>