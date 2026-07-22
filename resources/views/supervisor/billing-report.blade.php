@extends('layouts.supervisor')
@section('content')
<?php use App\Http\Controllers\Supervisor\UserssController; ?>
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
	<div style="    margin-top: 10px;" align="left">
		<a href="{{ url('/suser/export/billing/report') }}" id="export" class="btn btn-success">Export to Excel</a>
	</div>
	<table id="sortable-table-1" class="table table-striped table-responsive">
		<thead>
			<tr>
				<th class="sortStyle unsortStyle"> Sr No.<i class="mdi mdi-chevron-down"></i> </th>
				<th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
				<th class="sortStyle unsortStyle"> Last Name<i class="mdi mdi-chevron-down"></i> </th>
				<th class="sortStyle unsortStyle"> First Name<i class="mdi mdi-chevron-down"></i> </th>
				<th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
				<th class="sortStyle unsortStyle"> Total Hours</th>
				<th class="sortStyle unsortStyle"> Hours Rate($)<i class="mdi mdi-chevron-down"></i> </th>
				<th class="sortStyle unsortStyle"> Total Pay<i class="mdi mdi-chevron-down"></i> </th>
			</tr>
		</thead>
		<tbody id="result">
			<?php $count = 1; ?>
			@if($data->count() != 0)
			@foreach ($data as $datas)
			<tr>
				<td><?php echo $count; ?>.</td>
				<td>{{ $datas->emp_id  }}</td>
				<td> {{ $datas->last_name  }} </td>
				<td> {{ $datas->first_name  }} </td>
				<td> {{ $datas->companies_id   }} </td>
				<td><?php $total_time = UserssController::total_time($datas->id); 
				if($total_time <=  79){
						echo "<p style='text-align:center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:red' >".$total_time."</p>";
				}elseif($total_time ==  80){
						echo "<p style='text-align:center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_time."</p>";
				}else{
					echo "<p style='text-align:center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_time."</p>";
				}
					
					?></td>
				<td> ${{ $datas->hourst_rate  }} </td>
				<td> ${{ $total_time*$datas->hourst_rate }} </td>
			</tr>
			<?php $count++; ?>
			@endforeach
			@else
			<tr>
				<td colspan="16" class="no-data">
					Sorry, No data found!
				</td>
			</tr>
			@endif
		</tbody>
	</table>
	<div class="pagination">
		<?php echo $data->links(); ?>
	</div>
</div>
</div>
</div>
</div>
<div class="loding"></div>
</div>
@endsection