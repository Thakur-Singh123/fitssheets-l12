@extends('layouts.master')

@section('content')
<style>
/* Popup container - can be anything you want */
.popup {
  position: relative;
  display: inline-block;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* The actual popup */
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

/* Popup arrow */
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

/* Toggle this class - hide and show the popup */
.popup .show {
  visibility: visible;
  -webkit-animation: fadeIn 1s;
  animation: fadeIn 1s;
}

/* Add animation (fade in the popup) */
@-webkit-keyframes fadeIn {
  from {opacity: 0;} 
  to {opacity: 1;}
}

@keyframes fadeIn {
  from {opacity: 0;}
  to {opacity:1 ;}
}
</style>
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
			<h4 class="card-title">Edit Issues</h4>
			<p style="display:none" class="card-description"> Basic form elements </p>
			<form method="POST" action="{{ url('/lists-issue/update') }}" class="forms-sample">
			@csrf
				<input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}" >
				<input type="hidden" id="user_id" name="user_id" class="form-control" value="{{ $user }}" >
              <?php /*				
				<div class="form-group">
					<label for="company_id">Select Company-house</label>
					<select data-baseURL="{{ url('/') }}" class="form-control form-control-lg" id="company_idu" name="company_id">
						<option disabled value="0">Select Company(House)</option>
						  @if($companies->count() != 0)
							@foreach ($companies as $company)
								<option <?php if($datas->companies_id == $company->id){ echo "selected"; } ?> value="{{ $company->id }}" >{{ $company->company }}</option>
							@endforeach
						  @endif
					 </select>
				  </div>
				  
				  
				  <div class="form-group">
					<label for="name">Name</label>
					<input type="text" class="form-control" value="{{ $datas->name }}" id="name" name="name" placeholder="">
				  </div>
				  <div class="form-group">
					<label for="ssn">SSN</label>
					<input type="text" class="form-control" value="{{ $datas->ssn }}" id="ssn" name="ssn" placeholder="">
				  </div>
				  */?>
				  <div class="form-group">
					<label for="issue">Issue</label>
					<br>
					<textarea rows="4" cols="50" id="issue" name="issue" >{{ $datas->issue }}</textarea>
					 <?php /*				
					<input type="text" class="form-control" value="{{ $datas->issue }}" id="issue" name="issue" placeholder="">
					 */?>
				  </div>
				  <div class="form-group">
				  <label for="resolution_remarks">Resolution Remarks</label>
				  <input type="text" class="form-control" value="{{ $datas->resolution_remarks }}" id="resolution_remarks" name="resolution_remarks" placeholder="">
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
// When the user clicks on div, open the popup
function myFunction() {
  var popup = document.getElementById("myPopup");
  popup.classList.toggle("show");
}
</script>
@endsection		
