@extends('layouts.master')

@section('content')
<?php use App\Http\Controllers\Admin\TimesheetaController;
use App\Payperiods;
$payperiods_dates1 = Payperiods::orderBy('created_at', 'DESC')->get(); ?>
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
			<?php if(isset($search_by_comp)){ ?>
			<a href="{{ url('/users') }}/{{ $frm_date }}/{{ $t_date }}/{{ $search_by_comp }}"><i style="padding-bottom: 15px;" class="fa fa-backward"></i></a>
			<?php }else{ ?>
			<a href="{{ url('/users') }}"><i style="padding-bottom: 15px;" class="fa fa-backward"></i></a>
			<?php } ?>
			<form class="form-sample">
			<input type="hidden" class="form-control" value="{{ $id }}" id="user_id" name="user_id">
			  <div class="row">
			  <div class="col-md-2">
			  <h4 class="card-title">Time Sheets({{ $name }})</h4>
			<p  class="card-description add_hours"><a style="color:#fff;padding: 15px;" href="{{ url('/user/create/timesheets') }}/{{ $id }}"> Add Hours <i class="fa fa-plus-circle"></i></a> </p>
			</div>
			      <div class="col-md-2">
			          <h5 > Search Timesheets </h5>
			          </div>
				<div class="col-md-3">
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">From Date</label>
					<div class="col-sm-9">
					  <input type="text" class="form-control" id="frm_dt" name="frm_dt" value="{{ $frm_date }}">
					</div>
				  </div>
				</div>
				<div class="col-md-3">
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">To date</label>
					<div class="col-sm-9">
					  <input type="text" class="form-control" id="to_dt" name="to_dt" value="{{ $t_date }}">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				 <button data-baseURL="{{ url('/') }}" id="asubmit_date" type="submit" class="btn btn-success mr-2">Submit</button>
				 </div>
			  </div>
			  
			</form>
			<form>
			@csrf
			<input type="hidden" class="form-control" value="{{ $id }}" id="user_id" name="user_id">
			  <div class="row">
				<div class="col-md-6">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-3 col-form-label">Search by payperiod</label>
					
					<div class="col-sm-9">
					  <select data-baseURL="{{ url('/') }}" id="search_by_pay_ts" style="color: #1c45ef;font-weight: bold; font-size: 14px;"class="form-control" name="search_by_pay_ts" >
						<?php if(isset($payperiods_dates1)){ ?>
								<?php foreach($payperiods_dates1 as $payperiods_date){?>
								
									<?php 
												$TodayDate = new DateTime();	
												$bet_dates = explode('-',$payperiods_date->payperiod_value);
												if(isset($bet_dates)){
													$from_date    = $bet_dates[0];
													$to_date    = $bet_dates[1];
												}
												else{
													$from_date  = "";
													$to_date = "";
												}
												if(!empty($frm_dt) && !empty($to_dt)){
													$from_date = $frm_dt;
													$to_date = $to_dt;
												}
												$xto_date = explode('_',$to_date);
												$xto_date = implode('-',$xto_date);
												$xfrom_date = explode('_',$from_date);
												$xfrom_date = implode('-',$xfrom_date);
												$xtto_date = new DateTime($xto_date);
												$xtfrom_date  = new DateTime($xfrom_date);
											?>
										<?php  if (
									  $TodayDate->format('y-m-d') >= $xtfrom_date->format('y-m-d') && 
									  $TodayDate->format('y-m-d') <= $xtto_date->format('y-m-d')){
									  
									?>

											<option selected style="color: #0aab52; font-weight: bold; font-size: 16px;" value="<?php echo $payperiods_date->payperiod_value; ?>"> <?php echo "<p>".date('d-M-Y', strtotime($xfrom_date))."-".date('d-M-Y',strtotime($xto_date))."               |  ".date('d-M-Y', strtotime('+5 days', strtotime($xto_date)))."</p>"; ?></option>
									 <?php } else{ ?>
									<option  style="color: #f53838; font-weight: bold; font-size: 14px;" value="<?php echo $payperiods_date->payperiod_value; ?>"><?php echo "<p>".date('d-M-Y', strtotime($xfrom_date))."-".date('d-M-Y',strtotime($xto_date))."               |  ".date('d-M-Y', strtotime('+5 days', strtotime($xto_date)))."</p>"; ?></option>
									 <?php } ?><?php } ?>
							<?php } ?>
								
						
						
						
					  </select>
					  
					</div>
				  </div>
				  </div>
				
				<div class="col-md-2">
				 <button data-baseURL="{{ url('/') }}" id="aasubmit_date" type="submit" class="btn btn-success mr-2">Submit</button>
				 </div>
			  </div>
			  
			</form>
			
			
			
			@if($data->count() != 0)
			<div align="left">
				<a href="{{ route('user.export.time-sheet', $id) }}" id="export" class="btn btn-success">Export to Excel</a>
		    </div>
			@endif
			<?php $cm_check = TimesheetaController::checkCM($id); ?>
			<table id="sortable-table-1" class="table dataTable table-striped table-responsive">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle">Emp ID<i class="mdi mdi-chevron-down"></i> </th>
				  <?php if(!empty($cm_check)){ ?>
				  <th class="sortStyle unsortStyle"> Case Manager <i class="mdi mdi-chevron-down"></i> </th-->
					<?php } ?>
				  				  <th> Actions </th>
				  <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> House <i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Time<br>In <i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Time<br>Out <i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Hours<br>Worked <i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Date <i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Hours<br>Rate<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Remarks<i class="mdi mdi-chevron-down"></i> </th>
				  <th style="display:none" class="sortStyle unsortStyle"> Vacation<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Approved <i class="mdi mdi-chevron-down"></i></th>
				   <th > Approved<br>By </th>
				  <th > User Added<br>Hours At </th>
				   <th > Approved<br>At </th>

				</tr>
				
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
					<tr>
					  <td><?php echo $count; ?></td>
					  <td> {{ $datas->users->emp_id  }} </td>
					  <?php if(!empty($cm_check)){ ?>
						  <?php if($datas->cmcheck_status == 2){ ?>
							<?php $caseManager = TimesheetaController::caseManager($datas->cm_id); ?>
							
									
								  <td>
								  <?php if(!empty($caseManager)){ ?>
									<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" ><?php echo  $caseManager; ?></label><br>
									<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" ><?php echo date('M d, Y h:i a', strtotime($datas->cm_update_at )); ?></label><br>
									<label style="background:green;color:#fff;padding:5px;border-radius: 5px;"><?php echo date('(D)', strtotime($datas->cm_update_at )); ?></label>
									<?php } ?>
								  </td>
									
						  <?php }else{ ?> 
							  <td ></td>
						  <?php } ?>
					  <?php } ?>
					  <td>
						<a  href="{{ url('/user/edit/timesheets/') }}/{{ $datas->id }}" title="Edit"><i class="fa fa-pencil"></i></a>
						<a style="margin-left: 5px;" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_ats" title="Delete"><i class="fa fa-trash-o"></i></a>
					  </td>

					   <td> {{ $datas->users->name  }}</td>
					   <td> {{ $datas->users->dept  }} </td>
					  <td> {{ $datas->companies->company  }}</td>
					   <td> {{ substr($datas->houses->house_add, 0, 14)  }} </td>
					   <td> {{ $datas->time_in  }} </td>
					   <td> {{ $datas->time_out  }} </td>
					  <td> <?php $hours    = explode('_', $datas->hours_wrk);$hours = implode(":", $hours); echo $hours;?></td>
					  <td> <?php $hours_day    = explode('_', $datas->hours_day);$hours_day = implode("/", $hours_day); $hours_day = date("M d, Y(D)", strtotime($hours_day)); echo $hours_day;?> </td>
					  <td> {{ $datas->users->hourst_rate  }} </td>
					  <td> {{ $datas->remarks  }} </td>
					   <td style="display:none"> <?php if($datas->vacation_status == "0"){ echo "<h5 style='color:green'>No</h5>"; }elseif($datas->vacation_status == "1"){ echo "<h5 style='color:red'>Yes</h5>"; }?> </td>
					 <td> <?php if($datas->approve == "2"){ echo "<h5 style='color:green'>Yes</h5>"; }elseif($datas->approve == "1"){ echo "<h5 style='color:red'>No</h5>"; }else{ echo "<h5 style='color:#a5a548'>Pending</h5>"; } ?></td>
					  
					   <td >
					   
					   <?php if(!empty($datas->approved_by)){ echo $approved_by = TimesheetaController::userName($datas->approved_by); }else{ echo "--"; }  ?>
					</td>
						<td>{{ date('M d, Y h:i a', strtotime($datas->created_at )) }}</td>
					  
					  <td><?php if(!empty($datas->approved_at)){ echo date('M d, Y h:i a', strtotime($datas->approved_at )); }else{ echo "--"; }  ?></td>
					
					</tr>
				<?php $count++; ?>
				@endforeach
				
				@else
					<p>Sorry No Data!!</p>
				@endif
			  </tbody>
			</table>
			<br>
		  </div>
		</div>
	  </div>
	</div>
	<div class="loding"></div>
	</div>
@endsection		
