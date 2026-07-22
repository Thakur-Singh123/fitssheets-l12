@extends('layouts.supervisor')

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
                    <h4 class="card-title">Add New Hours</h4>
                    <p style="display:none" class="card-description"> Basic form elements </p>
                    <form id="time_sheet" method="POST" action="{{ url('/suser/store/timesheets') }}" class="forms-sample">
					@csrf
					<input type="hidden" id="user_id" name="user_id" class="form-control" value="{{ $user }}" >
                                           <div class="form-group">
                        <label for="company_id">Select Company</label>
                        <select class="form-control form-control-lg" id="company_id" name="company_id">
							<option disabled value="0">Select House</option>
							  @if($companies->count() != 0)
								@foreach ($companies as $company)
									<option value="{{ $company->id }}" >{{ $company->company }}</option>
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
									<option value="{{ $house->id }}" >{{ $house->house_add }}</option>
								@endforeach
							  @endif
						 </select>
                      </div>
					  
                      <div class="form-group">
                        <label for="date">Select Date</label>
                        <input type="text" class="form-control" id="hours_day" name="hours_day" >
                      </div>
					<div class="form-group">
						<label for="hours">Time In</label>
						<input type="text" class="form-control" id="time_in" name="time_in" placeholder="">
					  </div>
					  <div class="form-group">
						<label for="hours">Time Out</label>
						<input type="text" class="form-control" id="time_out" name="time_out" placeholder="">
					  </div>
					  <div class="form-group">
                        <label for="company_id">Approved</label>
                        <select class="form-control form-control-lg" id="approved" name="approved">
							<option value="0">Select Status</option>
							<option value="2" >Yes</option>
							<option value="1" >No</option>
						 </select>
                      </div>
					  <div class="form-group" id="remarks">
						<label for="notes">Remarks</label>
						<input type="text" class="form-control" id="remarks" name="remarks" placeholder="">
					  </div>
					  <div style="display:none" class="form-check">
                              <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="vacc" name="vacc" > Vacation Status <i class="input-helper"></i></label>
                      </div>
					   
                      <button type="submit" class="btn btn-success mr-2">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
<div class="loding"></div>
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
