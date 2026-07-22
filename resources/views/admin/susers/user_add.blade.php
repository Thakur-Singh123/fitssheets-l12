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
                    <h4 class="card-title">Create New User</h4>
                    <p style="display:none" class="card-description"> Basic form elements </p>
                    <form method="POST" action="{{ url('/users/store') }}" class="forms-sample">
					@csrf
                      <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                      </div>
                      <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                      </div>
					  <div class="form-group">
                        <label for="email">Supervisor Color</label>
                        <input type="color" class="form-control" id="color_field" name="color_field" placeholder="Choose Color">
                      </div>
					  <div class="form-group">
						  <label for="role">Role</label>
						  <select class="form-control form-control-lg" id="role" name="role">
							<option value="user" >User</option>
							<option value="manager">Manager</option>
							<option value="supervisor">Supervisor</option>
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
					  <div class="form-group">
                        <label for="company_id">Select Company</label>
                        <select class="form-control form-control-lg" id="company_id" name="company_id">
							<option disabled value="0">Select Company</option>
							  @if($companies->count() != 0)
								@foreach ($companies as $company)
									<option value="{{ $company->company }}" >{{ $company->company }}</option>
								@endforeach
							  @endif
						 </select>
                      </div>
					  <div class="form-group">
                        <label for="name">Add Hours Rate($)</label>
                        <input type="text" class="form-control" id="hour_rate" name="hour_rate" placeholder="Hourly Rate">
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
