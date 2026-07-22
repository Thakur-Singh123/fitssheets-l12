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
			<h4 class="card-title">Update House</h4>
			<p style="display:none" class="card-description"> Basic form elements </p>
			<form method="POST" action="{{ url('/houses/update') }}" class="forms-sample">
			@csrf
			 <input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}" >
			 <div class="form-group">
				<label for="company_id">Select Company</label>
				<select class="form-control form-control-lg" id="company_id" name="company_id">
					<option disabled value="0">Select Company</option>
					  @if($companies->count() != 0)
						@foreach ($companies as $company)
							<option <?php if($datas->companies_id == $company->id){ echo "selected"; } ?> value="{{ $company->id }}" >{{ $company->company }}</option>
						@endforeach
					  @endif
				 </select>
			  </div>
			  <div class="form-group">
				<label for="house_add">House Address</label>
				<textarea id="house_add" class="form-control"  name="house_add" >{{ $datas->house_add }}</textarea>
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
