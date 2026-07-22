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
                    <h4 class="card-title">Add User to Manager</h4>
                    <p style="display:none" class="card-description"> Basic form elements </p>
                    <form method="POST" action="{{ url('/user/musers/store') }}" class="forms-sample">
					@csrf
					<input type="hidden" id="muser_id" name="muser_id" class="form-control" value="{{ $id }}" >
                     <div class="form-group">
                        <label for="company_id">Select User</label>
                        <select class="form-control form-control-lg" id="user_id" name="user_id">
							<option disabled value="0">Select User</option>
							  @if($user->count() != 0)
								@foreach ($user as $users)
									<option value="{{ $users->id }}" >{{ $users->name }}</option>
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
