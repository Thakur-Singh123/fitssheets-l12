@extends('layouts.master')
@section('content') 
<div class="content-wrapper">
	@if(session('success'))
	<div class="alert alert-success">
		<h4>{{ session('success') }}</h4>
	</div>
	@endif
	@if(session('error'))
	<div class="alert alert-danger error-alert">
		<h4>{{ session('error') }}</h4>
	</div>
	@endif
	<div class="row">
		@if($data->count() != 0)
		@foreach ($data as $datas)
			<div class="col-md-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Change User Password</h4>
						<p style="display:none" class="card-description"> Basic form elements </p>
						<form method="POST" action="{{ url('/user/updatepassword') }}" class="forms-sample">
						@csrf
						<input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}" >
						<div class="form-group">
							<label for="password" >Current Password</label>
							<div style="position:relative;">
								<input id="password" placeholder="Current Password" type="password" class="form-control" name="current_password" autocomplete="current-password">
								<i class="fa fa-eye toggle-pass" data-target="current_password"></i>
							</div>
							@error('current_password')
							<small class="validation-error">
								{{ $message }}
							</small>
							@enderror
						</div>
						<div class="form-group">
							<label for="password" >New Password</label>
							<div style="position:relative;">
								<input id="new_password" placeholder="Enter new password" type="password" class="form-control" name="new_password">
								<i class="fa fa-eye toggle-pass" data-target="new_password"></i>
							</div>
							@error('new_password')
							<small class="validation-error">
								{{ $message }}
							</small>
							@enderror
						</div>
						<div class="form-group">
							<label for="password" >New Confirm Password</label>
							<div style="position:relative;">
								<input id="new_confirm_password" placeholder="Enter new confirm password" type="password" class="form-control" name="new_confirm_password">
								<i class="fa fa-eye toggle-pass" data-target="new_confirm_password"></i>
							</div>
							@error('new_confirm_password')
							<small class="validation-error">
								{{ $message }}
							</small>
							@enderror
						</div>
						<button type="submit" class="btn btn-success mr-2">Submit</button>
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




