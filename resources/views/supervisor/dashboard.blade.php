@extends('layouts.supervisor')
@section('content') 
<div class="content-wrapper">
	@if(session('success'))
	<div class="alert alert-success">
		<h4>{{ session('success') }}</h4>
	</div>
	@endif
	@if(session('error'))
	<div class="alert alert-danger">
		<h4>{{ session('error') }}</h4>
	</div>
	@endif
	<div class="row">
		<div class="col-md-6 grid-margin stretch-card average-price-card">
			<div class="card text-white" style="    background: #ff5722 !important;">
				<div class="card-body">
					<div class="d-flex justify-content-between pb-2 align-items-center">
						<h2 class="font-weight-semibold mb-0">{{ date("M d, Y", strtotime($current_date_time)) }}</h2>
						<div class="icon-holder" style="  border: #ff5722 !important; background: #ff5722 !important;">
						</div>
					</div>
					<div class="d-flex justify-content-between">
						<h5 class="font-weight-semibold mb-0">Today's</h5>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 grid-margin stretch-card average-price-card">
			<div class="card text-white" style="    background: #4caf50 !important;">
				<div class="card-body">
					<div class="d-flex justify-content-between pb-2 align-items-center">
						<h2 class="font-weight-semibold mb-0">{{ $user_count }}</h2>
						<div class="icon-holder" style="  border: #4caf50 !important; background: #4caf50 !important;">
							<i class="fa fa-users"></i>
						</div>
					</div>
					<div class="d-flex justify-content-between">
						<h5 class="font-weight-semibold mb-0">Users</h5>
					</div>
				</div>
			</div>
		</div>
		<div style="display:none" class="col-md-6 grid-margin stretch-card average-price-card">
			<div class="card text-white">
				<div class="card-body">
					<h3>"ilogstaffing Contact INFO"</h3>
					<br>
					<ul>
						<li>
							<h5>Tech support  1-844-255-3487  option 1 and 1</h5>
						</li>
						<li>
							<h5>Human Resources  1-844-255-3487  option 1 and 2</h5>
						</li>
						<li>
							<h5>Accounts and Payroll issues 1-844-255-3487  option 1 and 3</h5>
						</li>
					</ul>
					<hr style="    border-top: 1px solid #fff;">
					<ul>
						<li>
							<h5>fax:1-855-933-3487</h5>
						</li>
						<li>
							<h5>email: info@ilogstaffing.com</h5>
						</li>
						<li>
							<h5>email: humanresources@ilogstaffing.com</h5>
						</li>
						<li>
							<h5>
							email: finance@ilogstaffing.com
							<h5>
						</li>
					</ul>
					<hr style="    border-top: 1px solid #fff;">
					<ul>
						<li>
							<h5>email: humanresources@ilogstaffing.com</h5>
						</li>
						<li>
							<h5>email: accounts@ilogstaffing.com</h5>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Clock In & Clock Out ({{ date("M d, Y", strtotime($current_date_time)) }})</h4>
					<!-- <h2>Clock In & Clock Out ({{ date("M d, Y", strtotime($current_date_time)) }})</h2> -->
					<p  class="card-description">Supervisor</p>
					<!-- <h4 class="card-title">{{ Auth::user()->name }}</h4> -->
					<h4 class="user-naaam">{{ Auth::user()->name }}</h4>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
								<th class="sortStyle unsortStyle">Sr. No <i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Name <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Department <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Company <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> House <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Time In <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Time Out <i class="mdi mdi-chevron-down"></i></th>
								</tr>
							</thead>
							<tbody id="result">
								<?php $count = 1; ?>
								@if($data->count() != 0)
								@foreach ($data as $datas)
								<tr>
								<td><?php echo $count; ?>.</td>
								<td> {{ $datas->users->name  }}</td>
								<td> {{ $datas->users->dept  }} </td>
								<td> {{ $datas->companies->company  }}</td>
								<td> {{ $datas->houses->house_add  }} </td>
								<td> {{ $datas->time_in	  }} </td>
								<td> {{ $datas->time_out	  }} </td>
								</tr>
								<?php $count++; ?>
								@endforeach
								@else
								<tr>
								<td colspan="16" class="no-data">
									Sorry, No data found!
								</td>
								</tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
   <div class="loding"></div>
</div>
@endsection