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
		@if($data->count() != 0) 
		@foreach ($data as $datas)
		<div class="col-md-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Edit Company</h4>
					<p style="display: none" class="card-description">Basic form elements</p>
					<form method="POST" action="{{ url('/companies/update') }}" class="forms-sample">
						@csrf
						<input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}" />
						<div class="form-group">
							<label for="name">Name</label>
							<input type="text" class="form-control" value="{{ $datas->name }}" value="{{ old('name', $datas->name) }}" id="name" name="name" placeholder="Name"/>
							@error('name')
							<small class="validation-error"> 
								{{ $message }} 
							</small>
							@enderror
						</div>
						<div class="form-group">
							<label for="email">Company</label>
							<input type="text" class="form-control" value="{{ $datas->company }}" id="company" name="company" value="{{ old('name', $datas->company) }}" placeholder="Company"/>
							@error('company')
							<small class="validation-error">
								{{ $message }} 
							</small>
							@enderror
						</div>
						<button type="submit" class="btn btn-success mr-2">Update</button>
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
