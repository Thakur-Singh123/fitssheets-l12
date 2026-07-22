@extends('layouts.supervisor')
@section('content')
<div class="content-wrapper">
  @if(session('success'))
  <div class="alert alert-success">
    <h4>{{ session('success') }}</h4>
  </div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger error-alert">
    <h4>{{ session('error') }}</h4>
  </div>
  @endif
  <div class="row"></div>
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Change Password</h4>
          <p style="display:none" class="card-description">Basic form elements </p>
          <form method="POST" action="{{ url('/supervisor/profile/updatepassword') }}" class="forms-sample">
            @csrf
            <div class="form-group">
              <label for="password" >Current Password</label>
              <div style="position:relative;">
                <input id="password" placeholder="Enter current password" type="password" class="form-control" name="current_password">
                <i class="fa fa-eye toggle-pass" data-target="password"></i>
              </div>
              @error('current_password')
              <small class="validation-error">
                {{ $message }}
              </small>
              @enderror
            </div>
            <div class="form-group">
              <label for="password" >New Password</label>
              <div style="position:relative;">
                <input id="new_password" placeholder="Enter new password" type="password" class="form-control" name="new_password">
                <i class="fa fa-eye toggle-pass" data-target="new_password"></i>
              </div>
              @error('new_password')
              <small class="validation-error">
                {{ $message }}
              </small>
              @enderror
            </div>
            <div class="form-group">
              <label for="password" >New Confirm Password</label>
              <div style="position:relative;">
                <input id="new_confirm_password" placeholder="Enter new confirm password" type="password" class="form-control" name="new_confirm_password">
                <i class="fa fa-eye toggle-pass" data-target="new_confirm_password"></i>
              </div>
              @error('new_confirm_password')
              <small class="validation-error">
                {{ $message }}
              </small>
              @enderror
            </div>
            <button type="submit" class="btn btn-succes mr-2">Update</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection