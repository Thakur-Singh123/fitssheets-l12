@extends('layouts.supervisor')
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
                    <form method="POST" action="{{ url('/all/suser/payperiod/search/postdata') }}" class="forms-sample">
					@csrf
					    <div class="form-group">
                            <label for="payperiod" >Export Timesheet by Payperiod</label>
                            <select class="form-control form-control-lg" id="payperiod" name="payperiod">
								<?php if(isset($payperiods_dates)){ ?>
								<?php foreach($payperiods_dates as $payperiods_date){?>
									<option value="<?php echo $payperiods_date->payperiod_value; ?>"><?php echo $payperiods_date->payperiod; ?></option>
								<?php } ?>
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




