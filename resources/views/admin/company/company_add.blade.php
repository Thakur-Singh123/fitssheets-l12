@extends('layouts.master')
@section('content')
<div class="content-wrapper">
	<div class="row">
    @if(session('success'))
    <div class="alert alert-success">
        <h4>{{ session('success') }}</h4>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">
        <h4>{{ session('error') }}</h4>
    </div>
    @endif
  </div>
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Add New Company</h4>
          <p style="display:none" class="card-description"> Basic form elements </p>
          <form method="POST" action="{{ url('/companies/store') }}" class="forms-sample">
          @csrf
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Name">
              @error('name')
              <small class="validation-error">
                  {{ $message }}
              </small>
              @enderror
            </div>
            <div class="form-group">
              <label for="email">Company</label>
              <input type="text" class="form-control" id="company" name="company" value="{{ old('company') }}" placeholder="Company">
              @error('company')
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
  </div>
</div>
<div class="loding"></div>
@endsection		
