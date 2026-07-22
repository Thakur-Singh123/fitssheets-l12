@extends('layouts.supervisor')



@section('content')



  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<?php use App\Http\Controllers\Supervisor\UserssController; ?>

<style>

	.round {

  position: relative;

}



.round label {

  background-color: #fff;

  border: 1px solid #ccc;

  border-radius: 50%;

  cursor: pointer;

  height: 28px;

  left: 0;

  position: absolute;

  top: 0;

  width: 28px;

}



.round label:after {

  border: 2px solid #fff;

  border-top: none;

  border-right: none;

  content: "";

  height: 6px;

  left: 7px;

  opacity: 0;

  position: absolute;

  top: 8px;

  transform: rotate(-45deg);

  width: 12px;

}



.round input[type="checkbox"] {

  visibility: hidden;

}



.round input[type="checkbox"]:checked + label {

  background-color: #66bb6a;

  border-color: #66bb6a;

}



.round input[type="checkbox"]:checked + label:after {

  opacity: 1;

}





.container {

  margin: 0 auto;

}

td {

    text-align: center;

}

ul.imp_actions {

    display: flex;

    list-style: none;

    gap: 10px;

}

ul.imp_actions a {

    cursor: pointer;

}

.img-ss, .table td img:not(.thumb-image), .table th img:not(.thumb-image) {

    width: 25px !important;

    min-width: 25px !important;

    height: 25px !important;

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

			<form style="display:none" class="form-sample">

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

				 <button data-baseURL="{{ url('/') }}" id="nssubmit_user" type="submit" class="btn btn-success mr-2">Submit</button>

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

				 <button style="width:161px;" data-baseURL="{{ url('/') }}" id="nsearch_payperiod" type="submit" class="btn btn-success mr-2"><i class="fa fa-search"></i>Search Payperiod</button>

				 </div>

				 <div class="col-md-2">

				 <a href="" style="width:175px;margin-left: 15px" id="export_all_user" class="btn btn-success"><i class="fa fa-calendar"></i> Export Timesheets</a>

				 </button>

				 </div>

			  </div>

			  

			</form>

			<table id="sortable-table-1" class="table table-striped table-responsive">

			  <thead>

				<tr>

				  <th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>

				  <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>

				  <th > <a  style="    color: #fff" data-baseURL="{{ url('/') }}" id="tpapprove_all" class="btn btn-success">Click<br>to<br>Approve</a><br><input style="    margin-top: 10px;" type="checkbox" id="atime_approve" name="atime_approve" ><!--Approve<i class="mdi mdi-chevron-down"></i>--> </th>

				  <th class="sortStyle unsortStyle"> Actions </th>

				  <!--th class="sortStyle unsortStyle"> Approval Request<i class="mdi mdi-chevron-down"></i></th-->

				 <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>

				   <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>

				   <th class="sortStyle unsortStyle">Time<i class="mdi mdi-chevron-down"></i></th>

				  <th class="sortStyle unsortStyle">Day<i class="mdi mdi-chevron-down"></i></th>

				  <th class="sortStyle unsortStyle"> Created<i class="mdi mdi-chevron-down"></i></th>

				  <th> Status </th>

				  <th class="sortStyle unsortStyle"> THrs<i class="mdi mdi-chevron-down"></i></th>

				  <th class="sortStyle unsortStyle"> AHrs<i class="mdi mdi-chevron-down"></i></th>

				  <th class="sortStyle unsortStyle"> DHrs<i class="mdi mdi-chevron-down"></i></th>

				  

				  <!--th style='background: blue;color: #fff;font-size: 20px;font-weight: bold;' class="sortStyle unsortStyle"> Vacc hrs<i class="mdi mdi-chevron-down"></i></th>

				  <th style='background: blue;color: #fff;font-size: 20px;font-weight: bold;' class="sortStyle unsortStyle"> Vacc Dur<i class="mdi mdi-chevron-down"></i></th>

				  <th style='background: blue;color: #fff;font-size: 20px;font-weight: bold;' class="sortStyle unsortStyle"> Vacc Status<i class="mdi mdi-chevron-down"></i></th-->

				  <!--th class="sortStyle unsortStyle"> App it<i class="mdi mdi-chevron-down"></i></th>

				  <th class="sortStyle unsortStyle"> Dec it<i class="mdi mdi-chevron-down"></i></th>

				  <th class="sortStyle unsortStyle"> Del it<i class="mdi mdi-chevron-down"></i></th-->



				</tr>

			  </thead>

			  <tbody id="result">

			  <?php $count = 1; ?>

			  @if($data->count() != 0)

				@foreach ($data as $datas)

					<?php $color_info = UserssController::color_info($datas->id);  ?>

					<?php $approved_time = UserssController::approved_time($datas->id,$frm_date,$t_date);

							$total_time = UserssController::total_time($datas->id,$frm_date,$t_date); 

							$denied_time = UserssController::denied_time($datas->id,$frm_date,$t_date); 

				  ?>

					<tr <?php if($color_info != "") { ?>style="background:<?php echo $color_info; } ?>">

					  <td><?php echo $count; ?></td>

					  <td> {{ $datas->emp_id  }} </td>

					   <td>

					  	<?php if($total_time != 0){ ?> 

					  	<div class="container">

							  <div class="round">

							    <input class="astime_id" type="checkbox"  <?php if($approved_time == $total_time){ echo "checked"; } ?>  id="checkbox_{{ $datas->id  }}" name="app_all[]" data-val_add="<?php if($approved_time == $total_time){ echo 1; }else{ echo 0;} ?>" class="app_all" data-baseURL="{{ url('/') }}" data-uid="{{ $datas->id  }}" data-frmdt="{{ $frm_date }}" data-todt="{{ $t_date }}" data-ttime="{{ $total_time }}"/>

							    <label for="checkbox_{{ $datas->id  }}" data-val_add="<?php if($approved_time == $total_time){ echo 1; }else{ echo 0;} ?>" class="app_all" data-baseURL="{{ url('/') }}" data-uid="{{ $datas->id  }}" data-frmdt="{{ $frm_date }}" data-todt="{{ $t_date }}" data-ttime="{{ $total_time }}"></label>

							  </div>

							</div>

							<?php } ?>

						</td> 

					  <td>

					  <?php if(isset($ssearch_by_comp)){ ?>

						<a  style="margin-left: 5px;"  href="{{ url('/suser/timesheets') }}/{{ $datas->id  }}/{{ $frm_date }}/{{ $t_date }}/{{ $ssearch_by_comp }}" title="Time Sheets"><i class="fa fa-book"></i></a>

						<?php }else{ ?>

						<a  style="margin-left: 5px;"  href="{{ url('/suser/timesheets') }}/{{ $datas->id  }}/{{ $frm_date }}/{{ $t_date }}" title="Time Sheets"><i class="fa fa-book"></i></a>

						<?php } ?>

						<ul class="imp_actions">

							

								<li>

								<a  data-toggle="modal" data-target="#myModal" data-uid="{{ $datas->id }}" data-todt="{{ $t_date }}" data-frmdt="{{ $frm_date }}" data-baseURL="{{ url('/') }}" id="nttime_view"><img src="{{ url('public/assets/images/view.png') }}"></a>

								</li>

								<!-- <li><a data-uid="{{ $datas->id }}" data-todt="{{ $t_date }}" data-frmdt="{{ $frm_date }}" data-baseURL="{{ url('/') }}" id="nttime_approve"><img src="{{ url('assets/images/check.png') }}"></a></li>

								<li><a data-uid="{{ $datas->id }}" data-todt="{{ $t_date }}" data-frmdt="{{ $frm_date }}" data-baseURL="{{ url('/') }}" id="nttime_decline"><img src="{{ url('assets/images/decline.png') }}"></a></li>

								<li><a data-uid="{{ $datas->id }}" data-todt="{{ $t_date }}" data-frmdt="{{ $frm_date }}" data-baseURL="{{ url('/') }}" id="nttime_delete"><img src="{{ url('assets/images/delete.png') }}"></a></li> -->

							</ul>

					 </td>

					 	<!-- <td>

							<ul class="imp_actions">

							

								<li>

								<a  data-toggle="modal" data-target="#myModal" data-uid="{{ $datas->id }}" data-todt="{{ $t_date }}" data-frmdt="{{ $frm_date }}" data-baseURL="{{ url('/') }}" id="nttime_view"><img src="{{ url('public/assets/images/view.png') }}"></a>

								</li>

								<li><a data-uid="{{ $datas->id }}" data-todt="{{ $t_date }}" data-frmdt="{{ $frm_date }}" data-baseURL="{{ url('/') }}" id="nttime_approve"><img src="{{ url('public/assets/images/check.png') }}"></a></li>

								<li><a data-uid="{{ $datas->id }}" data-todt="{{ $t_date }}" data-frmdt="{{ $frm_date }}" data-baseURL="{{ url('/') }}" id="nttime_decline"><img src="{{ url('public/assets/images/decline.png') }}"></a></li>

								<li><a data-uid="{{ $datas->id }}" data-todt="{{ $t_date }}" data-frmdt="{{ $frm_date }}" data-baseURL="{{ url('/') }}" id="nttime_delete"><img src="{{ url('public/assets/images/delete.png') }}"></a></li>

							</ul>



						</td> -->

					<td style="text-align: left;"> {{ $datas->first_name  }} {{ $datas->last_name  }}</td>

					 <td > <?php $user_companies = UserssController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>

					  

					   <td> @if($datas->last_login_at != null) {{ date('h:i a', strtotime($datas->last_login_at)) }} @endif</td>

					   <td> @if($datas->last_login_at != null) {{ date('M d, Y', strtotime($datas->last_login_at)) }} @endif</td>

					  <td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>

					  <td> <?php if($datas->status == 1){ echo "<h5 style='color:green'>Active</h5>"; }else{ echo "<h5 style='color:red'>Inactive</h5>"; } ?></td>

					   <td><?php $total_time = UserssController::total_time($datas->id,$frm_date,$t_date); 

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

					   <td><?php $approved_time = UserssController::approved_time($datas->id,$frm_date,$t_date); 

					  if($approved_time <=  79){

								echo "<p id='ap_t_".$datas->id."' style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_time."</p>";

						}elseif($approved_time ==  80){

								echo "<p id='ap_t_".$datas->id."' style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_time."</p>";

						}else{

						   echo "<p id='ap_t_".$datas->id."' style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_time."</p>";

						}

					   

					   ?></td>

					    <td><?php $denied_time = UserssController::denied_time($datas->id,$frm_date,$t_date); 

						if($denied_time <=  80){

								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_time."</p>";

						}elseif($denied_time ==  80){

								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_time."</p>";

						}else{

						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_time."</p>";

						}

						?></td>



						<?php //$vacc_report = UserssController::vacc_report($datas->id); ?>

						<?php 

		// 								if(isset($vacc_report) && !empty($vacc_report)){

		// 										echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'>".$vacc_report[0]."</td>";

		// 										echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'>".date('M d', strtotime($vacc_report[1]))."-".date('M d, Y', strtotime($vacc_report[2]))."</td>";

		// 											if($vacc_report[3]==0){echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'><p style='padding: 10px;

    // text-align: left;

    // color: #000;

    // background: yellow;

    // font-size: 16px;

    // font-weight: bold;

    // border-radius: 10px;'>Pending</p></td>";}elseif($vacc_report[3]==1){echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'> <p style='padding: 10px;

    // text-align: left;

    // color: #fff;

    // background: green;

    // font-size: 16px;

    // font-weight: bold;

    // border-radius: 10px;'>Approved</p></td>";}elseif($vacc_report[3]==2){echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'><p style='padding: 10px;

    // text-align: left;

    // color: #fff;

    // background: red;

    // font-size: 16px;

    // font-weight: bold;

    // border-radius: 10px;'>Decline</p></td>";}else{echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'><p style='padding: 10px;

    // text-align: left;

    // color: #000;

    // background: yellow;

    // font-size: 16px;

    // font-weight: bold;

    // border-radius: 10px;'>Pending</p></td>";}

												



		// 								}

					?></td>

						<!--td><input type="checkbox"    name="nttime_approve" ></td>

						<td><input type="checkbox"    name="nttime_decline" ></td>

						<td><input type="checkbox"    name="nttime_delete" ></td-->



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

	<!-- Modal -->

  <div class="modal fade" id="myModal" role="dialog">

    <div style="max-width: 95%;" class="modal-dialog">

    

      <!-- Modal content-->

      <div class="modal-content">

        <div style="    display: flex;

    flex-flow: column;" class="modal-header">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Timesheet</h4>

        </div>

        <div class="modal-body">

          <table id="sortable-table-1" class="table dataTable table-striped table-responsive">

			  <thead>

				<tr>

				

				  <th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>

				  <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>

				    <?php if(!empty($cm_check)){ ?>

				  <th class="sortStyle unsortStyle"> Case Manager <i class="mdi mdi-chevron-down"></i> </th-->

					<?php } ?>

				   <!--th> Actions </th-->

				   

				  <!--th><a  style="    color: #fff" data-baseURL="{{ url('/') }}" id="spapprove_all" class="btn btn-success">Click<br>to<br>Approve</a><br><input style="    margin-top: 10px;" type="checkbox" id="time_approve" name="time_approve" ></th>

				 <th><a  style="    color: #fff" data-baseURL="{{ url('/') }}" id="spdecline_all" class="btn btn-success">Click<br>to<br>Decline</a><br><input     style="    margin-top: 10px;" type="checkbox" id="time_decline" name="time_decline" ></th>

				 <th><a  style="    color: #fff" data-baseURL="{{ url('/') }}" id="spdelete_all" class="btn btn-success">Click<br>to<br>Delete</a><br><input     style="    margin-top: 10px;" type="checkbox" id="time_delete" name="time_delete" ></th-->

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

						  <th > Approved<br>By </th>

				  <th > User Added<br>Hours At </th>

				   <th > Approved<br>At </th>

				</tr>

				

			  </thead>

			  <tbody id="result2">

			  </tbody>

			</table>

        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

        </div>

      </div>

      

    </div>

  </div>





	</div>

@endsection		

