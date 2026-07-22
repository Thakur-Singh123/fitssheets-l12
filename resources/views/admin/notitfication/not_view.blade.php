@extends('layouts.master')

@section('content')
<div class="content-wrapper">
	@if(\Session::has('success'))
		<div class="alert alert-success">
			<h4>{{\Session::get('success')}}</h4>
		</div>
	@endif
	<div class="row">
	  <div class="col-lg-12 grid-margin stretch-card">
		<div class="card">
		  <div class="card-body">
			<h4 class="card-title">Notifications</h4>
			<p  class="card-description"><a href="{{ route('notifications.create') }}"> Add Notification <i class="fa fa-plus-circle"></i></a></p>
			<table class="table table-striped">
			  <thead>
				<tr>
				  <th> # </th>
				  <th> Notification Title </th>
				  <th> Created </th>
				  <th> Actions </th>
				</tr>
			  </thead>
			  <tbody>
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
					<tr>
					  <td><?php echo $count; ?></td>
					  <td> {{ $datas->not_title  }} </td>
					  <td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>
					  <td>
						<a  href="{{ route('notifications.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
						<a style="margin-left: 5px;" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_notification" title="Delete"><i class="fa fa-trash-o"></i></a>
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
	<div class="loading"></div>
	</div>
@endsection		
