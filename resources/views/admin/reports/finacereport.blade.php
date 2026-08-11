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
				<div class="col-md-4">
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">Search User</label>
					<div class="col-sm-9">
					  <input type="text" class="form-control" id="srch_user" name="srch_user">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				 <button data-baseURL="{{ url('/') }}" id="submit_user" type="submit" class="btn btn-success mr-2">Submit</button>
				 </div>
				 		<div class="col-md-6">
				  <div class="form-group row">
					<label  style="font-size: 13px;" class="col-sm-3 col-form-label">Search User By Approve/Decline</label>
					<div class="col-sm-9">
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
		<div class="row grid-margin stretch-card">
              <div class="col-md-6 ">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Select payperiod</h4>
                    
					    <div class="form-group">
                            <select class="form-control form-control-lg" id="payperiod" name="payperiod">
								<?php if(isset($payperiods_dates)){ ?>
								<?php foreach($payperiods_dates as $payperiods_date){?>
									
									
									
											<?php 
												$TodayDate = new DateTime();	
												$bet_dates = explode('-',$payperiods_date->payperiod_value);
												if(isset($bet_dates)){
													$from_date    = $bet_dates[0];
													$to_date    = $bet_dates[1];
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
											<option style="color: #0aab52; font-weight: bold; font-size: 16px;" value="<?php echo $payperiods_date->payperiod_value; ?>"> <?php echo "<p>".date('d-M-Y', strtotime($xfrom_date))."-".date('d-M-Y',strtotime($xto_date))."               |  <b >".date('d-M-Y', strtotime('+5 days', strtotime($xto_date)))."</b></p>"; ?></option>
									 <?php } else{ ?>
									<option style="color: #f53838; font-weight: bold; font-size: 14px;" value="<?php echo $payperiods_date->payperiod_value; ?>"><?php echo "<p>".date('d-M-Y', strtotime($xfrom_date))."-".date('d-M-Y',strtotime($xto_date))."               |  <b >".date('d-M-Y', strtotime('+5 days', strtotime($xto_date)))."</b></p>"; ?></option>
									 <?php } ?>
									
								<?php } ?>
							<?php } ?>
								
							</select>
                        </div>
						
                   
                  </div>
                </div>
              </div>
			                <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Select company</h4>
                    
					    <div class="form-group">
							<select  id="search_by_comp" class="form-control" name="search_by_comp" >
								<option value="0" >Select</option>
								@if($companies->count() != 0)
									@foreach ($companies as $company)
										<option value="{{ $company->id }}" >{{ $company->company }}</option>
									@endforeach
								  @endif
							  </select>
                        </div>                  
                  </div>
                </div>
              </div>
			  <button data-baseURL="{{ url('/') }}" id="payroll" type="button" class="btn btn-success mr-2">Submit</button>
            </div>
</form>
			<div style="    margin-top: 10px;" align="left">
				<a href="{{ url('/user/export/all') }}" id="ecsv" class="btn btn-success">Export Excel Report</a>
		    </div>
		<table id="results" class="table table-striped table-responsive">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Last Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> First Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
				 
				   <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle">Total Hours<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Hourely Rate($)<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle">Billed $18<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Total Pay<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Total Billed<i class="mdi mdi-chevron-down"></i></th>
				  <th class="sortStyle unsortStyle"> Profit<i class="mdi mdi-chevron-down"></i></th>
				 
				</tr>
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
					<tr>
					  <td><?php echo $count; ?></td>
					   <td> {{ $datas->emp_id  }} </td>
					    <td> {{ $datas->last_name  }} </td>
					   <td> {{ $datas->first_name  }} </td>
					  <td> {{ $datas->name  }} </td>
<td > <?php $user_companies = AdminController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
					   <td>{{$datas->name}}</td>
					  <td> {{ $datas->hourst_rate  }} </td>
					   
					</tr>
				<?php $count++; ?>
				@endforeach
				
				@else
					<p>Sorry No Data!!</p>
				@endif
			  </tbody>
			</table>
<div class="loding"></div>
	</div>
@endsection		