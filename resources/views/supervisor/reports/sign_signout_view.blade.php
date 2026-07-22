@extends('layouts.supervisor')
@section('content')
<?php use App\Http\Controllers\Supervisor\SupervisorController; ?>
<style>
.table td img:not(.thumb-image),
.table th img:not(.thumb-image){
   border-radius:0 !important;
}
</style>
<div class="content-wrapper">
   @if(session('success'))
   <div class="alert alert-success">
      <h4>{{ session('success') }}</h4>
   </div>
   @endif
	<div class="row">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">All Users Sign In/Sign Out</h4>
					@if($LoginLogouttime->count())
					<div style="margin-top: 10px;" align="right">
						<a href="{{ url('/all/suser/sign_signout') }}" id="export" class="btn btn-success"><i class="fa fa-calendar"></i>Export to Excel</a>
					</div>
					<br>
					@endif
					<div class="table-responsive">
						<table id="sortable-table-1" class="table table-striped dataTable">
							<thead>
								<tr>
									<th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle">Emp ID<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle">Name<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle">Type<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle">Status<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle">Date<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle">Time<i class="mdi mdi-chevron-down"></i></th>
								</tr>
							</thead>
							<tbody>
								@forelse($LoginLogouttime as $datas)
								@php
									$user = SupervisorController::user_info($datas->users_id);
										$status = '';
										$date   = '';
										$time   = '';
									if($datas->last_login_at) {
										$status = 'Log In';
										$date = date('M d, Y', strtotime($datas->last_login_at));
										$time = date('h:i a', strtotime($datas->last_login_at));
									}
									if($datas->last_logout_at) {
										$status = 'Log Out';
										$date = date('M d, Y', strtotime($datas->last_logout_at));
										$time = date('h:i a', strtotime($datas->last_logout_at));
									}
								@endphp
								@if(isset($user) && $user->role == 'user')
								<tr>
									<td>{{ $LoginLogouttime->firstItem() + $loop->index }}.</td>
									<td>{{ $user->emp_id }}</td>
									<td>{{ $user->name }}</td>
									<td>{{ ucfirst($user->role) }}</td>
									<td>{{ $status }}</td>
									<td>{{ $date }}</td>
									<td>{{ $time }}</td>
								</tr>
								@endif
								@empty
								<tr>
								<td colspan="7" class="text-center">
									Sorry, No data found!
								</td>
								</tr>
								@endforelse
							</tbody>
						</table>
					</div>
					<div class="mt-3">
						{{ $LoginLogouttime->links('pagination::bootstrap-5') }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection