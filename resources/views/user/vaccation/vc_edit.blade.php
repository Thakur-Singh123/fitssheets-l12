@extends('layouts.user')
@section('content')
<style>
   #hrs_re h4{
   margin: 0 !important;
   }
   #hrs_re .col-sm-3 {
   background: green;
   color: #fff;
   padding: 10px;
   border-radius: 25px;
   }
   /* Popup container - can be anything you want */
   .popup {
   position: relative;
   display: inline-block;
   cursor: pointer;
   -webkit-user-select: none;
   -moz-user-select: none;
   -ms-user-select: none;
   user-select: none;
   }
   /* The actual popup */
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
   /* Popup arrow */
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
   /* Toggle this class - hide and show the popup */
   .popup .show {
   visibility: visible;
   -webkit-animation: fadeIn 1s;
   animation: fadeIn 1s;
   }
   /* Add animation (fade in the popup) */
   @-webkit-keyframes fadeIn {
   from {opacity: 0;} 
   to {opacity: 1;}
   }
   @keyframes fadeIn {
   from {opacity: 0;}
   to {opacity:1 ;}
   }
</style>
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
        @if($data->count() != 0)
        @foreach ($data as $datas)
        <div class="col-md-12 grid-margin stretch-card">
          <div style="background: #b2f0da;" class="card">
              <div class="card-body">
                <h4 class="card-title">Edit Vacation</h4>
                <p style="display:none" class="card-description"> Basic form elements </p>
                <form method="POST" action="{{ url('/enter-vaccation/update') }}" class="forms-sample">
                    @csrf
                    <input type="hidden" id="hidden_id" name="hidden_id" value="{{ $datas->id }}" class="form-control"  >
                    <label style="background: #ffbc00ab;"  class="col-sm-3 col-form-label">1. Enter a date range</label>
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">From Date</label>
                      <div class="col-sm-3">
                          <input type="text" class="form-control" value="{{ $datas->vacc_start }}" id="vfrm_dt" name="vacc_frm" >
                      </div>
                    </div>
                    @error('vacc_frm')
                    <small class="validation-error">
                      {{ $message }}
                    </small>
                    @enderror
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">To date</label>
                      <div class="col-sm-3">
                          <input type="text" class="form-control" value="{{ $datas->vacc_end }}" id="vto_dt" name="vacc_to" >
                      </div>
                    </div>
                    <div id="hrs_re" style="display:none" class="form-group row">
                      <div class="col-sm-3">
                          <h4>Vacc Requested: <span id="hours_req"></span> Hours</h4>
                      </div>
                    </div>
                    <label style="background: #ffbc00ab;"  class="col-sm-3 col-form-label">2. Enter Request Details</label>
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Reason for Vacation Request</label>
                      <div class="col-sm-9">
                          <textarea id="comments" name="comments">{{ $datas->vacc_comments }}</textarea>
                      </div>
                    </div>
                    <!--div class="form-group row">
                      <label class="col-sm-3 col-form-label">Time Policy</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="time_policy" value="{{ $datas->vacc_comments }}" name="time_policy" >
                      </div>
                      </div>
                      <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Report By</label>
                      <div class="col-sm-3">
                        <input type="text" class="form-control" id="report_by" value="{{ $datas->vacc_rbu }}" name="report_by" >
                      </div>
                      </div-->
                    <button type="submit" class="btn btn-success mr-2 submit_data">Update</button>
                </form>
              </div>
          </div>
        </div>
        @endforeach
        @else
        <tr>
          <td colspan="16" class="no-data">
            Sorry, No data found!
          </td>
        </tr>
        @endif
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