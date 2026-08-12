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
			<div style="margin: 0px 0px 10px 0px;" align="right">
				<a href="{{ url('/all/supervisor/users') }}" id="export" class="btn btn-success">Export to Excel</a>
		    </div>
			<table id="sortable-table-1" class="table table-striped table-responsive">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Driving License<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Email<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> First Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Last Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Hours Rate($)<i class="mdi mdi-chevron-down"></i> </th>
				</tr>
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($supervisor->count() != 0)
				@foreach ($supervisor as $datas)
			<?php
				$data = AdminController::UserManager($datas->id);
			?>
			<tr>
				<td> <b>{{ $datas->name  }}</b> </td>
			</tr>
			 @if($data->count() != 0)
				@foreach ($data as $user)
					<tr>
						<td>{{ ($supervisor->currentPage() - 1) * $supervisor->perPage() + $loop->iteration }}.</td>
					  <td> {{ $user->emp_id  }} </td>
					    <td> <?php if($user->drivers_license != null){ ?>
					<img style=" margin-top: 15px; width: 152px;height: 156px;" src="{{ url('/public/assets/uploads/driving-license') }}/{{ $user->drivers_license }}">
					<?php } else{ ?>
						<p>No License Found!</p>
						
					<?php } ?> </td>
					  <td> {{ $user->email  }} </td>
					  <td> {{ $user->name  }} </td>
					   <td> {{ $user->first_name  }} </td>
					    <td> {{ $user->last_name  }} </td>
					  <td> {{ $user->dept  }} </td>
					  <td > <?php $user_companies = AdminController::user_companies($user->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
					   <td> {{ $user->hourst_rate  }} </td>
					</tr>
				@endforeach
				@endif
			  </tbody>
				@endforeach
				@endif
			  </tbody>
			</table>
            {{ $supervisor->links('pagination::bootstrap-5') }}
		  </div>
		</div>
	  </div>
	</div>
	<div class="loding"></div>
	</div>
@endsection		
