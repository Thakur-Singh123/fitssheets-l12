@extends('layouts.master')

@section('content')
<?php use App\Http\Controllers\Admin\UserInfoController; ?>
<style>
.table td img:not(.thumb-image), .table th img:not(.thumb-image) {
    border-radius: none !important;
}
.table td {
    font-weight: 600;
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
		  	<div class="card-body-inn">
			<h4 class="card-title">Users</h4>
			{{-- <p  class="card-description"><a href="{{ route('users.create') }}"> Add User <i class="fa fa-plus-circle"></i></a> </p> --}}
			<div class="d-flex justify-content-end align-items-center mb-3">
				<a href="{{ route('users.create') }}" class="btn btn-primarys" style="background-color:#1c45ef; color: white; border-radius: 6px; padding: 10px 15px; font-size: 14px;">
					<i class="fa fa-plus mr-1"></i>
					Add Vacation
				</a>
			</div>
			  <div class="row">
				<div class="col-md-4">
				  <div class="form-group row">
					<label class="col-sm-4 col-form-label">Search User</label>
					<div class="col-sm-8">
					  <input type="text" class="form-control" id="srch_user" name="srch_user">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				 <button data-baseURL="{{ url('/') }}" id="submit_user" type="submit" class="btn btn-success mr-2">Submit</button>
				 </div>
				 		<div class="col-md-4">
				  <div class="form-group row">
					<label  style="font-size: 14px; display: flex; margin: 0px 30px 0px -5px;" class="col-sm-3 col-form-label">Search User By Approve/Decline</label>
					<div class="col-sm-8">
					  <select data-baseURL="{{ url('/') }}" id="aap_status" class="form-control" name="aap_status" >
						<option value="0" >Select</option>
						<option value="2" >Approved</option>
						<option value="1" >Declined</option>
					  </select>
					</div>
				  </div>
				</div>
		
			  </div>
		
					
			 <form>
			@csrf
			  <div class="row">
				<div class="col-md-2">
				  <div class="form-group row">
					<div class="col-sm-12">
					  <input style="color: #1c45ef;font-weight: bold; font-size: 14px;" value="{{ $frm_date }}" type="text" data-baseURL="{{ url('/') }}" class="form-control" id="frm_dt" name="frm_dt">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				  <div class="form-group row">
					<label class="col-sm-5 col-form-label"><b>-</b></label>
					<div class="col-sm-7">
					  <input style="color: #1c45ef;font-weight: bold; font-size: 14px;" value="{{ $t_date }}" type="text" data-baseURL="{{ url('/') }}" class="form-control" id="to_dt" name="to_dt">
					</div>
				  </div>
				</div>
			
				 <div class="col-md-6">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-5 col-form-label">Sort User By Company</label>
					<div class="col-sm-7">
					  <select data-baseURL="{{ url('/') }}" id="search_by_comp" class="form-control" name="search_by_comp" >
						<option value="0" >Select</option>
						@if($companies->count() != 0)
							@foreach ($companies as $company)
								<option <?php if(isset($search_by_comp)){if($search_by_comp == $company->id ){ echo "selected"; }} ?> value="{{ $company->id }}" >{{ $company->company }}</option>
							@endforeach
						  @endif 
					  </select>
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				 <button style="width:161px;"  data-baseURL="{{ url('/') }}" id="user_payperiod" type="submit" class="btn btn-success mr-2"><i class="fa fa-search"></i>Search Payperiod</button>
				 </div>
			  </div>
			  
			</form>
         <form>
			@csrf
			  <div class="row">
				<div class="col-md-6">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-5 col-form-label">Search by payperiod</label>
					
					<div class="col-sm-7">
					  <select data-baseURL="{{ url('/') }}" id="search_by_payu" style="color: #1c45ef;font-weight: bold; font-size: 14px;"class="form-control" name="search_by_payu" >
						<?php if(isset($payperiods_dates1)){ ?>
								<?php foreach($payperiods_dates1 as $payperiods_date){?>
									<?php 
												$TodayDate = new DateTime();	
												$bet_dates = explode('-',$payperiods_date->payperiod_value);
												//$company = $payperiods_date->companies->company;
												//print_r($company);
												
												$company = 'No';
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

											<option selected style="color: #0aab52; font-weight: bold; font-size: 16px;" value="<?php echo $payperiods_date->payperiod_value; ?>"> <?php echo "<p>".date('d-M-Y', strtotime($xfrom_date))."-".date('d-M-Y',strtotime($xto_date))."               |  ".date('d-M-Y', strtotime('+5 days', strtotime($xto_date)))."         |  ".$company."</p>"; ?></option>
									 <?php } else{ ?>
									<option  style="color: #f53838; font-weight: bold; font-size: 14px;" value="<?php echo $payperiods_date->payperiod_value; ?>"><?php echo "<p>".date('d-M-Y', strtotime($xfrom_date))."-".date('d-M-Y',strtotime($xto_date))."               |  ".date('d-M-Y', strtotime('+5 days', strtotime($xto_date)))."         |  ".$company."</p>"; ?></option>
									 <?php } ?><?php } ?>
							<?php } ?>
								
						
						
						
					  </select>
					  
					</div>
				  </div>
				  </div>
				 <div class="col-md-4">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-5 col-form-label">Sort User By Company</label>
					<div class="col-sm-7">
					  <select data-baseURL="{{ url('/') }}" id="search_by_compp" class="form-control" name="search_by_compp" >
						<option value="0" >Select</option>
						@if($companies->count() != 0)
							@foreach ($companies as $company)
								<option <?php if(isset($search_by_comp)){if($search_by_comp == $company->id ){ echo "selected"; }} ?> value="{{ $company->id }}" >{{ $company->company }}</option>
							@endforeach
						  @endif 
					  </select>
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				 <button style="width:161px;"  data-baseURL="{{ url('/') }}" id="user_spayperiod" type="submit" class="btn btn-success mr-2"><i class="fa fa-search"></i>Search Payperiod</button>
				 </div>
			  </div>
			  
			</form>
			
			
			
			<div class="row">
			<div class="col-md-4">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-5 col-form-label">Search User By Status</label>
					<div class="col-sm-7">
					  <select data-baseURL="{{ url('/') }}" id="user_status" class="form-control" name="user_status" >
						<option value="" >Select</option>
						<option value="1" >Active</option>
						<option value="0" >Inactive</option>
					  </select>
					</div>
				  </div>
				</div>
			<div class="col-md-4">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-5 col-form-label">Sort By Color</label>
					<div class="col-sm-7">
					 <select data-baseURL="{{ url('/') }}" id="search_by_super" class="form-control" name="search_by_super" >
						<option value="0" >Select</option>
						@if(isset($Supervisor_color))
							@foreach ($Supervisor_color as $Supervisor)
							<?php if(!empty($Supervisor->color_field)){ ?>
								<option  style="background:<?php echo $Supervisor->color_field; ?>;color:#fff" value="{{ $Supervisor->id }}" >{{ $Supervisor->name }}</option>
							<?php } ?>
							@endforeach
						  @endif
					  </select>
					</div>
				  </div>
				</div>
			</div>
			@if($data->count() != 0)
			<div style="margin:-64px 178px 40px 0px;" align="right">
				<a href="{{ url('/user/export/all') }}" id="export" class="btn btn-success">Export to Excel</a>
		    </div>
			@endif
			<div class="row">
			<div class="col-md-4">
				  <div class="form-group row">
					<label style="font-size: 13px;" class="col-sm-5 col-form-label">Staff List By Company</label>
					
					<div class="col-sm-7">
					 <select data-baseURL="{{ url('/') }}" id="search_by_stafflist" class="form-control" name="search_by_stafflist" >
						<option value="0" >Select</option>
						@if($companies->count() != 0)
							@foreach ($companies as $company)
								<option <?php if(isset($search_by_comp)){if($search_by_comp == $company->id ){ echo "selected"; }} ?> value="{{ $company->id }}" >{{ $company->company }}</option>
							@endforeach
						  @endif 
					  </select>
					</div>
					<div style="margin: -48px 0px 0px 330px;" align="right">
					   <a href="" id="export_s" class="btn btn-success">Export</a>
				    </div>
				    </div>
				</div>
			</div>
		</div>
			<table id="sortable-table-1" class="table table-striped table-responsive">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th> Actions </th>
				  <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
				   <th> Status </th>
				   <th class="sortStyle unsortStyle"> Hours Rate($)<i class="mdi mdi-chevron-down"></i> </th>
				   <th class="sortStyle unsortStyle">Time<i class="mdi mdi-chevron-down"></i></th>
					<th class="sortStyle unsortStyle">Day<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Created<i class="mdi mdi-chevron-down"></i> </th>
				 
				  <th class="sortStyle unsortStyle"> Total Hours<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Approved Hours<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Declined Hours<i class="mdi mdi-chevron-down"></i></th>
				  <th > Approved By </th>
				  <th class="sortStyle unsortStyle"> Driving License<i class="mdi mdi-chevron-down"></i> </th>
				   <th class="sortStyle unsortStyle"> COVID Report<i class="mdi mdi-chevron-down"></i> </th>
				    <th class="sortStyle unsortStyle"> Email<i class="mdi mdi-chevron-down"></i> </th>
				  <?php if(Auth::user()->id == 72 || Auth::user()->id == 50 || Auth::user()->id == 282 ){ ?>
				  <?php }else{ ?>
				  <th class="sortStyle unsortStyle"> User Password<i class="mdi mdi-chevron-down"></i> </th>
				  <?php } ?>
				  <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
				 
				</tr>
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
				<?php $approved_by = UserInfoController::approved_by($datas->id); 
						$color_info = UserInfoController::color_info($datas->id); 
				?>
					<tr <?php if($color_info != "") { ?>style="background:<?php echo $color_info; } ?>">
					  <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}.</td>
					  <td> {{ $datas->last_name  }} {{ $datas->first_name  }}</td>
					 
					  <td>
						{{-- <a  href="{{ route('users.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a> --}}
						<a href="{{ route('users.edit', ['user' => $datas->id, 'u' => 'user']) }}" title="Edit">
							<i class="fa fa-pencil"></i>
						</a>
						 <?php if(Auth::user()->id == 72 || Auth::user()->id == 50 || Auth::user()->id == 282){ ?>
						<?php }else{ ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/changepassword') }}/{{ $datas->id  }}" title="Change Password"><i class="fa fa-unlock"></i></a>
						<?php } ?>
						{{-- <a style="margin-left: 5px;cursor:pointer" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_user" title="Delete"><i class="fa fa-trash-o"></i></a> --}}
						<a style="margin-left: 5px;cursor:pointer" data-baseURL="{{ url('/') }}" data-user_id="{{ $datas->id  }}" class="is_delete_user" title="Delete"><i class="fa fa-trash-o"></i></a>
						<?php if($datas->role == "user"){ ?>
						 <?php if(isset($search_by_comp)){ ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/timesheets') }}/{{ $datas->id  }}/{{ $frm_date }}/{{ $t_date }}/{{ $search_by_comp }}" title="Time Sheets"><i class="fa fa-book"></i></a>
						<?php }else{ ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/timesheets') }}/{{ $datas->id  }}" title="Time Sheets"><i class="fa fa-book"></i></a>
						<?php } ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/driver/license') }}/{{ $datas->id  }}" title="Driver License"><i class="fa fa-drivers-license"></i></a>
						<?php } ?>

						
					  </td>

					   <td>{{ $datas->emp_id  }}</td>
					  
					    <td> <?php if($datas->status == 1){ echo "<h5 style='color:green'>Active</h5>"; }else{ echo "<h5 style='color:red'>Inactive</h5>"; } ?></td>
					     <td> {{ $datas->hourst_rate  }} </td>
					 
					  
					
					 
					  
					   <td style="display:none"> <?php //echo $supervisor = UserInfoController::supervisor_info($datas->companies_id); ?> </td>
					  
					 
					   
					   <td> @if($datas->last_login_at != null) {{ date('h:i a', strtotime($datas->last_login_at)) }} @endif</td>
					   <td> @if($datas->last_login_at != null) {{ date('M d, Y', strtotime($datas->last_login_at)) }} @endif</td>
					  <td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>
					  <td>
					   <?php if($frm_date && $t_date){ ?>
					  <?php 
					   $frm_date    = explode('-', $frm_date);
						$frm_date = implode("_", $frm_date);
						$t_date    = explode('-', $t_date);
					   $t_date = implode("_", $t_date); } ?>
					  <?php if($frm_date && $t_date){ ?>
					  <?php 
					  $total_time = UserInfoController::ttotal_time($datas->id, $frm_date, $t_date);
					   }else{ ?>
					  <?php $total_time = UserInfoController::total_time($datas->id); }
						if($total_time <=  79){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_time."</p>";
						}elseif($total_time ==  80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_time."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_time."</p>";
						}
					   ?></td>
					   <td>
					    <?php if($frm_date && $t_date){ ?>
					  <?php $approved_time = UserInfoController::tapproved_time($datas->id, $frm_date, $t_date);
					   }else{ ?>
					  <?php $approved_time = UserInfoController::approved_time($datas->id); }

					    if($approved_time <=  79){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_time."</p>";
						}elseif($approved_time ==  80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_time."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_time."</p>";
						}
					   
					   ?></td>
					    <td>
						 <?php if($frm_date && $t_date){ ?>
					  <?php $denied_time = UserInfoController::tdenied_time($datas->id, $frm_date, $t_date);
					   }else{ ?>
					  <?php $denied_time = UserInfoController::denied_time($datas->id); }
						if($denied_time <=  80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_time."</p>";
						}elseif($denied_time ==  80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_time."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_time."</p>";
						}
						?></td>
						 <td><?php echo $approved_by; ?></td>
						 <td> <?php if($datas->drivers_license != null){ ?>
					<img style=" margin-top: 15px; width: 152px;height: 156px;" src="{{ url('/assets/uploads/driving-license') }}/{{ $datas->drivers_license }}">
					<?php } else{ ?>
						<p class="no-data">No license found!</p>
						<a  href="{{ route('users.edit', $datas->id) }}" title="Edit">Upload Here</a>
					<?php } ?> </td>
					<td> <?php if($datas->covid_report != null){ ?>
					<img style=" margin-top: 15px; width: 152px;height: 156px;border-radius: none !important;" src="{{ url('/assets/uploads/covid-report') }}/{{ $datas->covid_report }}">
					<?php } else{ ?>
						<p class="no-data">No report found!</p>
					<?php } ?> </td>
					 <td> {{ $datas->email  }} </td>
					  <?php if(Auth::user()->id == 72 || Auth::user()->id == 50 ){ ?>
						<?php }else{ ?>
					  <td> {{ $datas->pass  }} </td>
					  <?php } ?>
					  <td > <?php $user_companies = UserInfoController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
					   <td> {{ $datas->dept  }} </td>
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
            {{ $data->links('pagination::bootstrap-5') }}
		</div>
	  </div>
	</div>
	<div class="loding"></div>
</div>
@endsection	

