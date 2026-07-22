@extends('layouts.supervisor')
@section('content')
<?php use App\Http\Controllers\Supervisor\SupervisorController; ?>
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
					<h4 class="card-title">All Users</h4>
					@if($users->count() != 0)
					<div style="margin-top: 10px;" align="right">
						<a href="{{ url('/all/suser/') }}" id="export" class="btn btn-success"><i class="fa fa-calendar"></i>Export to Excel</a>
					</div><br>
					@endif
					<table id="sortable-table-1" class="table dataTable table-striped table-responsive">
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
							@if($users->count() != 0)
							@foreach ($users as $datas)
							<tr>
								<td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}.</td>
								<td> {{ $datas->emp_id  }} </td>
								<td>
								<?php if($datas->drivers_license != null) { ?>
								<img style=" margin-top: 15px; width: 152px;height: 156px;" src="{{ url('/public/assets/uploads/driving-license') }}/{{ $datas->drivers_license }}">
								<?php } else { ?>
								<p class="no-data">No License Found!</p>
								<?php } ?> 
								</td>
								<td> {{ $datas->email  }} </td>
								<td> {{ $datas->name  }} </td>
								<td> {{ $datas->first_name  }} </td>
								<td> {{ $datas->last_name  }} </td>
								<td> {{ $datas->dept  }} </td>
								<td > <?php $user_companies = SupervisorController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
								<td> {{ $datas->hourst_rate  }} </td>
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
					{{ $users->links('pagination::bootstrap-5') }}
				</div>
			</div>
		</div>
	</div>
   <div class="loding"></div>
</div>
@endsection