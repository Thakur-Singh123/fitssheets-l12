@extends('layouts.master')
@section('content') 
<div class="content-wrapper">
@if(\Session::has('success'))
    <div class="alert alert-success">
        <h4>{{\Session::get('success')}}</h4>
    </div>
@endif

            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h3>SMS Alerts</h3>
                             <form method="POST" action="{{ url('/smsnotification') }}">
                                  @csrf
                                <div class="form-group">
                                    <label for="dept_add">Message</label>
                                    <textarea class="form-control"  name='message' id="message" rows="4" cols="50"></textarea>
                                    @error('message')
                                        <span style="color:red;font-size:14px;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label style="margin-top:5px;" for="dept_add">All Users</label>
                                    <input style="font-size: 16px;width: 50px;height: 25px;" type="checkbox"  value="1" name="all_user" id="all_userb"> 
                                </div>
                                <div id="all_u_c" class="form-group">
                                              <label for="users_id">Select Users</label>
                                              <select  name="users_id[]" id="users_idm" class="mySelect" multiple="multiple">
                                              <option disabled value="0">Select Users</option>
                                                @if($user->count() != 0)
                                                    @foreach ($user as $users)
                                                        <option value="{{ $users->id }}">{{ $users->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('users_id')
                                            <span style="color:red;font-size:14px;">
                                                {{ $message }}
                                            </span>
                                            @enderror
                                            </div>
                                <div id="all_u_u" class="form-group">
                                  <label for="company_id">Select Companies</label>
                                  <select name="company_id[]" id="company_idm" class="mySelect" multiple="multiple">
                                    <option disabled value="0">Select Company</option>
                                      @if($companies->count() != 0)
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}" >{{ $company->company }}</option>
                                        @endforeach
                                      @endif
                                </select>
                                </div>
                                <div class="form-group">
                                     <label>Any Other Phone numbers (seperate with a comma [,])</label>
                                     <input class="form-control" type='text' name='numbers' />
                                 </div>
                               
                                <button type="submit" class="btn btn-success mr-2">Send!</button>
                          </form>
                    </div>
                </div>
              </div>
            </div>
          </div>

@endsection