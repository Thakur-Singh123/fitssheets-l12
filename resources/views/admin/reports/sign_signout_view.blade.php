@extends('layouts.master')
@section('content')
<?php use App\Http\Controllers\Admin\AdminController; ?>
<style>
.table td img:not(.thumb-image), .table th img:not(.thumb-image) {
    border-radius: none !important;
}
</style>
<div class="content-wrapper">
	@if(\Session::has('success'))
	<div class="alert alert-success">
		<h4>{{\Session::get('success')}}</h4>
	</div>
	@endif
	<div class="row">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">All Users Sign In/Sign Out</h4>
					@if($LoginLogouttime->count() != 0)
					<div style="margin: 0px 0px 10px 0px;" align="right">
						<a href="{{ url('/all/users/sign_signout') }}" id="export" class="btn btn-success">Export to Excel</a>
					</div>
					@endif
					<div class="table-responsive">
						<table id="sortable-table-1" class="table table-stripe">
							<thead>
								<tr>
									<th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle"> Type<i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle"> Status<i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle"> Date<i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle"> Time<i class="mdi mdi-chevron-down"></i> </th>
								</tr>
							</thead>
							<tbody id="result">
							<?php $count = 1; ?>
							@if($LoginLogouttime->count() != 0)
								@foreach ($LoginLogouttime as $datas)
							<?php
								$user = AdminController::user_info($datas->users_id);
								if($datas->last_login_at != null) {
									$status = "Log In";
									$date = date('M d, Y', strtotime($datas->last_login_at ));
									$time = date('h:i a', strtotime($datas->last_login_at));
								}
								if($datas->last_logout_at != null){
									$status = "Log Out";
									$date = date('M d, Y', strtotime($datas->last_logout_at));
									$time = date('h:i a', strtotime($datas->last_logout_at));
								}
							?>
							<?php if(isset($user)){ ?>
								<tr>
								<td>{{ ($LoginLogouttime->currentPage() - 1) * $LoginLogouttime->perPage() + $loop->iteration }}.</td>
								<td> {{ $user->emp_id  }} </td>
								<td> {{ $user->name  }} </td>
								<td> {{ $user->role  }} </td>
								<td> {{  $status  }} </td>
								<td> {{  $date  }} </td>
								<td> {{  $time  }} </td>
								</tr>
								<?php }	?>
								<?php $count++; ?>
								@endforeach
								@else
								<tr>
								<td colspan="7" class="no-data">
									Sorry, No data found!
								</td>
								</tr>
								@endif
							</tbody>
						</table>
						{{ $LoginLogouttime->links('pagination::bootstrap-5') }}
					</div>
				</div>
			</div>
		</div>
	</div>
    <div class="loding"></div>
</div>
@endsection		
