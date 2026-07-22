@extends('layouts.user')
@section('content')
<style>
/*Popup container - can be anything you want*/
.validation-error,
small.validation-error,
label.error{
  display: block;
  margin-top: 6px;
  color: #ff1b15;
  font-size: 12px;
  font-weight: 500;
}
.popup {
  position: relative;
  display: inline-block;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
/*The actual popup*/
.popup .popuptext {
  visibility: hidden;
  width: 700px;
  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 8px 0;
  position: absolute;
  z-index: 1;
  bottom: 125%;
  left: 50%;
  margin-left: -80px;
}
/*Popup arrow*/
.popup .popuptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 15%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}
/*Toggle this class - hide and show the popup */
.popup .show {
  visibility: visible;
  -webkit-animation: fadeIn 1s;
  animation: fadeIn 1s;
}
/*Add animation (fade in the popup)*/
@-webkit-keyframes fadeIn {
  from {
    opacity: 0;
  } 
  to {
    opacity: 1;
  }
  }
  @keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity:1 ;
  }
}
</style>
<?php use App\Http\Controllers\User\UserController; ?>
<?php $time_status = UserController::time_status(Auth::user()->id); ?>
<?php if($time_status < 1) { ?>
  <div id="timesheetcreateModal" class="modal fade">
    <div class="modal-dialog">
      <div style="top: 100px;background: black;height:70vh" class="modal-content">
        <div class="modal-header">
          <button style=" display:none;color: #fff;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div style="padding-top:45px" class="modal-body">
          <p>Please upload your drivers licence to start the activation process.</p>
          <p>Call  1 844 329 1514 option 1 for help.</p>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
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
          <h4 class="card-title">Add New Hours</h4>
          <p style="display:none" class="card-description"> Basic form elements </p>
          <form id="time_sheet" method="POST" action="{{ url('/time-sheets/store') }}" class="forms-sample">
            @csrf
            <input type="hidden" id="user_id" name="user_id" class="form-control" value="{{ $user }}" >
            <div class="form-group">
              <label for="company_id">Select Company</label>
              <select data-baseURL="{{ url('/') }}" class="form-control form-control-lg" id="company_idu" name="company_id" value="{{ old('company_id') }}">
                <option value="none" selected disabled hidden>Select Company</option>
                @if($companies->count() != 0)
                  @foreach ($companies as $company)
                    <option value="{{ $company->id }}" >
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
              <label for="company_id">Select House</label>
              <select class="form-control form-control-lg" id="house_id" name="house_id" value="{{ old('house_id') }}">
                <option disabled value="0">Select House</option>
                @if($houses->count() != 0)
                  @foreach ($houses as $house)
                    <option value="{{ $house->id }}" >{{ $house->house_add }}</option>
                  @endforeach
                @endif
              </select>
              @error('house_id')
              <small class="validation-error">
                {{ $message }}
              </small>
              @enderror
            </div>
            <div class="form-group">
              <label for="date">Select Date</label>
              <input type="text" class="form-control" id="hours_day" name="hours_day" value="{{ old('hours_day') }}">
              @error('hours_day')
              <small class="validation-error">
                {{ $message }}
              </small>
              @enderror
            </div>
            <div class="form-group">
              <label for="hours">Time In</label>
              <input type="text" class="form-control" id="time_in" name="time_in" value="{{ old('time_in') }}" placeholder="hh:mm">
              @error('time_in')
              <small class="validation-error">
                {{ $message }}
              </small>
              @enderror
            </div>
            <div class="form-group">
              <label for="hours">Time Out</label>
              <input data-baseURL="{{ url('/') }}"  type="text" class="form-control" id="time_out" name="time_out" value="{{ old('time_out') }}" placeholder="hh:mm">
              @error('time_out')
              <small class="validation-error">
                {{ $message }}
              </small>
              @enderror
            </div>
            <div class="form-group">
              <label for="hours">Total Hours</label>
              <input disabled  type="text" class="form-control" id="total_hours" name="total_hours"  >
              @error('total_hours')
              <small class="validation-error">
                {{ $message }}
              </small>
              @enderror
            </div>
            <div class="form-check">
              <label class="form-check-label">
              <!--a target="blank"   style="color: green;
              font-size: 18px;
              margin-left: -30px;
              margin-top: 15px;
              margin-bottom: 15px;
              text-decoration: underline;" class="popup">How to apply for vacation?
              </a-->
              <!--a target="blank"  href="#" style="color: green;
              font-size: 18px;
              margin-left: -30px;
              margin-top: 15px;
              margin-bottom: 15px;
              text-decoration: underline;" class="popup">Apply Vaccation though gusto
              </a-->
              </label>
              <label class="form-check-label">
              <a  style="color: green;
                font-size: 18px;
                margin-left: -30px;
                margin-top: 15px;
                margin-bottom: 15px;
                text-decoration: underline;" href="{{ route('enter-vaccation.create') }}" class="enter_vacca" >Enter Vaccation
              </a>
              </label>
            </div>
            <button type="submit" class="btn btn-succes mr-2 submit_data">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="loding"></div>
<script>
  // When the user clicks on div, open the popup
  function myFunction() {
    var popup = document.getElementById("myPopup");
    popup.classList.toggle("show");
  }
</script>
@endsection