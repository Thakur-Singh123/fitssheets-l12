@extends('layouts.user')
@section('content') 
<div class="content-wrapper">
	@if(\Session::has('success'))
	<div class="alert alert-success">
		<h4>{{\Session::get('success')}}</h4>
	</div>
	@endif
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
					<h4 class="card-title">Upload Driving License</h4>
					<form method="POST" action="{{ url('/my/driving-license/submit') }}" enctype="multipart/form-data" class="forms-sample">
						@csrf
						<input type="hidden" id="user_id" name="user_id" class="form-control" value="{{ Auth::user()->id }}" >
						<span >(File size must be max 5MB)</span>
						<div style="margin-top: 12px;margin-left: -13px;" class="col-md-6">
							<input type="file" name="driving_license" class="form-control">
						</div>
						<div style="margin-top: 12px;margin-left: -13px;" class="col-md-6">
							<button type="submit" class="btn btn-success">Upload</button>
						</div>
					</form>
					<?php if(Auth::user()->drivers_license != null) { ?>
					    <img style=" margin-top: 15px; width: 400px;" src="{{ url('/assets/uploads/driving-license') }}/{{ Auth::user()->drivers_license }}">
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection