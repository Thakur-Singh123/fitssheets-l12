@extends('layouts.supervisor')
@section('content')
<style>
.popup {
  position: relative;
  display: inline-block;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
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
.popup .show {
  visibility: visible;
  -webkit-animation: fadeIn 1s;
  animation: fadeIn 1s;
}
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
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Add New Issue</h4>
          <p style="display:none" class="card-description"> Basic form elements </p>
          <form id="time_sheet" method="POST" action="{{ url('/susers/list-issues/store') }}" class="forms-sample">
            @csrf
            <input type="hidden" id="user_id" name="user_id" class="form-control" value="{{ $user }}" >
            <div class="form-group">
              <label for="company_id">Select Company</label>
              <select data-baseURL="{{ url('/') }}" class="form-control form-control-lg" id="company_idu" name="company_id">
                <option disabled value="none"selected>Select Company</option>
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
            <?php /*
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" class="form-control" id="name" name="name" value="{{ $name }}">
            </div>
            <div class="form-group">
              <label for="ssn">SSN</label>
              <input  type="text" class="form-control" id="ssn" name="ssn" value="{{ $ssn }}"  >
            </div>
            */?>
            <!--<div class="form-group">-->
            <!--label for="issue">Issue</label-->
            <!--<br>
              <textarea rows="4" cols="50" id="issue" name="issue" ></textarea> -->
            <?php /*
              <input type="text" class="form-control" id="issue" name="issue" >
            */?>
            <!--</div>-->
            <div class="form-group">
              <label for="issue">Describe Your Issue</label>
              <textarea class="form-control issue-box" id="issue" name="issue" rows="6" placeholder="Describe your issue in detail...">{{ old('issue') }}</textarea>
              <small class="text-muted">
                Please explain your issue clearly so we can resolve it quickly.
              </small>
              @error('issue')
              <small class="validation-error">
              {{ $message }}
                </small>
              @enderror
            </div>
            <div class="form-group">
              <label for="resolution_remarks">Resolution Remarks</label>
              <input  type="text" class="form-control" id="resolution_remarks" name="resolution_remarks" placeholder="Enter any resolution remarks">
            </div>
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