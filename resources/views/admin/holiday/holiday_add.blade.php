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
          <h4 class="card-title">Add New Holiday</h4>
          <p style="display:none" class="card-description"> Basic form elements </p>
          <form method="POST" action="{{ url('/holidays/store') }}" class="forms-sample">
          @csrf
          <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" id="date" name="date" placeholder="Date">
              @error('date')
                <small class="validation-error">
                  {{ $message }}
                </small>
                @enderror
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <input type="text" class="form-control" id="description" name="description" placeholder="Description">
              @error('description')
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
