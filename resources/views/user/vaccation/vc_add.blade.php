@extends('layouts.user')
@section('content')
<style>
#hrs_re h4 {
  margin: 0 !important;
}
#hrs_re .col-sm-3 {
  background: green;
  color: #fff;
  padding: 10px;
  border-radius: 25px;
}
</style>
<?php use App\Http\Controllers\User\UserController; ?>
<?php $time_status = UserController::time_status(Auth::user()->id); ?>
<?php if($time_status < 1){ ?>
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
      <div style="background: #b2f0da;" class="card">
        <div class="card-body">
            <h4 class="card-title">Add New Vacation</h4>
            <div class="d-flex justify-content-end mb-3">
              <a href="{{ route('time-sheets.create') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back
              </a>
            </div>
            <p style="display:none" class="card-description"> Basic form elements </p>
            <form id="time_sheet" method="POST" action="{{ url('/enter-vaccation/store') }}" class="forms-sample">
              @csrf
              <input type="hidden" id="user_id" name="user_id" class="form-control" >
              <label style="background: #ffbc00ab;" class="col-sm-3 col-form-label">1. Enter a date range</label>
              @error('vacc_frm')
              <small class="validation-error">
              {{ $message }}
              </small>
              @enderror
              <div class="form-group row">
                  <label class="col-sm-3 col-form-label">From Date</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" id="vfrm_dt" name="vacc_frm" >
                  </div>
              </div>
              <div class="form-group row">
                  <label class="col-sm-3 col-form-label">To date</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" id="vto_dt" name="vacc_to" >
                  </div>
              </div>
              <div id="hrs_re" style="display:none" class="form-group row">
                  <div class="col-sm-3">
                    <h4>Vacc Requested: <span id="hours_req"></span> Hours</h4>
                  </div>
              </div>
              <label style="background: #ffbc00ab;" class="col-sm-3 col-form-label">2. Enter Request Details</label>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Reason for Vacation Request</label>
                <div class="col-sm-9">
                  <textarea id="comments" name="comments"></textarea>
                </div>
              </div>
              <!--div class="form-group row">
              <label class="col-sm-3 col-form-label">Time Policy</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="time_policy" name="time_policy" >
              </div>
              </div>
              <div class="form-group row">
              <label class="col-sm-3 col-form-label">Report By</label>
              <div class="col-sm-3">
                <input type="text" class="form-control" id="report_by" name="report_by" >
              </div>
              </div-->
              <button type="submit" class="btn btn-success mr-2 submit_data">Submit</button>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="loding"></div>
<script>
  //When the user clicks on div, open the popup
  function myFunction() {
    var popup = document.getElementById("myPopup");
    popup.classList.toggle("show");
  }
</script>
@endsection