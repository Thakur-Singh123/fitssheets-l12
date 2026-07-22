@extends('layouts.user')
@section('content') 
<style>
.card {
    background: #14a800;
}
.card-body {
    text-align: center;
}
.btn-success {
	background-color: #1d4354 !important;
	border-color: #1d4354 !important;
}
.btn {
    font-size: 20px;
}
.form-control {
    padding: 5px 5px;
}
.card .card-title {
	color: #fff;
	font-size: 30px;
}
span {
	font-size: 18px;
	color: #fff;
}
</style>
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
					<h4 class="card-title">Upload your Vaccination Card</h4>
					<form method="POST" action="{{ url('/my/covid-report/submit') }}" enctype="multipart/form-data" class="forms-sample">
					@csrf
						<input type="hidden" id="user_id" name="user_id" class="form-control" value="{{ Auth::user()->id }}" >
						<span >(File size must be max 5MB)</span>
						<span >(File type Must be jpeg,jpg,png)</span>
						<div style="margin-top: 12px;margin-left: 260px;" class="col-md-6">
							<input type="file" name="covid_report" class="form-control">
						</div>
						<div style="margin-top: 12px;margin-left: 245px;" class="col-md-6">
							<button type="submit" class="btn btn-success">Upload</button>
						</div>
					</form>
					<hr style="border-top: 5px solid #fff;">
					<?php if(Auth::user()->covid_report != null && Auth::user()->covid_report != '0') { ?>
						<h3 style="color: #fff;">My Vaccination Card</h3>
						<img style=" margin-top: 15px; width: 400px;" src="{{ url('/assets/uploads/covid-report') }}/{{ Auth::user()->covid_report }}">
					<?php } else { ?>
						<h3 style="color: #fff;">Sample Vaccination Card</h3>
						<img style=" margin-top: 15px; width: 400px;" src="{{ url('/assets/uploads/covid-report') }}/sample-covid.png">
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection