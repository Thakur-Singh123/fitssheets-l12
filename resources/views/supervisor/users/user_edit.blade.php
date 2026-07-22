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
			<h4 class="card-title">Create New User</h4>
			<p style="display:none" class="card-description"> Basic form elements </p>
			<form method="POST" action="{{ url('/users/update') }}" class="forms-sample">
			@csrf
			<input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}" >
			  <div class="form-group">
				<label for="name">Name</label>
				<input type="text" class="form-control" value="{{ $datas->name }}" id="name" name="name" placeholder="Name">
			  </div>
			  <div class="form-group">
				<label for="email">Email address</label>
				<input type="email" class="form-control" value="{{ $datas->email }}" id="email" name="email" placeholder="Email">
			  </div>
			  <div class="form-group">
				  <label for="role">Role</label>
				  <select class="form-control form-control-lg" id="role" name="role">
					<option <?php if($datas->role == "user"){ echo "selected"; }  ?> value="user">User</option>
					<option <?php if($datas->role == "manager"){ echo "selected"; }  ?>  value="manager">Manager</option>
				  </select>
			  </div>
			  <div class="form-group">
				  <label for="role">Status</label>
				  <select class="form-control form-control-lg" id="status" name="status">
					<option <?php if($datas->status == "1"){ echo "selected"; }  ?> value="1" >Active</option>
					<option <?php if($datas->status == "0"){ echo "selected"; }  ?> value="0">Inactive</option>
				  </select>
			  </div>
			  <div class="form-group">
				<label for="dept">Department</label>
				<input type="text" value="{{ $datas->dept }}" class="form-control" id="dept" name="dept" placeholder="Department">
			  </div>
			  <button type="submit" class="btn btn-success mr-2">Submit</button>
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
