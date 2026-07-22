@extends('layouts.user')
@section('content')
<div class="content-wrapper">
 @if(session('success'))
    <div class="alert alert-success">
      <h4>{{ session('success') }}</h4>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">
      <h4>{{ session('error') }}</h4>
    </div>
    @endif
	<div class="row">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Vaccations</h4>
					<div class="d-flex justify-content-end mb-3">
						<a href="{{ route('time-sheets.index') }}" class="btn btn-secondary">
						    <i class="fa fa-arrow-left"></i> Back
						</a>
					</div>
					<div class="table-responsive">
						<table id="sortable-table-1" class="table dataTable table-striped">
							<thead>
								<tr>
									<th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle"> From<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle"> To<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle"> Reporting<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle"> Created<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle"> Actions<i class="mdi mdi-chevron-down"></i></th>
									<th class="sortStyle unsortStyle">Status<i class="mdi mdi-chevron-down"></i></th>
								</tr>
							</thead>
							<tbody id="result">
								<?php $count = 1; ?>
								@if($data->count() != 0)
								@foreach ($data as $datas)
								<tr>
									<td><?php echo $count; ?>.</td>
									<td>
										<?php $vacc_frm    = explode('_', $datas->vacc_start);
											$vacc_frm = implode("-", $vacc_frm); 
												echo date('M d, Y', strtotime($vacc_frm));
											?>
									</td>
									<td><?php $vacc_end    = explode('_', $datas->vacc_end);
										$vacc_end = implode("-", $vacc_end); 
											echo date('M d, Y', strtotime($vacc_end));
										?></td>
									<td><?php $vacc_rbu    = explode('_', $datas->vacc_rbu);
										$vacc_rbu = implode("-", $vacc_rbu); 
											echo date('M d, Y', strtotime($vacc_rbu));
										?></td>
									<td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>
									<td>
										<a  href="{{ route('enter-vaccation.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
										<a style="margin-left: 5px;display:none" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_vaccations" title="Delete"><i class="fa fa-trash-o"></i></a>
									</td>
									<td>
										<?php if($datas->vacc_status==0){echo "<p style='padding: 10px;
											text-align: center;
											color: #000;
											background: yellow;
											font-size: 16px;
											font-weight: bold;
											width: 80px;
											margin: 0px 0px 0px 40px;
											border-radius: 10px;'>Pending</p>";}elseif($datas->vacc_status==1){echo "<p style='padding: 10px;
											text-align: center;
											color: #fff;
											background: green;
											font-size: 14px;
											font-weight: bold;
											width: 80px;
											margin: 0px 0px 0px 40px;
											border-radius: 10px;'>Approved</p>";}elseif($datas->vacc_status==2){echo "<p style='padding: 10px;
											text-align: center;
											color: #fff;
											background: red;
											font-size: 14px;
											font-weight: bold;
											width: 80px;
											margin: 0px 0px 0px 40px;
											border-radius: 10px;'>Decline</p>";}else{echo "<p style='padding: 10px;
											text-align: center;
											color: #000;
											background: yellow;
											font-size: 14px;
											font-weight: bold;
											width: 80px;
											margin: 0px 0px 0px 40px;
											border-radius: 10px;'>Pending</p>";} 
										?>
									</td>
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
						<br>
					    {{ $data->links('pagination::bootstrap-5') }}
					</div>
				</div>
			</div>
		</div>
	</div>
   <div class="loding"></div>
</div>
@endsection