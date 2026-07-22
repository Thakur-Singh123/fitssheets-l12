@extends('layouts.user')
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
/* Add animation (fade in the popup) */
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
				<h4 class="card-title">Edit Hours</h4>
				<p style="display:none" class="card-description"> Basic form elements </p>
				<form method="POST" action="{{ url('/time-sheets/update') }}" class="forms-sample">
					@csrf
					<input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}" >
					<input type="hidden" id="user_id" name="user_id" class="form-control" value="{{ $user }}" >
					<div class="form-group">
						<label for="company_id">Select Company-house</label>
						<select data-baseURL="{{ url('/') }}" class="form-control form-control-lg" id="company_idu" name="company_id">
							<option disabled value="0">Select Company(House)</option>
							@if($companies->count() != 0)
								@foreach ($companies as $company)
								<option <?php if($datas->companies_id == $company->id) { echo "selected"; } ?> value="{{ $company->id }}" >
									{{ $company->company }}
								</option>
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
					<div class="form-check">
						<label class="form-check-label">
						<label class="form-check-label">
							<!--a target="blank" href="https://www.loom.com/share/804f9b1cf2c8468081fd4e719c29d077" style="color: green;
							font-size: 18px;
							margin-left: -30px;
							margin-top: 15px;
							margin-bottom: 15px;
							text-decoration: underline;" class="popup">How to apply for vacation?</a-->
						</label>
					</div>
					<button type="submit" class="btn btn-success mr-2 submit_data">Update</button>
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
<div class="loding"></div>
<script>
//When the user clicks on div, open the popup
function myFunction() {
	var popup = document.getElementById("myPopup");
	popup.classList.toggle("show");
}
</script>
@endsection