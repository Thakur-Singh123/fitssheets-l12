@extends('layouts.master')

@section('content')
<div class="content-wrapper">
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
                    <h4 class="card-title">Create New Payperiod</h4>
                    <p style="display:none" class="card-description"> Basic form elements </p>
                    <form method="POST" action="{{ url('/payperiods/store') }}" class="forms-sample">
					@csrf<?php /*
					<div class="form-group">
                        <label for="house_add">Payperiod Title</label>
						<span>(Like this 22-Jun to 5-Jul-20)</span>
						<input type="text" class="form-control"  id="payperiod_title" name="payperiod_title" placeholder="Payperiod Title">
						
                      </div>
					  <div class="form-group">
                        <label for="house_add">Payperiod Value</label>
						<span>(Like this 2020_06_22-2020_07_05)</span>
						<input type="text" class="form-control"  id="payperiod_value" name="payperiod_value" placeholder="Payperiod Value">
						
                      </div> */?>
                     <div class="form-group">
                        <label for="company_id">Select Company</label>
                        <select class="form-control form-control-lg" id="company_id" name="company_id">
							<option disabled selected value="none">Select Company</option>
							  @if($companies->count() != 0)
								@foreach ($companies as $company)
									<option value="{{ $company->id }}" >{{ $company->company }}</option>
								@endforeach
							  @endif
						 </select>
                      </div>
                     
                      <button type="submit" class="btn btn-success mr-2">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
<div class="loding"></div>

@endsection		
