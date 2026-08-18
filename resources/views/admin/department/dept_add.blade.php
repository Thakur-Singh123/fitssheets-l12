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
            <h4 class="card-title">Add New Department</h4>
            <p style="display: none" class="card-description">Basic form elements</p>
            <form method="POST" action="{{ url('/department/store') }}" class="forms-sample">
              @csrf
              <div class="form-group">
                <label for="dept_add">Department Name</label>
                <input id="dept_add" class="form-control" name="dept_add" placeholder="name" />
                @error('dept_add')
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
