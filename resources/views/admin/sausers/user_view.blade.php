@extends('layouts.master')

@section('content')
<?php use App\Http\Controllers\Admin\UserssaController; ?>
<?php use App\Http\Controllers\Admin\UserInfoController; ?>
<style>td {
    text-align: center;
}</style>
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
			<h4 class="card-title">Users</h4>
			<form class="form-sample">
			  <div class="row">
				<div class="col-md-10">
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">Search User</label>
					<div class="col-sm-9">
					  <input type="text" class="form-control" id="sasrch_user" name="sasrch_user">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				 <button data-baseURL="{{ url('/') }}" id="ssasubmit_user" type="submit" class="btn btn-success mr-2">Submit</button>
				 </div>
			  </div>
			  
			</form>
			<form class="form-sample" method="POST" action="{{ url('/user/suser/exportall/timesheet') }}">
			@csrf
			  <div class="row">
				<div class="col-md-2">
				  <div class="form-group row">
					<div class="col-sm-12">
					  <input type="text" data-baseURL="{{ url('/') }}" class="form-control" id="frm_dt" name="frm_dt" value="{{ $frm_date }}">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				  <div class="form-group row">
					<label class="col-sm-2 col-form-label"><b>-</b></label>
					<div class="col-sm-10">
					  <input type="text" data-user="{{ $user }}" data-baseURL="{{ url('/') }}" class="form-control" id="to_dt" name="to_dt" value="{{ $t_date }}">
					</div>
				  </div>
				</div>
				
				 <div class="col-md-6">
					  <div class="form-group row">
						<label  style="font-size: 13px;" class="col-sm-3 col-form-label">Sort User By Company</label>
						<div class="col-sm-9">
						  <select data-baseURL="{{ url('/') }}" id="sasearch_by_comp" class="form-control" name="sasearch_by_comp" >
							<option value="0" >Select</option>
							@if($companiess->count() != 0)
								@foreach ($companiess as $company)
									<option value="{{ $company->id }}" >{{ $company->company }}</option>
								@endforeach
							  @endif
						  </select>
						</div>
						</div>
				 </div>
				 <div class="col-md-2">
				 <button style="width:162px;" data-user="{{ $user }}" data-baseURL="{{ url('/') }}" id="sasearch_payperiod" type="submit" class="btn btn-success mr-2"><i class="fa fa-search"></i>Search Payperiod</button>
				 </div>
				 <div class="col-md-3">
				 <a href="" style="width:174px;margin-left:15px" data-user="{{ $user }}" id="saexport_all_user" class="btn btn-success"><i class="fa fa-calendar"></i> Export Timesheets</a>
				 </div>
			  </div>
			  
			</form>
			<table id="sortable-table-1" class="table table-striped table-responsive">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
				  <th> Actions </th>
				  <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
				   <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				   <th class="sortStyle unsortStyle">Time<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle">Day<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Created<i class="mdi mdi-chevron-down"></i></th>
				   <th> Status </th>
				   <th style="display:none"> Hours Status </th>
				  <th class="sortStyle unsortStyle"> Total Hours<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Approved Hours<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Declined Hours<i class="mdi mdi-chevron-down"></i></th>
				 

				</tr>
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
				<?php $color_info = UserInfoController::color_info($datas->id);  ?>
					<tr <?php if($color_info != "") { ?>style="background:<?php echo $color_info; } ?>">
					  <td><?php echo $count; ?></td>
					  <td> {{ $datas->emp_id  }} </td>
					  <td>
						{{-- <a class="view_ts" style="margin-left: 5px;"  href="{{ url('/user/suser/timesheets') }}/{{ $user }}/{{ $datas->id  }}/{{ $frm_date }}/{{ $t_date }}" title="Time Sheets"><i class="fa fa-book"></i></a> --}}
						<a class="view_ts" 
   style="margin-left: 5px;"  
   href="{{ url('/user/suser/timesheets') }}/{{ $user }}/{{ $datas->id }}/{{ $frm_date }}/{{ $t_date }}?u=supervisor" 
   title="Time Sheets">
   <i class="fa fa-book"></i>
</a>
					 </td>
					  <td> {{ $datas->last_name  }} {{ $datas->first_name  }} </td>
					  <td> {{ $datas->dept  }} </td>
					   <td > <?php $user_companies = UserssaController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
					   <td> @if($datas->last_login_at != null) {{ date('h:i a', strtotime($datas->last_login_at)) }} @endif</td>
					   <td> @if($datas->last_login_at != null) {{ date('M d, Y', strtotime($datas->last_login_at)) }} @endif</td>
					  <td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>
					  <td> <?php if($datas->status == 1){ echo "<h5 style='color:green'>Active</h5>"; }else{ echo "<h5 style='color:red'>Inactive</h5>"; } ?></td>
			
					  <td><?php  
					
					  	$total_time = UserssaController::total_time($datas->id,$frm_date,$t_date); $pay_per = ($total_time/80) * 100; 
						if($total_time <=  79){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_time."</p>";
						}elseif($total_time ==  80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_time."</p>";
						}
					   ?></td>
					   <td><?php $approved_time = UserssaController::approved_time($datas->id,$frm_date,$t_date); 
					    if($approved_time <=  79){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_time."</p>";
						}elseif($approved_time ==  80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_time."</p>";
						}
					   
					   ?></td>
					    <td><?php $denied_time = UserssaController::denied_time($datas->id,$frm_date,$t_date); 
						if($denied_time <=  80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_time."</p>";
						}elseif($denied_time ==  80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_time."</p>";
						}
						?></td>

					</tr>
				<?php $count++; ?>
				@endforeach
				
				@else
					<p>Sorry No Data!!</p>
				@endif
			  </tbody>
			</table>
			<div class="pagination">
    <?php echo $data->links(); ?>
</div>
		  </div>
		</div>
	  </div>
	</div>
	<div class="loding"></div>
	</div>
@endsection		
