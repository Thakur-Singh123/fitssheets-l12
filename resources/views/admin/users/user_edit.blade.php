@extends('layouts.master') 
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
					<h4 class="card-title">Edit Details</h4> 
					<p style="display:none" class="card-description">Basic form elements</p> 
					<form method="POST" action="{{ url('/users/update') }}" class="forms-sample" enctype="multipart/form-data"> 
					@csrf 
					<input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}"> 
					<div class="row">
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="name">Employee ID</label><br> 
								<input type="text" class="form-control" id="emp_id" name="emp_id" value="{{ $datas->emp_id }}" placeholder="Employee ID">
							</div> 
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="name">Username</label> 
								<input type="text" class="form-control" id="username" name="username" value="{{ $datas->username }}" placeholder="Username">
							</div> 
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="name">First Name</label> 
								<input type="text" class="form-control" id="first_name" name="first_name" value="{{ $datas->first_name }}" placeholder="First Name">  
							    @error('first_name')
								<small class="validation-error">
									{{ $message }}
								</small>
								@enderror
							</div> 
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="name">Last Name</label> 
								<input type="text" class="form-control" id="last_name" name="last_name" value="{{ $datas->last_name }}" placeholder="Last Name"> 
							</div> 
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="hours">Date Of Birth</label> 
								<input type="text" class="form-control" value="{{ $datas->dob }}" id="hours_day" name="hours_day" placeholder="2026-08-20">  
							</div> 
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="email">Email address</label> 
								<input type="email" class="form-control" value="{{ $datas->email }}" id="email" name="email" placeholder="Email"> 
								@error('email')
								<small class="validation-error">
									{{ $message }}
								</small>
								@enderror
							</div> 
						</div>
						<?php if($datas->role != "supervisor"){ ?> 
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="name">Phone No</label> 
								<input type="text" class="form-control" id="phone_no" name="phone_no" value="{{ $datas->phone_no }}" placeholder="Phone No">
							</div> 
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="email">SSN No</label> 
								<input type="text" class="form-control" id="ssn_no" name="ssn_no" value="{{ $datas->ssn_no }}" placeholder="SSN No"> 
							</div> 
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="email">Child Sup</label> 
								<input type="text" class="form-control" id="child_sup" name="child_sup" value="{{ $datas->child_sup }}" placeholder="$100">  
							</div> 
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="email">Health Insurance</label> 
								<input type="text" class="form-control" id="health_insurance" name="health_insurance" value="{{ $datas->health_insurance }}" placeholder="$100">  
							</div> 
						</div>
						<?php } ?> 
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="role">Role</label> 
								<select class="form-control form-control-lg" id="role" name="role"> 
									<option <?php if($datas->role == "user"){ echo "selected"; } ?> value="user">User</option> 
									<option <?php if($datas->role == "casemanager"){ echo "selected"; } ?> value="casemanager">Case Manager</option> 
									<option <?php if($datas->role == "supervisor"){ echo "selected"; } ?> value="supervisor">Supervisor</option> 
									@error('role')
									<small class="validation-error">
										{{ $message }}
									</small>
									@enderror
								</select> 
							</div> 
						</div>
						<?php if($datas->role == "supervisor"){ ?> 
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="email">Supervisor Color</label> 
								<input type="color" class="form-control" id="color_field" name="color_field" value="{{ $datas->color_field }}"> 
							</div> 
						</div>
						<?php } ?> 
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="role">Status</label> 
								<select class="form-control form-control-lg" id="status" name="status"> 
									<option <?php if($datas->status == "1"){ echo "selected"; } ?> value="1">Active</option> 
									<option <?php if($datas->status == "0"){ echo "selected"; } ?> value="0">Inactive</option> 
								</select> 
							</div> 
						</div>
						<div class="col-md-6">
							<div id="multiple_comp" class="form-group"> 
								<label for="company_id">Select Company</label> 
								<select class="form-control form-control-lg" id="company_id" name="companys_id[]"> 
									<option disabled value="0">Select Company</option> 
									@if($companies->count() != 0) 
										@foreach ($companies as $company) 
											<option <?php if($company_id->count() != 0){ foreach ($company_id as $company_ids){ if($company_ids->users_id == $company->id){ echo "selected"; } } } ?> value="{{ $company->id }}">
												{{ $company->company }}
											</option> 
										@endforeach 
									@endif 
								</select> 
							</div> 
						</div>
						<?php if($datas->role != "supervisor"){ ?> 
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="dept">Select Department</label> 
								<select class="form-control form-control-lg" id="dept" name="dept"> 
									<option disabled value="0">Select Department</option> 
									@if($department->count() != 0) 
										@foreach ($department as $departments) 
											<option <?php if($datas->dept == $departments->department){ echo "selected"; } ?> value="{{ $departments->department }}">
												{{ $departments->department }}
											</option> 
										@endforeach 
									@endif 
								</select> 
							</div> 
						</div>
						<?php } ?> 
						<?php if($datas->role != "supervisor"){ ?> 
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="name">Add Hours Rate($)</label> 
								<input type="text" class="form-control" value="{{ $datas->hourst_rate }}" id="hour_rate" name="hour_rate" placeholder="Hourly Rate"> 
							</div> 
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="name">Upload Driver License</label> 
								<span>(File size must be max 5MB)</span> 
								<input type="file" name="driving_license" class="form-control">
								@error('driving_license')
								<small class="validation-error">
									{{ $message }}
								</small>
								@enderror
							</div> 
						</div>
						<?php } ?> 
						<?php if($datas->role == "test"){ ?> 
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="users_id">Assign Users</label> 
								<select name="users_id[]" id="users_idm" class="mySelect" multiple="multiple"> 
									<option disabled value="0">Select Users</option> 
									@if($alluser->count() != 0) 
										@foreach ($alluser as $users) 
											<option <?php if($UserSupervisorRel->count() != 0){ foreach ($UserSupervisorRel as $UserSupervisorRels){ if($UserSupervisorRels->users_id == $users->id){ echo "selected"; } } } ?> value="{{ $users->id }}">
												{{ $users->name }}
											</option> 
										@endforeach 
									@endif 
								</select> 
							</div> 
						</div>
						<?php } ?> 
						<?php if($datas->role == "casemanager"){ ?> 
						<div class="col-md-6">
							<div class="form-group"> 
								<label for="cmusers_id">Assign Users</label> 
								<select name="cmusers_id[]" id="users_idm" class="mySelect" multiple="multiple"> 
									<option disabled value="0">Select Users</option> 
									@if($alluser->count() != 0) 
										@foreach ($alluser as $users) 
											<option <?php if($UserCasemanagerRel->count() != 0){ foreach ($UserCasemanagerRel as $UserCasemanagerRels){ if($UserCasemanagerRels->users_id == $users->id){ echo "selected"; } } } ?> value="{{ $users->id }}">
												{{ $users->name }}
											</option> 
										@endforeach 
									@endif 
								</select> 
							</div> 
						</div>
						<?php } ?> 
						<?php if($datas->drivers_license != null){ ?> 
						<div class="col-md-6">
							<div class="form-group"> 
								<img style="margin-top: 15px; width: 400px;" src="{{ url('/public/assets/uploads/driving-license') }}/{{ $datas->drivers_license }}"> 
							</div> 
						</div>
						<?php } ?> 
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