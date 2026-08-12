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
<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-end align-items-center">
            <label for="vaccine_status" class="mb-0 mr-2 font-weight-bold" style="font-size:14px;">
                Sort By Status :
            </label>
            <select data-baseURL="{{ url('/') }}"
				id="vaccine_status"
				name="vaccine_status"
				class="form-control"
				style="width:100px;height:100px;">
                <option value="1">Yes</option>
                <option value="0">No</option>
                <option value="2">Need Action</option>
            </select>
        </div>
    </div>
</div>
	<div class="row">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Users Vaccine Reports</h4>
					@if($users->count() != 0)
					<div style="margin-top: 10px;display:none" align="left">
						<a href="{{ url('/all/users/') }}" id="export" class="btn btn-success">Export to Excel</a>
					</div>
					@endif
					<div class="table-responsive">
					<table id="sortable-table-1" class="table table-striped">
						<thead>
							<tr>
							<th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle"> Email<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle"> Vaccine Status<i class="mdi mdi-chevron-down"></i> </th>
							<th class="sortStyle unsortStyle"> Vaccine Card<i class="mdi mdi-chevron-down"></i> </th>
							</tr>
						</thead>
						<tbody id="result">
						<?php $count = 1; ?>
						@if($users->count() != 0)
							@foreach ($users as $datas)
								<tr>
								<td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}.</td>
								<td> {{ $datas->email  }} </td>
								<td> {{ $datas->name  }} </td>
								<td> <?php if($datas->covid_report == ""){ echo "<p style='color: #000;background: yellow;width: 50%;text-align: center;padding: 5px;border-radius: 10px;'>--</p>"; }elseif( $datas->covid_report == '0'){ echo "<p style='color: #fff;background: red;width: 50%;text-align: center;padding: 5px;border-radius: 10px;'>No</p>";}elseif( $datas->covid_report != "" && $datas->covid_report != '0'){ echo "<p style='color: #fff;background: green;width: 50%;text-align: center;padding: 5px;border-radius: 10px;'>yes</p>"; }else{ echo "<p style='color: #000;background: yellow;width: 50%;text-align: center;padding: 5px;border-radius: 10px;'>--</p>"; } ?>  </td>
								<td> <?php if($datas->covid_report != null && $datas->covid_report != '0' ){ ?>
								<img style=" margin-top: 15px; width: 152px;height: 156px;border-radius: inherit !important;" src="{{ url('/assets/uploads/covid-report') }}/{{ $datas->covid_report }}">
								<?php } else{ ?>
									<p class="no-data">No Report found!</p>	
								<?php } ?> </td>
								</tr>
							<?php $count++; ?>
							@endforeach
							@else
							<tr>
								<td colspan="5" class="no-data">
									Sorry, No data found!
								</td>
							</tr>
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
@endsection		
