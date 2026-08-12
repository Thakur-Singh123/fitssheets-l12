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
				<h4 class="card-title">Edit Holiday</h4>
				<p style="display: none" class="card-description">Basic form elements</p>
				<form method="POST" action="{{ url('/holidays/update') }}" class="forms-sample">
					@csrf
					<input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="{{ $datas->id }}" />
					<div class="form-group">
						<label for="Date">Date</label>
						<input
							type="date"
							class="form-control"
							value="{{ $datas->date }}"
							id="date"
							name="date"
							placeholder="Date"
						/>
					</div>
					<div class="form-group">
						<label for="email">Description</label>
						<input
							type="text"
							class="form-control"
							value="{{ $datas->description }}"
							id="description"
							name="description"
							placeholder="Description"
						/>
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
