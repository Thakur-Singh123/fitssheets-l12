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
			<h4 class="card-title">Inactive Users(Last Clock In/Out)</h4>
						 <div class="row">
				<div class="col-md-4">
				  <div class="form-group row">
					<h2>Inactive Users By Month</h2>
				  </div>
				</div>
				<div class="col-md-2">
				     <input type="text" class="form-control" id="frm_dt" name="frm_dt" placeholder="From">
				 </div>
				<div class="col-md-2">
				      <input type="text" class="form-control" id="to_dt" name="to_dt" placeholder="To">
				 </div>
				<div class="col-md-2">
				       <button data-baseURL="{{ url('/') }}" id="inactive_user" type="submit" class="btn btn-success mr-2">Submit</button>
				 
				 </div> 		
		
			  </div>
			@if($users->count() != 0)
			<div style="margin-top: 10px;" align="left">
				<a href="{{ url('/inactive/employees') }}" id="export" class="btn btn-success">Export to Excel</a>
		    </div>
			@endif
			<table id="sortable-table-1" class="table table-striped table-responsive">
			  <thead>
				<tr>
				  <th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Last Clock In Date<i class="mdi mdi-chevron-down"></i> </th>
				  <th class="sortStyle unsortStyle"> Last Clock In Time<i class="mdi mdi-chevron-down"></i> </th>
					<th class="sortStyle unsortStyle"> Last Clock Out Time<i class="mdi mdi-chevron-down"></i> </th>

				</tr>
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($users->count() != 0)
				@foreach ($users as $datas)
					<tr>
					  <td><?php echo $count; ?></td>
					  <?php $user_companies = AdminController::user_companies($datas->id); ?>
					  <td> {{ $datas->name  }}<?php echo ' <b>('.$user_companies.')</b>'; ?> </td>
						<?php $user_last_clock = AdminController::user_last_clock($datas->id);?>
						
					  <td >  <?php 
					  
						if(isset($user_last_clock)) {
							
							if($user_last_clock->hours_day != null){
								$user_last_clock_day =  explode('_',$user_last_clock->hours_day);
								$user_last_clock_day =  implode('-',$user_last_clock_day);
								echo date('M d, Y', strtotime($user_last_clock_day));
							}
							
						} ?>
					</td>
					  <td> <?php 
					  
						if(isset($user_last_clock)) {
							
							if($user_last_clock->time_in != null){
								echo $user_last_clock->time_in;
							}
							
						} ?> </td>
						<td> <?php 
					  
						if(isset($user_last_clock)) {
							
							if($user_last_clock->time_out != null){
								echo $user_last_clock->time_out;
							}
							
						} ?> </td>
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
