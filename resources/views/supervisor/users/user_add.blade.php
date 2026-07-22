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
						  <label for="role">Role</label>
						  <select class="form-control form-control-lg" id="role" name="role">
							<option value="user" >User</option>
							<option value="manager">Manager</option>
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
                        <label for="dept">Department</label>
                        <input type="text" class="form-control" id="dept" name="dept" placeholder="Department">
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
