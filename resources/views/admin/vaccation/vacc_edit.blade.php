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
					<h4 class="card-title">Update Vacations</h4>
					<p style="display:none" class="card-description"> Basic form elements </p>
					<form method="POST" action="{{ url('/vaccations/update') }}" class="forms-sample">
					@csrf
					<input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}" >
					<div class="form-group">
						<label for="dept_add">User</label>
						<select class="form-control form-control-lg" id="user_id" name="user_id">
							<option disabled value="0">Select User</option>
							@if($users->count() != 0)
								@foreach ($users as $user)
									<option value="{{ $user->id }}" >{{ $user->name }}</option>
								@endforeach
							@endif
						</select>
					</div>
					<div class="form-group">
						<label for="vacc_sl">Paid Time OFF</label>
						<input id="vacc_sl" class="form-control"  name="vacc_sl" value="{{ $datas->vacc_sl }}" />
					</div>
					<!--div class="form-group">
						<label for="vacc_vc">Vacation</label>
						<input id="vacc_vc" class="form-control" name="vacc_vc" value="{{ $datas->vacc_vc }}" />
					</div>
					<div class="form-group">
						<label for="vacc_be">Bereveavment</label>
						<input id="vacc_be" class="form-control"  name="vacc_be" value="{{ $datas->vacc_be }}" />
					</div>
					<div class="form-group">
						<label for="vacc_jd">Jury Duty</label>
						<input id="vacc_jd" class="form-control" name="vacc_jd" value="{{ $datas->vacc_jd }}"  >
					</div-->
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">From Date</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="vfrm_dt" name="vacc_frm" value="{{ $datas->vacc_frm }}" >
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">To date</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="vto_dt" name="vacc_to" value="{{ $datas->vacc_to }}" >
						</div>
					</div>
					<button type="submit" class="btn btn-success mr-2">Update</button>
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
