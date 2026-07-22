@extends('layouts.master')

@section('content')

<style>
.table.table-striped tbody tr:nth-child(odd) {
    background: #fff;
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
		  	<h2>Payperiods</h2>
				<p><a href="{{ route('payperiods.create') }}"> <i class="fa fa-plus-circle"></i>Add Payperiod </a></p>
				<form method="POST" action="{{ url('/payperiods/astore') }}" class="forms-sample">@csrf
				<button type="submit" class="btn btn-success mr-2"><i class="fa fa-plus-circle"></i>Auto Add Payperiod</button></form>
			<table class="table table-striped">
			  <thead>
				<tr>
				  <th> # </th>
							  <th> Payperiod </th>
							  <th> Company </th>
							  <th> Start Date </th>
							  <th> End Date </th>
							  <th> Pay Date </th>
								<th> Reports </th>
							  <th> Actions </th>
							  
							  
				</tr>
			  </thead>
			 <tbody>
						 <?php $count = 1; ?>
						  @if($data->count() != 0)
							@foreach ($data as $datas)
								<?php
								  $TodayDate = new DateTime();	
									$bet_dates = explode('-',$datas->payperiod_value);
									if(isset($bet_dates)){
										$from_date    = $bet_dates[0];
										$to_date    = $bet_dates[1];
									}
									$xto_date = explode('_',$to_date);
									$xto_date = implode('-',$xto_date);
									$xfrom_date = explode('_',$from_date);
									$xfrom_date = implode('-',$xfrom_date);
									$xtto_date = new DateTime($xto_date);
									$xtfrom_date  = new DateTime($xfrom_date);
								  ?>
								  <?php  if (
									  $TodayDate->format('y-m-d') >= $xtfrom_date->format('y-m-d') && 
									  $TodayDate->format('y-m-d') <= $xtto_date->format('y-m-d')){
									  
									?>
								<tr style="background: #0aab52;">
								
								  <td style="color: #fff;"><p style="color: #fff;
    font-size: 15px;
    text-transform: uppercase;
    font-weight: bold;">CP</p><p style="color: #fff;
    font-size: 15px;
    "><?php echo $count; ?></p></td>
								  <td style="color: #fff;"> {{ $datas->payperiod  }} </td>
								  <td style="color: #fff;"> {{ $datas->companies->company  }} </td>
								  
								
								  <td> <b style="font-size: 18px;color: #fff;">{{ date('d-M-Y', strtotime($xfrom_date)) }}</b> </td>
								  <td> <b style="font-size: 18px;color: #fff;">{{ date('d-M-Y',strtotime($xto_date)) }}</b> </td>
								 
								  <td> <b style="font-size: 18px;color: #cad42a;">{{ date('M d, Y', strtotime('+5 days', strtotime($xto_date))) }}</b></td>
								   <td>
								  
								  
								  <a href="https://fitssheets.com/user/payroll/search/csvpostdata/<?php echo $datas->payperiod_value; ?>/<?php echo $datas->companies_id; ?>" id="ecsv" class="btn btn-success">Export to CSV</a>
								  <a href="{{ url('/userss/payroll-file/') }}/<?php echo $datas->payperiod_value; ?>/<?php echo $datas->companies_id; ?>" id="ecsv" class="btn btn-success">Export Payroll</a>
								  </td>
								  <td style="color: #fff;">
									<a  href="{{ route('payperiods.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
									<a style="margin-left: 5px;" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_payperiod" title="Delete"><i class="fa fa-trash-o"></i></a>
								  </td>
								  
								 
								
								</tr>
								
									  <?php } else{ ?>
									  
								<tr>
								  <td><?php echo $count; ?></td>
								  <td> {{ $datas->payperiod  }} </td>
								  <td> {{ $datas->companies->company  }} </td>
								  
								
								  <td> <b style="font-size: 18px;color: #f53838;">{{ date('d-M-Y', strtotime($xfrom_date)) }}</b> </td>
								  <td> <b style="font-size: 18px;color: #f53838;">{{ date('d-M-Y',strtotime($xto_date)) }}</b> </td>
								 
								  <td> <b style="font-size: 18px;color: #0aab52;">{{ date('M d, Y', strtotime('+5 days', strtotime($xto_date))) }}</b></td>
								   <td>
								  
								  <a href="https://fitssheets.com//user/payroll/search/csvpostdata/<?php echo $datas->payperiod_value; ?>/<?php echo $datas->companies_id; ?>" id="ecsv" class="btn btn-success">Export to CSV</a>
								   <a href="{{ url('/userss/payroll-file/') }}/<?php echo $datas->payperiod_value; ?>/<?php echo $datas->companies_id; ?>" id="ecsv" class="btn btn-success">Export Payroll</a>
								  </td>
								  <td>
									<a  href="{{ route('payperiods.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
									<a style="margin-left: 5px;" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_payperiod" title="Delete"><i class="fa fa-trash-o"></i></a>
								  </td>
								  
								 
								</tr>
									  <?php } ?>
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
