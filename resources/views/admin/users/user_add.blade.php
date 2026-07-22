@extends('layouts.master')

@section('content')
<?php 
$u_role = "user";
if(isset($_GET['u'])){
	$u_role =	$_GET['u'];
}

?>
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
                    <h4 class="card-title">Create New User</h4>
                    <p style="display:none" class="card-description"> Basic form elements </p>
                    <form method="POST" action="{{ url('/users/store') }}" class="forms-sample" enctype="multipart/form-data">
					@csrf
						
					   <div class="form-group">
				<label for="name">Username</label>
				<input type="text" class="form-control" id="username" name="username" placeholder="Username" >
			  </div>
                      <div class="form-group">
                        <label for="name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name">
                      </div>
					  <div class="form-group">
                        <label for="name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name">
                      </div>
                       <div class="form-group">
												<label for="hours">Date Of Birth</label>
												<input type="text" class="form-control" id="hours_day" name="hours_day" placeholder="">
											  </div>
                      <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                      </div>
                      <?php if($u_role != "supervisor"){ ?>
                       <div class="form-group">
                        <label for="name">Phone No.</label>
                        <input type="text" class="form-control" id="phone_no" name="phone_no" placeholder="Phone No.">
                      </div>
                      <div class="form-group">
                        <label for="email">SSN No.</label>
                        <input type="text" class="form-control" id="ssn_no" name="ssn_no" placeholder="SSN No.">
                      </div>
                       <div class="form-group">
                        <label for="email">Child Sup</label>
                        <input type="text" class="form-control" id="child_sup" name="child_sup" placeholder="$100">
                      </div>
                       <div class="form-group">
                        <label for="email">Health Insurance</label>
                        <input type="text" class="form-control" id="health_insurance" name="health_insurance" placeholder="$100">
                      </div>
                      <?php } ?>
					  <div class="form-group">
						  <label for="role">Role</label>
						  <select class="form-control form-control-lg" id="role" name="role">
							<option value="user" >User</option>
							<option value="casemanager">Case Manager</option>
							<option  <?php if($u_role == "supervisor"){ echo "selected";  }?> value="supervisor">Supervisor</option>
						  </select>
                      </div>
					  <div class="form-group">
						  <label for="role">Status</label>
						  <select class="form-control form-control-lg" id="status" name="status">
							<option value="1" >Active</option>
							<option value="0">Inactive</option>
						  </select>
                      </div>
                      <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                      </div>
					  <div class="form-group">
                        <label for="confirmed">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmed" name="confirmed" placeholder="Confirm Password">
                      </div>
                      <?php if($u_role != "supervisor"){ ?>
					   <div class="form-group">
                        <label for="company_id">Select Department</label>
                        <select class="form-control form-control-lg" id="dept" name="dept">
							<option disabled value="0">Select Department</option>
							  @if($department->count() != 0)
								@foreach ($department as $departments)
									<option value="{{ $departments->department }}" >{{ $departments->department }}</option>
								@endforeach
							  @endif
						 </select>
                      </div>
                      <?php } ?>
					   <div  id="multiple_comp" class="form-group">
                        <label for="company_id">Select Company</label>
                        <select  class="form-control form-control-lg" id="company_id" name="companys_id[]">
							<option disabled value="0">Select Company</option>
							  @if($companies->count() != 0)
								@foreach ($companies as $company)
									<option value="{{ $company->id }}" >{{ $company->company }}</option>
								@endforeach
							  @endif
						 </select>
                      </div>
                       <?php if($u_role != "supervisor"){ ?>
					  <div class="form-group">
                        <label for="name">Add Hours Rate($)</label>
                        <input type="text" class="form-control" id="hour_rate" name="hour_rate" placeholder="Hourly Rate">
                      </div>
					  <div class="form-group">
					  <label for="name">Upload Driver License</label>
						<span >(File size must be max 5MB)</span>
						<input type="file" name="driving_license" class="form-control">
					  </div>
					   <?php } ?>
                      <button type="submit" class="btn btn-success mr-2">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
<div class="loding"></div>
@endsection		
