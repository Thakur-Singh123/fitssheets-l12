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
@if($data->count() != 0)
			@foreach ($data as $datas)
	  <div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		  <div class="card-body">
			<p style="display:none" class="card-description"> Basic form elements </p>
			<form method="POST" action="{{ url('/user/suser/update/timesheets') }}" class="forms-sample">
			@csrf
				<input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}" >
				<input type="hidden" id="user_id" name="user_id" class="form-control" value="{{ $datas->users_id }}" >
				 				  <div class="form-group">
					<label for="company_id">Select Company-house</label>
					<select class="form-control form-control-lg" id="company_id" name="company_id">
						<option disabled value="0">Select Company(House)</option>
						  @if($companies->count() != 0)
							@foreach ($companies as $company)
								<option <?php if($datas->companies_id == $company->id){ echo "selected"; } ?> value="{{ $company->id }}" >{{ $company->company }}</option>
							@endforeach
						  @endif
					 </select>
				  </div>
				  <div class="form-group">
                        <label for="company_id">Select House</label>
                        <select class="form-control form-control-lg" id="house_id" name="house_id">
							<option disabled value="0">Select House</option>
							  @if($houses->count() != 0)
								@foreach ($houses as $house)
									<option <?php if($datas->houses_id == $house->id){ echo "selected"; } ?> value="{{ $house->id }}" >{{ $house->house_add }}</option>
								@endforeach
							  @endif
						 </select>
                      </div>
				  <div class="form-group">
					<label for="date">Select Date</label>
					<input type="text" class="form-control" value="{{ $datas->hours_day }}" id="hours_day" name="hours_day" >
				  </div>
				  <div class="form-group">
					<label for="hours">Time In</label>
					<input type="text" class="form-control" value="{{ $datas->time_in }}" id="time_in" name="time_in" placeholder="">
				  </div>
				  <div class="form-group">
					<label for="hours">Time Out</label>
					<input type="text" class="form-control" value="{{ $datas->time_out }}" id="time_out" name="time_out" placeholder="">
				  </div>
				   <div class="form-group">
                        <label for="company_id">Approved</label>
                        <select class="form-control form-control-lg" id="approved" name="approved">
							<option value="0">Select Status</option>
							<option <?php if($datas->approve == 2){ echo "selected"; } ?> value="2" >Yes</option>
							<option <?php if($datas->approve == 1){ echo "selected"; } ?> value="1" >No</option>
						 </select>
                      </div>
				  <div style="display:none" class="form-check">
					<label class="form-check-label">
					<input type="checkbox" class="form-check-input" <?php if($datas->vacation_status == 1){ echo "checked"; } ?> id="vacc" name="vacc" > Vacation Status <i class="input-helper"></i></label>
				  </div>  
				 <button type="submit" class="btn btn-success mr-2">Update</button>
			</form>
		  </div>
		</div>
	  </div>
				@endforeach
		@else
			<p>Sorry No Data!!</p>
		@endif
	</div>
  </div>

@endsection		
