@extends('layouts.master')

@section('content')
<?php use App\Http\Controllers\Admin\UserInfoController; ?>
<?php use App\Http\Controllers\Admin\AdminController; ?>
<style>
.table td img:not(.thumb-image), .table th img:not(.thumb-image) {
    border-radius: none !important;
}
</style>
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
			<h4 class="card-title">This Month Applicants</h4>
			 <div class="row">
				<div class="col-md-4">
				  <div class="form-group row" style="margin:10px 0px 0px 10px;">
					<h4>Applicants By Month</h4>
				  </div>
				</div>
				<div class="col-md-2">
				      <select data-baseURL="{{ url('/') }}" id="aap_month" class="form-control" name="aap_month" >
						<option value="0" >Select</option>
						<option value="1" >January</option>
						<option value="2" >February</option>
						<option value="3" >March</option>
						<option value="4" >April</option>
						<option value="5" >May</option>
						<option value="6" >June</option>
						<option value="7" >July</option>
						<option value="8" >August</option>
						<option value="9" >September</option>
						<option value="10" >October</option>
						<option value="11" >November</option>
						<option value="12" >December</option>
					  </select>
				 </div>
				<div class="col-md-2">
				      <select data-baseURL="{{ url('/') }}" id="aap_year" class="form-control" name="aap_year" >
						<option value="0" >Select</option>
						@for($year = date('Y'); $year >= 2020; $year--)
							<option value="{{ $year }}">{{ $year }}</option>
						@endfor
					  </select>
				 </div>
				<div class="col-md-2">
				    <button data-baseURL="{{ url('/') }}" id="submit_dt" type="submit" class="btn btn-success mr-2">Submit</button>
					
				</div> 		
			  </div>
			@if($users->count() != 0)
			<div style="margin:0px 0px 10px 0px;" align="right">
				<a href="{{ url('/all/applicants') }}" id="export" class="btn btn-success">Export to Excel</a>
		    </div>
			@endif
			<div class="table-responsive">
            <table id="sortable-table-1" class="table table-striped">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
				</tr>
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($users->count() != 0)
				@foreach ($users as $datas)
					<tr>
					  <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}.</td>
					  <td> {{ $datas->name  }} </td>
					  <td> <?php $user_companies = AdminController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
					  <td> {{ $datas->emp_id  }} </td>
					  <td> {{ $datas->dept  }} </td>
					</tr>
				<?php $count++; ?>
				@endforeach
				
				@else
					<p>Sorry No Data!!</p>
				@endif
			  </tbody>
			</table>
			{{ $users->links('pagination::bootstrap-5') }}
            </div>
		  </div>
		</div>
	  </div>
	</div>
	<div class="loding"></div>
</div>
<script>
@endsection		
