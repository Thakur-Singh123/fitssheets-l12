@extends('layouts.user')
@section('content') 
<div class="content-wrapper">
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
   <div class="row"></div>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Profile</h4>
                    <form method="POST" action="{{ url('/my/name/update') }}" enctype="multipart/form-data" class="forms-sample">
                        @csrf
                        <input type="hidden" id="user_id" name="user_id" class="form-control" value="{{ Auth::user()->id }}" >
                        <div class="form-group text-center mb-4">
                            <div class="profile-upload-wrapper">
                                @if(!empty(Auth::user()->avtar))
                                    <img src="{{ asset('public/assets/uploads/users/'.Auth::user()->avtar) }}" class="profile-preview">
                                @else
                                    <img src="{{ asset('public/assets/images/default.jpg') }}" class="profile-preview">
                                @endif
                                <label for="avtar" class="upload-icon"><i class="fa fa-pencil"></i></label>
                                <input type="file" id="avtar" name="avtar" accept="image/*" style="display:none;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="fname" >First Name</label>
                            <input id="fname"  type="text" value="{{ old('fname', Auth::user()->first_name)  }}" class="form-control" name="fname" placeholder="Enter first name">
                            @error('fname')
                            <small class="validation-error">
                                {{ $message }}
                            </small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="lname" >Last Name</label>
                            <input id="lname"  value="{{ old('lname',Auth::user()->last_name)  }}" type="text" class="form-control" name="lname" placeholder="Enter last name">
                        </div>
                        <div class="form-group">
                            <label for="phone_no" >Phone Number</label>
                            <input id="phone_no" value="{{ old('phone_no', Auth::user()->phone_no)  }}" type="text" class="form-control" name="phone_no" placeholder="Enter phone number">
                            @error('phone_no')
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