@extends('layouts.master')
@section('content') 
<div class="content-wrapper">
@if(\Session::has('Pass_Success'))
		<div class="alert alert-success">
			<h4>{{\Session::get('Pass_Success')}}</h4>
		</div>
	@endif
			<div class="row">
				@if (count($errors) > 0)
				<div class="alert alert-danger">
					<strong>Whoops!</strong> There were some problems with your input.<br><br>
					<ul>
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
				@endif
			</div>
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Search Timesheet by PayPeriod</h4>
                    <p style="display:none" class="card-description"> Basic form elements </p>
                    <form method="POST" action="{{ url('/all/payperiod/search/postdata') }}" class="forms-sample">
					@csrf
					    <div class="form-group">
                            <label for="payperiod" >Export Timesheet by Payperiod</label>
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
											<option style="color: #0aab52; font-weight: bold; font-size: 16px;" value="<?php echo $payperiods_date->payperiod_value; ?>"> <?php echo "<p>".date('d-M-Y', strtotime($xfrom_date))."-".date('d-M-Y',strtotime($xto_date))."               |  ".date('d-M-Y', strtotime('+5 days', strtotime($xto_date)))."</p>"; ?></option>
									 <?php } else{ ?>
									<option style="color: #f53838; font-weight: bold; font-size: 14px;" value="<?php echo $payperiods_date->payperiod_value; ?>"><?php echo "<p>".date('d-M-Y', strtotime($xfrom_date))."-".date('d-M-Y',strtotime($xto_date))."               |  ".date('d-M-Y', strtotime('+5 days', strtotime($xto_date)))."</p>"; ?></option>
									 <?php } ?><?php } ?>
							<?php } ?>
								
								
							</select>
                        </div>
						<button type="submit" class="btn btn-success mr-2">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

@endsection




