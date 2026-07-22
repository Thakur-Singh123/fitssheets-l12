@extends('layouts.manager')
@section('content') 
		<div class="content-wrapper">
	@if(\Session::has('success'))
		<div class="alert alert-success">
			<h4>{{\Session::get('success')}}</h4>
		</div>
	@endif
		<div class="row">
	<div class="col-md-6 grid-margin stretch-card average-price-card">
                    <div class="card text-white" style="    background: #ff5722 !important;">
                      <div class="card-body">
                        <div class="d-flex justify-content-between pb-2 align-items-center">
                          <h2 class="font-weight-semibold mb-0">{{ date("M d, Y", strtotime($current_date_time)) }}</h2>
                          <div class="icon-holder" style="  border: #ff5722 !important; background: #ff5722 !important;">
                    
                          </div>
                        </div>
                        <div class="d-flex justify-content-between">
                          <h5 class="font-weight-semibold mb-0">Today's</h5>
                        </div>
                      </div>
                    </div>
					
                  </div>
				 	<div class="col-md-6 grid-margin stretch-card average-price-card">
                    <div class="card text-white" style="    background: #4caf50 !important;">
                      <div class="card-body">
                        <div class="d-flex justify-content-between pb-2 align-items-center">
                          <h2 class="font-weight-semibold mb-0">{{ $user_count }}</h2>
                          <div class="icon-holder" style="  border: #4caf50 !important; background: #4caf50 !important;">
						  <i class="fa fa-users"></i>
                          </div>
                        </div>
                        <div class="d-flex justify-content-between">
                          <h5 class="font-weight-semibold mb-0">Users</h5>
                        </div>
                      </div>
                    </div>
					
                  </div>
	</div>
	<div class="row">
	  <div class="col-lg-12 grid-margin stretch-card">
		<div class="card">
		  <div class="card-body">
			<h2>Clock In & Clock Out ({{ date("M d, Y", strtotime($current_date_time)) }})</h2>
			<p  class="card-description">Manager</p>
			<h4 class="card-title">{{ Auth::user()->name }}</h4>
			<table class="table table-striped table-responsive">
			  <thead>
				<tr>
				  <th> # </th>
				  <th> Name </th>
				  <th> Department </th>
				  <th> Company </th>
				  <th> House</th>
				  <th> Time In </th>
				  <th> Time Out </th>
				</tr>
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
					<tr>
					  <td><?php echo $count; ?></td>
						 <td> {{ $datas->users->name  }}</td>
					   <td> {{ $datas->users->dept  }} </td>
					  <td> {{ $datas->companies->company  }}</td>
					   <td> {{ $datas->houses->house_add  }} </td>
					  <td> {{ $datas->time_in	  }} </td>
					  <td> {{ $datas->time_out	  }} </td>
					</tr>
				<?php $count++; ?>
				@endforeach
				
				@else
					<p>Sorry No Data!!</p>
				@endif
			  </tbody>
			</table>

		  </div>
		</div>
	  </div>
	</div>
	<div class="loding"></div>
	</div>
@endsection	