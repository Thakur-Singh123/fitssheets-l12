@extends('layouts.master')
@section('content')
<div class="content-wrapper">
	<div class="row">
		<div class="col-md-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Add Vacation</h4>
					<p style="display:none" class="card-description"> Basic form elements </p>
					<form method="POST" action="{{ url('/vaccations/store') }}" class="forms-sample">
					@csrf
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
						@error('user_id')
						<small class="validation-error">
							{{ $message }}
						</small>
						@enderror
					</div>
					<div class="form-group">
					<label for="vacc_sl">Paid Time OFF</label>
					<input id="vacc_sl" class="form-control"  name="vacc_sl" />
					@error('vacc_sl')
					<small class="validation-error">
						{{ $message }}
					</small>
					@enderror
					</div>
					<!--div class="form-group">
					<label for="vacc_vc">Vacation</label>
					<input id="vacc_vc" class="form-control" name="vacc_vc" />
					</div>
					<div class="form-group">
					<label for="vacc_be">Bereveavment</label>
					<input id="vacc_be" class="form-control"  name="vacc_be" />
					</div>
					<div class="form-group">
					<label for="vacc_jd">Jury Duty</label>
					<input id="vacc_jd" class="form-control" name="vacc_jd" />
					</div-->
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">From Date</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="vfrm_dt" name="vacc_frm" >
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label">To date</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="vto_dt" name="vacc_to" >
						</div>
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
