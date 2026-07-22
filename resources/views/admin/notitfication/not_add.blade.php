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
                    <h4 class="card-title">Create New Notification</h4>
                    <p style="display:none" class="card-description"> Basic form elements </p>
                    <form method="POST" action="{{ url('/notifications/store') }}" class="forms-sample">
					@csrf
                      <div class="form-group">
                        <label for="dept_add">Notifcation Title</label>
						<input id="not_title" class="form-control"  name="not_title" />
                                              </div>
											   <div class="form-group">
                        <label for="dept_add">Notifcation Text</label>
						<textarea id="not_text" class="form-control"  name="not_text"></textarea>
                                              </div>
											  <div class="form-group">
											  <label for="users_id">Select Users</label>
											  <select  name="users_id[]" id="users_idm" class="mySelect" multiple="multiple">
											  <option disabled value="0">Select Users</option>
												@if($user->count() != 0)
													@foreach ($user as $users)
														<option value="{{ $users->id }}">{{ $users->name }}</option>
													@endforeach
												@endif
											</select>
											</div>
											<div class="form-group">
											  <label for="company_id">Select Companies</label>
											  <select name="company_id[]" id="company_idm" class="mySelect" multiple="multiple">
												<option disabled value="0">Select Company</option>
												  @if($companies->count() != 0)
													@foreach ($companies as $company)
														<option value="{{ $company->id }}" >{{ $company->company }}</option>
													@endforeach
												  @endif
											</select>
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
