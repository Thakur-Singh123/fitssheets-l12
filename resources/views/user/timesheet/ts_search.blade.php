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
					<form class="form-sample">
						<div class="row">
							<div class="col-md-2">
								<h4 class="card-title">Search Timesheets</h4>
							</div>
							<div class="col-md-4">
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">From Date</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="frm_dt" name="frm_dt">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group row">
									<label class="col-sm-3 col-form-label">To date</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="to_dt" name="to_dt">
									</div>
								</div>
							</div>
							<div class="col-md-2">
								<button data-baseURL="{{ url('/') }}" id="submit_date" type="submit" class="btn btn-success mr-2">Submit</button>
							</div>
						</div>
					</form>
					<div align="left">
						<a href="{{ route('export.time-sheet') }}" id="export" class="btn btn-success">Export to Excel</a>
					</div>
					<br>
					<table id="sortable-table-1" class="table dataTable table-striped table-responsive">
						<thead>
							<tr>
								<th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Email<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> House <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Time In <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Time Out <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Hours Worked <i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Date <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Hours Rate<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Approved <i class="mdi mdi-chevron-down"></i></th>
								<th class="sortStyle unsortStyle"> Remarks<i class="mdi mdi-chevron-down"></i> </th>
								<th class="sortStyle unsortStyle"> Actions<i class="mdi mdi-chevron-down"></i> </th>
							</tr>
						</thead>
						<tbody id="result">
						</tbody>
					</table>
					<br>
				</div>
			</div>
		</div>
	</div>
	<div class="loding"></div>
</div>
@endsection