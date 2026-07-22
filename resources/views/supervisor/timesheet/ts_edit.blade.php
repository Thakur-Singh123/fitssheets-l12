@extends('layouts.supervisor')
@section('content')
<div class="content-wrapper">
	<div class="row">
		@if(session('success'))
		<div class="alert alert-success">
			<h4>{{ session('success') }}</h4>
		</div>
		@endif
		@if(session('error'))
		<div class="alert alert-danger">
			<h4>{{ session('error') }}</h4>
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
					<form method="POST" action="{{ url('/suser/update/timesheets') }}" class="forms-sample">
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
							@error('company_id')
							<small class="validation-error">
								{{ $message }}
							</small>
							@enderror
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
							@error('house_id')
							<small class="validation-error">
								{{ $message }}
							</small>
							@enderror
						</div>
						<div class="form-group">
							<label for="date">Select Date</label>
							<input type="text" class="form-control" value="{{ $datas->hours_day }}" id="hours_day" name="hours_day" >
						@error('hours_day')
							<small class="validation-error">
								{{ $message }}
							</small>
							@enderror
						</div>
						<div class="form-group">
							<label for="hours">Time In</label>
							<input type="text" class="form-control" value="{{ $datas->time_in }}" id="time_in" name="time_in" placeholder="">
							@error('time_in')
							<small class="validation-error">
								{{ $message }}
							</small>
							@enderror
						</div>
						<div class="form-group">
							<label for="hours">Time Out</label>
							<input type="text" class="form-control" value="{{ $datas->time_out }}" id="time_out" name="time_out" placeholder="">
						@error('time_out')
							<small class="validation-error">
								{{ $message }}
							</small>
							@enderror
						</div>
						<div class="form-group">
							<label for="company_id">Approved</label>
							<select class="form-control form-control-lg" id="approved" name="approved">
								<option value="0">Select Status</option>
								<option <?php if($datas->approve == 2){ echo "selected"; } ?> value="2" >Yes</option>
								<option <?php if($datas->approve == 1){ echo "selected"; } ?> value="1" >No</option>
							</select>
						</div>
						<div class="form-group" id="remarks">
							<label for="notes">Remarks</label>
							<input type="text" class="form-control" value="{{ $datas->remarks }}" id="remarks" name="remarks" placeholder="">
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>      

<script>
$(document).ready(function(){
	
	$('#approved').on('change', function() {
  if ( this.value == '2')
    $("#remarks").hide();     
  else
    $("#remarks").show();
}).trigger("change");
	
	
   
});
</script> 
@endsection		
