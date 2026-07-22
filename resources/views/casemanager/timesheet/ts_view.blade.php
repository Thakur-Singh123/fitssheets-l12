@extends('layouts.manager')

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
			
			<form  class="form-sample">
				<input type="hidden" class="form-control" value="{{ $id }}" id="user_id" name="user_id">
			  <div class="row">
			   <div class="col-md-2">
			        <h4 class="card-title">Time Sheets({{ $name }})</h4>
					<p  style="display:none" class="card-description add_hours"><a style="color:#fff;padding: 15px;" href="{{ url('/muser/create/timesheets') }}/{{ $id }}"> Add Hours <i class="fa fa-plus-circle"></i></a> </p>
			   </div>
			   <div  class="col-md-2">
			          <h5 > Search Timesheets </h5>
			          </div>
				<div  class="col-md-3">
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">From Date</label>
					<div class="col-sm-9">
					  <input type="text" class="form-control" id="frm_dt" name="frm_dt" value="<?php if(!empty($f_d)){echo $f_d;}else{echo $arr_dates[0]['frm_date']; } ?>">
					</div>
				  </div>
				</div>
				<div  class="col-md-3">
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">To date</label>
					<div class="col-sm-9">
					  <input type="text" class="form-control" id="to_dt" name="to_dt" value="<?php if(!empty($t_d)){echo $t_d;}else{echo $arr_dates[0]['t_date']; } ?>">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				 <button data-baseURL="{{ url('/') }}" id="cmsubmit_date" type="submit" class="btn btn-success mr-2">Submit</button>
				 </div>
			  </div>
			</form>
			
			
			<table id="sortable-table-1" class="table dataTable table-striped table-responsive">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
				   <th> Actions </th>
				  <th><a  style="    color: #fff" data-baseURL="{{ url('/') }}" id="cmapprove_all" class="btn btn-success">Click<br>If<br>OK</a><br><input style="    margin-top: 10px;" type="checkbox" id="time_approve" name="time_approve" ></th>
				 <th><a  style="    color: #fff" data-baseURL="{{ url('/') }}" id="cmdecline_all" class="btn btn-success">Review</a><br><input     style="    margin-top: 10px;" type="checkbox" id="time_decline" name="time_decline" ></th>
				 <th><a  style="    color: #fff" data-baseURL="{{ url('/') }}" id="cmdelete_all" class="btn btn-success">Click<br>to<br>Delete</a><br><input     style="    margin-top: 10px;" type="checkbox" id="time_delete" name="time_delete" ></th>
				  
				  <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> House <i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Time In <i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Time Out <i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Hours Worked <i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Date <i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Hours Rate<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Vacation<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Approved <i class="mdi mdi-chevron-down"></i></th>
				</tr>
				
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
					<tr>
					  <td><?php echo $count; ?></td>
					  <td> {{ $datas->users->emp_id  }}</td>
					  <td>
						<a  href="{{ url('/cmuser/edit/timesheets/') }}/{{ $datas->id }}/<?php if(!empty($f_d)){echo $f_d;}else{echo $arr_dates[0]['frm_date']; } ?>/<?php if(!empty($t_d)){echo $t_d;}else{echo $arr_dates[0]['t_date']; } ?>" title="Edit"><i class="fa fa-pencil"></i></a>
						<a style="margin-left: 5px;" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_mts" title="Delete"><i class="fa fa-trash-o"></i></a>
					  </td>
					  <td><input type="checkbox" class="time_id"  value="<?php echo $datas->id; ?>" <?php if($datas->cmcheck_status == 2){ echo "checked"; } ?> name="time_id[]" ></td>
					  <td><input type="checkbox" class="time_idd"  value="<?php echo $datas->id; ?>" <?php if($datas->cmcheck_status == 1){ echo "checked"; } ?> name="time_idd[]" ></td>
					  <td><input type="checkbox" class="time_idde"  value="<?php echo $datas->id; ?>" name="time_idde[]" ></td>
						
						 <td> {{ $datas->users->name  }}</td>
					   <td> {{ $datas->users->dept  }} </td>
					  <td> {{ $datas->companies->company  }}</td>
					   <td> {{ $datas->houses->house_add  }} </td>
					    <td> {{ $datas->time_in  }} </td>
					   <td> {{ $datas->time_out  }} </td>
					  <td> <?php $hours    = explode('_', $datas->hours_wrk);$hours = implode(":", $hours); echo $hours;?></td>
					  <td> <?php $hours_day    = explode('_', $datas->hours_day);$hours_day = implode("/", $hours_day); $hours_day = date("M d, Y(D)", strtotime($hours_day)); echo $hours_day;?> </td>
					  <td> {{ $datas->users->hourst_rate   }} </td>
					  <td> {{ $datas->vacation_status	  }} </td>
					  <td> <?php if($datas->approve == "2"){ echo "<h5 style='color:green'>Yes</h5>"; }elseif($datas->approve == "1"){ echo "<h5 style='color:red'>No</h5>"; }else{ echo "<h5 style='color:#a5a548'>Pending</h5>"; } ?></td>
					
					</tr>
				<?php $count++; ?>
				@endforeach
				
				@else
					<p>Sorry No Data!!</p>
				@endif
			  </tbody>
			</table>

		  </div>
		</div>
	  </div>
	</div>
	<div class="loding"></div>
	</div>
@endsection		
