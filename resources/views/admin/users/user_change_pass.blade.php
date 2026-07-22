@extends('layouts.master')
@section('content') 
<div class="content-wrapper">
@if(\Session::has('Pass_Success'))
		<div class="alert alert-success">
			<h4>{{\Session::get('Pass_Success')}}</h4>
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
                            <input id="password" placeholder="Current Password" type="password" class="form-control" name="current_password" autocomplete="current-password">
                        </div>
  
                        <div class="form-group">
                            <label for="password" >New Password</label>
                            <input id="new_password" placeholder="New Password" type="password" class="form-control" name="new_password" autocomplete="current-password">
                        </div>
  
                        <div class="form-group">
                            <label for="password" >New Confirm Password</label>
                            <input id="new_confirm_password" placeholder="New Confirm Password" type="password" class="form-control" name="new_confirm_password" autocomplete="current-password">
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




