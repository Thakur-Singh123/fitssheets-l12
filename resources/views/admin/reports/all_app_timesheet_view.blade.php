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
			<h4 class="card-title">All Users Approve timesheet</h4>
						 <form>
			@csrf
			  <div class="row">
				<div class="col-md-2">
				  <div class="form-group row">
					<div class="col-sm-12">
					  <input type="text" data-baseURL="{{ url('/') }}" class="form-control" id="frm_dt" name="frm_dt" placeholder="From">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				  <div class="form-group row">
					<label class="col-sm-2 col-form-label"><b>-</b></label>
					<div class="col-sm-10">
					  <input type="text" data-baseURL="{{ url('/') }}" class="form-control" id="to_dt" name="to_dt" placeholder="To">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				 <button style="width:172px;"  data-baseURL="{{ url('/') }}" id="all_user_app" type="submit" class="btn btn-success mr-2"><i class="fa fa-search"></i>Search Payperiod</button>
				 </div>
			  </div>
			  
			</form>
			<div style="margin-top: 10px;" align="left">
				<a href="{{ url('/all/users/all_app_timesheet') }}" id="export" class="btn btn-success">Export to Excel</a>
		    </div>
			<table id="sortable-table-1" class="table table-striped table-responsive">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Email<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> House<i class="mdi mdi-chevron-down"></i> </th>
				   <th class="sortStyle unsortStyle"> Time In<i class="mdi mdi-chevron-down"></i> </th>
				    <th class="sortStyle unsortStyle"> Time Out<i class="mdi mdi-chevron-down"></i> </th>
					 <th class="sortStyle unsortStyle"> Hours Worked<i class="mdi mdi-chevron-down"></i> </th>
					 <th class="sortStyle unsortStyle"> Day<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Hours Rate($)<i class="mdi mdi-chevron-down"></i> </th>
				   <th class="sortStyle unsortStyle"> Vacation<i class="mdi mdi-chevron-down"></i> </th>
				    <th class="sortStyle unsortStyle"> Approved<i class="mdi mdi-chevron-down"></i> </th>
				</tr>
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($user->count() != 0)
				@foreach ($user as $datas)
			<?php
				$data = AdminController::timesheet_data($datas->id);
			?>
			 @if($data->count() != 0)
				@foreach ($data as $time)
			<?php
						$hours_day    = explode('_', $time->hours_day);
						 $hours_day = implode("/", $hours_day); 
						 $hours_day = date("M d, Y", strtotime($hours_day)); 
						 if($time->vacation_status == "0"){ 
							$vacation_status = "No"; 
						 }elseif($time->vacation_status == "1"){
							 $vacation_status = "Yes";
							}else{
								$vacation_status = "";
							}
								 
						if($time->approve == "2"){ 
								$approve = "Yes";
						}elseif($time->approve == "1"){
							$approve = "No";
						}else{
							$approve = "Pending"; 
						}
			?>
					<tr>
					  <td>{{  $time->users->emp_id  }}</td>
					   <td>{{  $time->users->email  }}</td>
					    <td>{{  $time->users->name  }}</td>
						 <td>{{  $time->users->dept  }}</td>
						  <td > <?php $user_companies = AdminController::user_companies($time->users->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
						   <td>{{  $time->houses->house_add  }}</td>
						    <td>{{  $time->time_in  }}</td>
							 <td>{{  $time->time_out }}</td>
							  <td>{{  $time->hours_wrk  }}</td>
							   <td>{{  $hours_day  }}</td>
							       <td>{{  $time->users->hourst_rate  }}</td>
							 <td>{{  $vacation_status }}</td>
							  <td>{{  $approve  }}</td>
					</tr>
				@endforeach
				@endif
			  </tbody>
				@endforeach
				@endif
			  </tbody>
			</table>
			<div class="pagination">
			<?php echo $user->links(); ?>
            </div>
		  </div>
		</div>
	  </div>
	</div>
	<div class="loding"></div>
	</div>
@endsection		
