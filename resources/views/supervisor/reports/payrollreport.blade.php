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
	<div class="row">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Users</h4>
					<form >
						<div class="row grid-margin stretch-card">
							<div class="col-md-6 ">
								<div class="card">
									<div class="card-body">
										<h4 class="card-titles">Select payperiod</h4>
										<div class="form-group">
											<select class="form-control form-control-lg" id="payperiod" name="payperiod">
												<?php if(isset($payperiods_dates)){ ?>
												<?php foreach($payperiods_dates as $payperiods_date){?>
												<option value="<?php echo $payperiods_date->payperiod_value; ?>"><?php echo $payperiods_date->payperiod; ?></option>
												<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6 grid-margin stretch-card">
								<div class="card">
									<div class="card-body">
										<h4 class="card-titles">Select company</h4>
										<div class="form-group">
											<select  id="search_by_comp" class="form-control" name="search_by_comp" >
												<option value="0" >Select</option>
												@if($companies->count() != 0)
													@foreach ($companies as $company)
													<option value="{{ $company->id }}" >{{ $company->company }}</option>
													@endforeach
												@endif
											</select>
										</div>
									</div>
								</div>
							</div>
							<button data-baseURL="{{ url('/') }}" id="spayroll" type="button" class="btn btn-success" style="font-size:12px;line-height:1.2;margin: 0px 0px 0px 15px;width: 60px;">Submit</button>
						</div>
					</form>
					<div style="    margin-top: 10px;" align="right">
						<a href="{{ url('/user/export/all') }}" id="export" class="btn btn-success"><i class="fa fa-calendar"></i>Export to Excel</a>
					</div>
					<br>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
								<th class="sortStyle unsortStyle"> Date<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Last Name<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> First Name<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Payroll Code<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Hours<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Hours Rate($)<i class="mdi mdi-chevron-down"></i> </th>
								</tr>
							</thead>
							<tbody id="result"></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
   <div class="loding"></div>
</div>
@endsection