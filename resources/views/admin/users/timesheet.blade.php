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
			<h4 class="card-title">Users</h4>
			<p  class="card-description"><a href="{{ route('users.create') }}"> Add User <i class="fa fa-plus-circle"></i></a> </p>
			
			  <div class="row">
				<div class="col-md-4">
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">Search User</label>
					<div class="col-sm-9">
					  <input type="text" class="form-control" id="srch_user" name="srch_user">
					</div>
				  </div>
				</div>
				<div  class="col-md-2">
				 <button data-baseURL="{{ url('/') }}" id="nsubmit_user" type="submit" class="btn btn-success mr-2">Submit</button>
				 </div>
				 		<div style="display:none" class="col-md-6">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-3 col-form-label">Search User By Approve/Decline</label>
					<div class="col-sm-9">
					  <select data-baseURL="{{ url('/') }}" id="naap_status" class="form-control" name="naap_status" >
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
					  <input value="<?php if(isset($from_date)){ echo $from_date; }else{ echo $frm_date; } ?>" type="text" data-baseURL="{{ url('/') }}" class="form-control" id="frm_dt" name="frm_dt">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				  <div class="form-group row">
					<label class="col-sm-2 col-form-label"><b>-</b></label>
					<div class="col-sm-10">
					  <input  value="<?php if(isset($to_date)){ echo $to_date; }else{ echo $t_date; } ?>" type="text" data-baseURL="{{ url('/') }}" class="form-control" id="to_dt" name="to_dt">
					</div>
				  </div>
				</div>
				 
				 <div class="col-md-6">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-3 col-form-label">Sort User By Company</label>
					<div class="col-sm-9">
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
				 <button style="width:161px;"  data-baseURL="{{ url('/') }}" id="nuser_payperiod" type="submit" class="btn btn-success mr-2"><i class="fa fa-search"></i>Search Payperiod</button>
				 </div>
			  </div>
			  
			</form>
			<div style="display:none" class="row">
			<div class="col-md-4">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-3 col-form-label">Search User By Status</label>
					<div class="col-sm-6">
					  <select data-baseURL="{{ url('/') }}" id="nuser_status" class="form-control" name="nuser_status" >
						<option value="" >Select</option>
						<option value="1" >Active</option>
						<option value="0" >Inactive</option>
					  </select>
					</div>
				  </div>
				</div>
			<div class="col-md-4">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-3 col-form-label">Sort By Color</label>
					<div class="col-sm-6">
					 <select data-baseURL="{{ url('/') }}" id="nsearch_by_super" class="form-control" name="nsearch_by_super" >
						<option value="0" >Select</option>
						@if($Supervisor_color->count() != 0)
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
			<div style="    margin-top: 10px;" align="left">
				<a href="{{ url('/user/export/all') }}" id="export" class="btn btn-success">Export to Excel</a>
		    </div>
			@endif
			<table id="sortable-table-1" class="table table-striped table-responsive">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
				  <th> Actions </th>
				  <th > Approved By </th>
				  
			
				   <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle">Time<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle">Day<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Created<i class="mdi mdi-chevron-down"></i> </th>
				 
				  <th class="sortStyle unsortStyle"> Total Hours<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Approved Hours<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Declined Hours<i class="mdi mdi-chevron-down"></i></th>
				  <th style='background: blue;color: #fff;font-size: 20px;font-weight: bold;' class="sortStyle unsortStyle"> Vacc hrs<i class="mdi mdi-chevron-down"></i></th>
				  <th style='background: blue;color: #fff;font-size: 20px;font-weight: bold;' class="sortStyle unsortStyle"> Vacc Dur<i class="mdi mdi-chevron-down"></i></th>
				  <th style='background: blue;color: #fff;font-size: 20px;font-weight: bold;' class="sortStyle unsortStyle"> Vacc Status<i class="mdi mdi-chevron-down"></i></th>
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
					  <td><?php echo $count; ?></td>
					  <td>{{ $datas->emp_id  }}</td>
					  <td>
						<a  href="{{ route('users.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
						 <?php if(Auth::user()->id == 72 || Auth::user()->id == 50 || Auth::user()->id == 282){ ?>
						<?php }else{ ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/changepassword') }}/{{ $datas->id  }}" title="Change Password"><i class="fa fa-unlock"></i></a>
						<?php } ?>
						<a style="margin-left: 5px;cursor:pointer" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_user" title="Delete"><i class="fa fa-trash-o"></i></a>
						<?php if($datas->role == "user"){ ?>
						 <?php if(isset($search_by_comp)){ ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/timesheets') }}/{{ $datas->id  }}/{{ $frm_date }}/{{ $t_date }}/{{ $search_by_comp }}" title="Time Sheets"><i class="fa fa-book"></i></a>
						<?php }else{ ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/timesheets') }}/{{ $datas->id  }}" title="Time Sheets"><i class="fa fa-book"></i></a>
						<?php } ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/driver/license') }}/{{ $datas->id  }}" title="Driver License"><i class="fa fa-drivers-license"></i></a>
						<?php } ?>

						
					  </td>
					  <td><?php echo $approved_by; ?></td>					 											
					   <td > <?php $user_companies = UserInfoController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
					 
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
						<?php /*
												<?php
												
												$vacc_report = UserInfoController::vacc_report($datas->id) ?>
												
						<?php 
										if(isset($vacc_report) && !empty($vacc_report)){
												echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'>".$vacc_report[0]."</td>";
												echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'>".date('M d', strtotime($vacc_report[1]))."-".date('M d, Y', strtotime($vacc_report[2]))."</td>";
													if($vacc_report[3]==0){echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'><p style='padding: 10px;
    text-align: left;
    color: #000;
    background: yellow;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Pending</p></td>";}elseif($vacc_report[3]==1){echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'> <p style='padding: 10px;
    text-align: left;
    color: #fff;
    background: green;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Approved</p></td>";}elseif($vacc_report[3]==2){echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'><p style='padding: 10px;
    text-align: left;
    color: #fff;
    background: red;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Decline</p></td>";}else{echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'><p style='padding: 10px;
    text-align: left;
    color: #000;
    background: yellow;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Pending</p></td>";}
												

										}
					?>
*/?>					</tr>
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
