@extends('layouts.master') @section('content')
<div class="content-wrapper">
	@if(session('success'))
	<div class="alert alert-success">
		<h4>{{ session('success') }}</h4>
	</div>
	@endif @if(session('error'))
	<div class="alert alert-danger">
		<h4>{{ session('error') }}</h4>
	</div>
	@endif
	<div class="row">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
				<h4 class="card-title">Houses</h4>
				{{--<p class="card-description"><a href="{{ route('houses.create') }}"> Add House <i class="fa fa-plus-circle"></i></a></p>--}}
				<div class="d-flex justify-content-end align-items-center mb-3">
					<a href="{{ route('houses.create') }}" class="btn btn-primarys" style="background-color: #1c45ef; color: white; border-radius: 6px; padding: 10px 15px; font-size: 14px">
						<i class="fa fa-plus mr-1"></i>
						Add House
					</a>
				</div>
				<table class="table table-striped">
					<thead>
					<tr>
						<th>Sr. No</th>
						<th>Company</th>
						<th>House</th>
						<th>Created</th>
						<th>Actions</th>
					</tr>
					</thead>
					<tbody>
					<?php $count = 1; ?> @if($data->count() != 0) @foreach ($data as $datas)
					<tr>
						<td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}.</td>
						<td style="display: none"><img src="../../../assets/images/faces-clipart/pic-1.png" alt="image" /></td>
						<td>{{ $datas->companies_id }}</td>
						<td>{{ $datas->house_add }}</td>
						<td>{{ date('M d, Y', strtotime($datas->created_at)) }}</td>
						<td>
						<a href="{{ route('houses.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
						{{--<a style="margin-left: 5px" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_house" title="Delete"><i class="fa fa-trash-o"></i></a>--}}
						<a style="margin-left: 5px" data-baseURL="{{ url('/') }}" data-house_id="{{ $datas->id  }}" class="delete_house_record" title="Delete"><i class="fa fa-trash-o"></i></a>
						</td>
					</tr>
					<?php $count++; ?> @endforeach @else
					<tr>
						<td colspan="5" class="no-data">Sorry, No data found!</td>
					</tr>
					@endif
					</tbody>
				</table>
				{{ $data->links('pagination::bootstrap-5') }}
				</div>
			</div>
		</div>
	</div>
    <div class="loading"></div>
</div>
@endsection
