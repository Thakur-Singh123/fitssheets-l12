@extends('layouts.master')
@section('content') 
<div class="content-wrapper">
@if(\Session::has('success'))
		<div class="alert alert-success">
			<h4>{{\Session::get('success')}}</h4>
		</div>
	@endif
			<div class="row">
				@if (count($errors) > 0)
				<div class="alert alert-danger">
					<strong>Whoops!</strong> There were some problems with your input.<br><br>
					<ul>
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
				@endif
			</div>
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Driving License</h4>
					<?php if($drivers_license != null){ ?>
					<img style=" margin-top: 15px; width: 400px;" src="{{ url('public/assets/uploads/driving-license') }}/{{ $drivers_license }}">
					<?php } else{ ?>
						<p>No License Found!</p>
						<a  href="{{ route('users.edit', $id) }}" title="Edit">Upload Here</a>
					<?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

@endsection




