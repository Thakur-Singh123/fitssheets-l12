@extends('layouts.manager')

@section('content')
<div class="content-wrapper">
	@if(\Session::has('success'))
		<div class="alert alert-success">
			<h4>{{\Session::get('success')}}</h4>
		</div>
	@endif
    <?php
        // echo "<pre>";
        // print_r($arr_dates);
    ?>
	<div class="row">
	  <div class="col-lg-12 grid-margin stretch-card">
		<div class="card">
		  <div class="card-body">
			<h4 class="card-title">Users</h4>
			<form class="form-sample">
			  <div class="row">
				<div class="col-md-10">
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">Search User</label>
					<div class="col-sm-9">
					  <input type="text" class="form-control" id="srch_user" name="srch_user">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				 <button data-baseURL="{{ url('/') }}" id="msubmit_user" type="submit" class="btn btn-success mr-2">Submit</button>
				 </div>
			  </div>
			  
			</form>
			<table class="table table-striped ">
			  <thead>
				<tr>
				  <th> # </th>
				  <th> Email </th>
				  <th> Name </th>
				  <th> Department </th>
				   <th> Company </th>
				   <th>Time</th>
				  <th>Day</th>
				  <th> Created </th>
				  <th> Status </th>
				  <th> Actions </th>
				</tr>
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
					<tr>
					  <td><?php echo $count; ?></td>
					  <td style="display:none">
						<img src="../../../assets/images/faces-clipart/pic-1.png" alt="image"> </td>
					  <td> {{ $datas->email  }} </td>
					  <td> {{ $datas->name  }} </td>
					  <td> {{ $datas->dept  }} </td>
					  <td> {{ $datas->companies_id  }} </td>
					   <td> @if($datas->last_login_at != null) {{ date('h:i a', strtotime($datas->last_login_at)) }} @endif</td>
					   <td> @if($datas->last_login_at != null) {{ date('M d, Y', strtotime($datas->last_login_at)) }} @endif</td>
					  <td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>
					  <td> Inactive </td>
					  <td>
						<a  style="margin-left: 5px;"  href="{{ url('/cmuser/timesheets') }}/{{ $datas->id  }}" title="Time Sheets"><i class="fa fa-book"></i></a>
					 </td>
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
