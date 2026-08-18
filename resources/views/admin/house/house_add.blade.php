@extends('layouts.master') @section('content')
<div class="content-wrapper">
	<div class="row">
		@if(session('success'))
		<div class="alert alert-success">
		    <h4>{{ session('success') }}</h4>
		</div>
		@endif @if(session('error'))
		<div class="alert alert-danger">
		    <h4>{{ session('error') }}</h4>
		</div>
		@endif
	</div>
	<div class="row">
		<div class="col-md-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Add New House</h4>
					<p style="display: none" class="card-description">Basic form elements</p>
					<form method="POST" action="{{ url('/houses/store') }}" class="forms-sample">
						@csrf
						<div class="form-group">
							<label for="company_id">Select Company</label>
							<select class="form-control form-control-lg" id="company_id" name="company_id">
								<option value="none" selected disabled hidden>Select Company</option>
								@if($companies->count() != 0)
								@foreach ($companies as $company)
								    <option value="{{ $company->company }}">
										{{ $company->company }}
									</option>
								@endforeach 
								@endif
							</select>
							@error('company_id')
							<small class="validation-error"> 
								{{ $message }} 
							</small>
							@enderror
						</div>
						<div class="form-group">
							<label for="house_add">House Address</label>
							<textarea id="house_add" class="form-control" name="house_add">{{ old('house_add') }}</textarea>
							@error('house_add')
							<small class="validation-error"> 
								{{ $message }} </small>
							@enderror
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
