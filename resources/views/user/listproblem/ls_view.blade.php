@extends('layouts.user')

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
			  

			<table id="sortable-table-1" class="table dataTable table-striped table-responsive">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> SSN<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Problems <i class="mdi mdi-chevron-down"></i></th>
				  <th> Actions </th>
				</tr>
				
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
					<tr>
					  <td><?php echo $count; ?></td>
						 <td> {{ $datas->users->name  }}</td>
					   <td> {{ $datas->users->ssn_no  }} </td>
					  <td> {{ $datas->companies->company  }}</td>
					  
					   <td> {{ $datas->problems  }} </td>
					  <td>
						<a  href="{{ route('time-sheets.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
					
					  </td>
					</tr>
				<?php $count++; ?>
				@endforeach
				
				@else
					<p>Sorry No Data!!</p>
				@endif
			  </tbody>
			</table>
			<br>
			<?php echo $data->render(); ?>
		  </div>
		</div>
	  </div>
	</div>
	<div class="loding"></div>
	</div>
@endsection		
