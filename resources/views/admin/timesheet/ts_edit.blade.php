@extends('layouts.master')
@section('content')
<style>
/*Popup container - can be anything you want*/
.validation-error,
small.validation-error,
label.error{
  display: block;
  margin-top: 6px;
  color: #ff1b15;
  font-size: 12px;
  font-weight: 500;
}
.popup {
  position: relative;
  display: inline-block;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
/*The actual popup*/
.popup .popuptext {
  visibility: hidden;
  width: 700px;
  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 8px 0;
  position: absolute;
  z-index: 1;
  bottom: 125%;
  left: 50%;
  margin-left: -80px;
}
/*Popup arrow*/
.popup .popuptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 15%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}
/*Toggle this class - hide and show the popup */
.popup .show {
  visibility: visible;
  -webkit-animation: fadeIn 1s;
  animation: fadeIn 1s;
}
/*Add animation (fade in the popup)*/
@-webkit-keyframes fadeIn {
  from {
    opacity: 0;
  } 
  to {
    opacity: 1;
  }
  }
  @keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity:1 ;
  }
}
</style>
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
			<h4 class="card-title">Edit User</h4>
			<p style="display:none" class="card-description"> Basic form elements </p>
			<form method="POST" action="{{ url('/user/update/timesheets') }}" class="forms-sample">
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
                        <label for="house_id">Select House</label>
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
					  <div class="form-group">
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
			<p class="no-data">Sorry no data found!</p>
		@endif
	</div>
  </div>
@endsection		
