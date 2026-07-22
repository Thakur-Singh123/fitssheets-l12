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
                    <h4 class="card-title">Update Payperiod</h4>
                    <p style="display:none" class="card-description"> Basic form elements </p>
                    @if($data->count() != 0)
					@foreach ($data as $datas)
					<form  method="POST" action="{{ url('/payperiods/update') }}">
					@csrf
					<input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}" >
					<div class="form-group">
                        <label for="house_add">Payperiod Title</label>
						<span>(Like this 22-Jun to 5-Jul-20)</span>
						<input type="text" class="form-control"  id="payperiod_title" name="payperiod_title" value="{{ $datas->payperiod }}">
						
                      </div>
					  <div class="form-group">
                        <label for="house_add">Payperiod Value</label>
						<span>(Like this 2020_06_22-2020_07_05)</span>
						<input type="text" class="form-control"  id="payperiod_value" name="payperiod_value" value="{{ $datas->payperiod_value }}">
						
                      </div>
                     <div class="form-group">
                        <label for="company_id">Select Company</label>
                       <select class="form-control show-tick" id="company_id" name="company_id">
								<option disabled value="0">Select Company</option>
									@if($companies->count() != 0)
									@foreach ($companies as $company)
										<option <?php if($datas->companies_id == $company->id){ echo "selected"; } ?> value="{{ $company->id }}" >{{ $company->company }}</option>
									@endforeach
									@endif
							</select>
                      </div>
                      
                      <button type="submit" class="btn btn-success mr-2">Submit</button>
                    </form>
					@endforeach
			@else
			<p>Sorry No Data!!</p>
			@endif
                  </div>
                </div>
              </div>
            </div>
          </div>
<div class="loding"></div>


@endsection		
