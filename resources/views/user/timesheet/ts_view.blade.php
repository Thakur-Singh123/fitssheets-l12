@extends('layouts.user')
@section('content')
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
					<div class="row">
						<div class="col-md-2">
							<h4 class="card-title">Time Sheets</h4>
							<p  class="card-description add_hours"><a style="color:#fff;padding: 15px;" href="{{ route('time-sheets.create') }}"> Add Hours <i class="fa fa-plus-circle"></i></a> </p>
						</div>
						<div class="col-md-2"></div>
						<div class="col-md-3"></div>
						<div class="col-md-3"></div>
						<div class="col-md-2">
							<button class="btn btn-success mr-2"><a style="color:#fff;text-decoration:none" href="{{ url('/search/timesheets') }}" >Search Timesheets</a></button>
						</div>
					</div>
					@if($data->count() != 0)
					<div align="left">
						<a href="{{ route('export.time-sheet') }}" id="export" class="btn btn-success"><i class="fa fa-calendar"></i>  Export to Excel</a>
					</div>
					@endif <br>
					<table id="sortable-table-1" class="table dataTable table-striped table-responsive">
						<thead>
							<tr>
								<th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Email<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> House <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Time In <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Time Out <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Hours Worked <i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Date <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Hours Rate<i class="mdi mdi-chevron-down"></i> </th>
								<th style="display:none" class="sortStyle unsortStyle"> Vacation<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Approved <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Remarks <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Actions <i class="mdi mdi-chevron-down"></i></th>
							</tr>
						</thead>
						<tbody id="result">
							<?php $count = 1; ?>
							@if($data->count() != 0)
							@foreach ($data as $datas)
							<tr>
								<td><?php echo $count; ?>.</td>
								<td> {{ $datas->users->emp_id  }} </td>
								<td style="display:none">
								    <img src="../../../assets/images/faces-clipart/pic-1.png" alt="image"> 
								</td>
								<td> {{ $datas->users->email  }}</td>
								<td> {{ $datas->users->name  }}</td>
								<td> {{ $datas->users->dept  }} </td>
								<td> {{ $datas->companies->company  }}</td>
								<td> {{ $datas->houses->house_add  }} </td>
								<td> {{ $datas->time_in  }} </td>
								<td> {{ $datas->time_out  }} </td>
								<td> <?php $hours    = explode('_', $datas->hours_wrk);$hours = implode(":", $hours); echo $hours;?></td>
								<td> <?php $hours_day    = explode('_', $datas->hours_day);$hours_day = implode("/", $hours_day); $hours_day = date("M d, Y(D)", strtotime($hours_day)); echo $hours_day;?> </td>
								<td> {{ $datas->users->hourst_rate  }} </td>
								<td style="display:none"> <?php if($datas->vacation_status == "0"){ echo "<h5 style='color:green'>No</h5>"; }elseif($datas->vacation_status == "1"){ echo "<h5 style='color:red'>Yes</h5>"; }?> </td>
								<td> <?php if($datas->approve == "2"){ echo "<h5 style='color:green'>Yes</h5>"; }elseif($datas->approve == "1"){ echo "<h5 style='color:red'>No</h5>"; }else{ echo "<h5 style='color:#a5a548'>Pending</h5>"; } ?></td>
								<td> {{ $datas->remarks  }} </td>
								<td>
									<a  href="{{ route('time-sheets.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
									<a style="margin-left: 10px;" class="delete_timesheet_record" data-timesheet_id ="{{ $datas->id }}" title="Delete"><i class="fa fa-trash-o"></i></a>
								</td>
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
					<br>
					{{ $data->links('pagination::bootstrap-5') }}
				</div>
			</div>
		</div>
    </div>
   <div class="loding"></div>
</div>
@endsection