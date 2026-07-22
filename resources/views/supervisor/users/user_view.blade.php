@extends('layouts.supervisor')
@section('content')
<?php use App\Http\Controllers\Supervisor\UserssController; ?>
<?php use App\Http\Controllers\Supervisor\TimesheetssController; ?>
<style>td {
   text-align: center;
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
				<h4 class="card-title">Users</h4>
				<?php /*<form class="form-sample">
					<div class="row">
					<div class="col-md-10">
					<div class="form-group row">
					<label class="col-sm-3 col-form-label">Search User</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="ssrch_user" name="ssrch_user">
					</div>
					</div>
					</div>
					<div class="col-md-2">
					<button data-baseURL="{{ url('/') }}" id="ssubmit_user" type="submit" class="btn btn-success mr-2">Submit</button>
					</div>
					</div>
					
					</form>*/?>
				<form action="{{ url('/suser/searchs') }}" method="POST" role="search">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-md-10">
							<div class="form-group row">
							<label class="col-sm-3 col-form-label">Search User</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="ssrch_users" placeholder="Search Users" > 
							</div>
							</div>
						</div>
						<div class="col-md-2">
							<button data-baseURL="{{ url('/') }}"  type="submit" class="btn btn-success mr-2">Submit</button>
						</div>
					</div>
				</form>
				<form class="form-sample" method="POST" action="{{ url('/suser/exportall/timesheet') }}">
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
								<input type="text" data-baseURL="{{ url('/') }}" class="form-control" id="to_dt" name="to_dt" value="{{ $t_date }}">
							</div>
							</div> 
						</div>
						<div class="col-md-6">
							<div class="form-group row">
							<label  style="font-size: 13px;" class="col-sm-3 col-form-label">Sort User By Company</label>
							<div class="col-sm-9">
								<select data-baseURL="{{ url('/') }}" id="ssearch_by_comp" class="form-control" name="ssearch_by_comp" >
									<option value="0" >Select</option>
									@if($companiess->count() != 0)
									@foreach ($companiess as $company)
									<option <?php if(isset($ssearch_by_comp)){if($ssearch_by_comp == $company->id ){ echo "selected"; }} ?>    value="{{ $company->id }}" >{{ $company->company }}</option>
									@endforeach
									@endif
								</select>
							</div>
							</div>
						</div>
						<div class="col-md-2">
							<button style="width:161px;" data-baseURL="{{ url('/') }}" id="search_payperiod" type="submit" class="btn btn-success mr-2"><i class="fa fa-search"></i>Search Payperiod</button>
						</div>
					</div>
				</form>
				<form>
					@csrf
					<div class="row">
						<div class="col-md-6">
							<div class="form-group row">
							<label  style="font-size: 13px;" class="col-sm-3 col-form-label">Search by payperiod</label>
							<div class="col-sm-9">
								<select data-baseURL="{{ url('/') }}" id="search_by_payu" style="color: #1c45ef;font-weight: bold; font-size: 12px;"class="form-control" name="search_by_payu" >
									<?php if(isset($payperiods_dates1)){ ?>
									<?php foreach($payperiods_dates1 as $payperiods_date){?>
									<?php 
										$TodayDate = new DateTime();	
										$bet_dates = explode('-',$payperiods_date->payperiod_value);
										$company = $payperiods_date->companies->company;
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
									<option selected style="color: #0aab52; font-weight: bold; font-size: 12px;" value="<?php echo $payperiods_date->payperiod_value; ?>"> <?php echo "<p>".date('d-M-Y', strtotime($xfrom_date))."-".date('d-M-Y',strtotime($xto_date))."               |  ".date('d-M-Y', strtotime('+5 days', strtotime($xto_date)))."         |  ".$company."</p>"; ?></option>
									<?php } else{ ?>
									<option  style="color: #f53838; font-weight: bold; font-size: 12px;" value="<?php echo $payperiods_date->payperiod_value; ?>"><?php echo "<p>".date('d-M-Y', strtotime($xfrom_date))."-".date('d-M-Y',strtotime($xto_date))."               |  ".date('d-M-Y', strtotime('+5 days', strtotime($xto_date)))."         |  ".$company."</p>"; ?></option>
									<?php } ?><?php } ?>
									<?php } ?>
								</select>
							</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group row">
							<label  style="font-size: 13px;" class="col-sm-3 col-form-label">Sort User By Company</label>
							<div class="col-sm-9">
								<select data-baseURL="{{ url('/') }}" id="search_by_compp" class="form-control" name="search_by_compp" >
									<option value="0" >Select</option>
									@if($companiess->count() != 0)
									@foreach ($companiess as $company)
									<option <?php if(isset($search_by_compp)){if($search_by_compp == $company->id ){ echo "selected"; }} ?> value="{{ $company->id }}" >{{ $company->company }}</option>
									@endforeach
									@endif
								</select>
							</div>
							</div>
						</div>
						<div class="col-md-2">
							<button style="width:161px;"  data-baseURL="{{ url('/') }}" id="suser_sspayperiod" type="submit" class="btn btn-success mr-2"><i class="fa fa-search"></i>Search Payperiod</button>
						</div>
						<div style="" class="col-md-2">
							<a href="" style="width:175px;margin-left:790px" id="export_all_user" class="btn btn-success"><i class="fa fa-calendar"></i> Export Timesheets</a>
							</button>
						</div>
					</div>
					<br>
				</form>
				<table id="sortable-table-1" class="table table-striped table-responsive">
					<thead>
						<tr>
							<th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle"> Actions<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle"> Supervisor<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle">Time<i class="mdi mdi-chevron-down"></i></th>
							<th class="sortStyle unsortStyle">Day<i class="mdi mdi-chevron-down"></i></th>
							<th class="sortStyle unsortStyle"> Created<i class="mdi mdi-chevron-down"></i></th>
							<th class="sortStyle unsortStyle"> Status<i class="mdi mdi-chevron-down"></i></th>
							<th class="sortStyle unsortStyle"> Total Hours<i class="mdi mdi-chevron-down"></i></th>
							<th class="sortStyle unsortStyle"> Approved Hours<i class="mdi mdi-chevron-down"></i></th>
							<th class="sortStyle unsortStyle"> Declined Hours<i class="mdi mdi-chevron-down"></i></th>
						</tr>
					</thead>
					<tbody id="result">
						@if($data->count() != 0)
						@foreach ($data as $datas)
						<?php $color_info = UserssController::color_info($datas->id);  ?>
						<tr <?php if($color_info != "") { ?>style="background:<?php echo $color_info; } ?>">
							<td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}.</td>
							<td> {{ $datas->emp_id  }} </td>
							<td>
							<?php if(isset($ssearch_by_comp)){ ?>
							<a  style="margin-left: 5px;"  href="{{ url('/suser/timesheets') }}/{{ $datas->id  }}/{{ $frm_date }}/{{ $t_date }}/{{ $ssearch_by_comp }}" title="Time Sheets"><i class="fa fa-book"></i></a>
							<?php }else{ ?>
							<a  style="margin-left: 5px;"  href="{{ url('/suser/timesheets') }}/{{ $datas->id  }}/{{ $frm_date }}/{{ $t_date }}" title="Time Sheets"><i class="fa fa-book"></i></a>
							<?php } ?>
							</td>
							<td> {{ $datas->last_name  }} {{ $datas->first_name  }}</td>
							<td> {{ $user_f_name  }} </td>
							<td> {{ $datas->dept  }} </td>
							<td > <?php $user_companies = TimesheetssController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
							<td> @if($datas->last_login_at != null) {{ date('h:i a', strtotime($datas->last_login_at)) }} @endif</td>
							<td> @if($datas->last_login_at != null) {{ date('M d, Y', strtotime($datas->last_login_at)) }} @endif</td>
							<td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>
							<td> <?php if($datas->status == 1){ echo "<h5 style='color:green'>Active</h5>"; }else{ echo "<h5 style='color:red'>Inactive</h5>"; } ?></td>
							<td><?php $total_time = TimesheetssController::ttotal_time($datas->id,$frm_date,$t_date); 
							// echo $datas->id;
								// echo $frm_date;
							// echo $t_date;
							// echo $total_time;
							if($total_time <=  79){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_time."</p>";
							}elseif($total_time ==  80){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_time."</p>";
							}else{
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_time."</p>";
							}
							
							?></td>
							<td><?php $approved_time = TimesheetssController::tapproved_time($datas->id,$frm_date,$t_date); 
							if($approved_time <=  79){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_time."</p>";
							}elseif($approved_time ==  80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_time."</p>";
							}else{
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_time."</p>";
							}
								
								?></td>
							<td><?php $denied_time = TimesheetssController::tdenied_time($datas->id,$frm_date,$t_date); 
							if($denied_time <=  80){
									echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_time."</p>";
							}elseif($denied_time ==  80){
									echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_time."</p>";
							}else{
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_time."</p>";
							}
							?></td>
						</tr>
						@endforeach
						@else
						<p>Sorry No Data!!</p>
						@endif
					</tbody>
				</table>
				{{ $data->links('pagination::bootstrap-5') }}
				</div>
			</div>
		</div>
	</div>
    <div class="loding"></div>
</div>
@endsection