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
		<?php if(Auth::user()->username != null && !empty(Auth::user()->username)) { ?>
		<div style="display:none" class="col-md-12 grid-margin stretch-card average-price-card">
			<div style="background:#d44358" class="card text-white">
				<div class="card-body">
					<h3 style="color: #fff;">Click here to check your paystubs</h3><br>
					<h3 style="color: #fff;">(<a style="color: #fff;" target="_blank" href="https://www.businessonlinepayroll.com/SPF/Login/Auth.aspx">https://www.businessonlinepayroll.com/SPF/Login/Auth.aspx</a>)</h3><br>
					<h3 style="color: #fff;">Your username is <a style="color: #fff; font-size:25px;mfont-weight:900; text-decoration: underline blue;">{{ Auth::user()->username }}</a></h3>
					<h3 style="color: #fff;">Password is your full Social Security Number: <a style="color: #fff;font-size: 25px;font-weight: 900;">xxx-xx-</a><a style="color: #fff;font-size: 25px;font-weight: 900;text-decoration: underline blue;">xxxx</a></h3><br>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="col-md-6 grid-margin stretch-card average-price-card">
			<div class="card text-white" style="    background: #ff5722 !important;">
				<div class="card-body">
					<div class="d-flex justify-content-between pb-2 align-items-center">
						<h2 class="font-weight-semibold mb-0"><?php if(Auth::user()->status == 1) { ?><a  style="color:#fff" href="{{ route('time-sheets.create') }}">Add Hours</a><?php } else { ?> <a>Account Verification in Progress.</a><?php } ?></h2>
						<div class="icon-holder" style="  border: #ff5722 !important; background: #ff5722 !important;"> 
							<i class="fa fa-plus-circle"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 grid-margin stretch-card average-price-card">
			<div class="card text-white" style="    background: #4caf50 !important;">
				<div class="card-body">
					<div class="d-flex justify-content-between pb-2 align-items-center">
						<h2 class="font-weight-semibold mb-0"><a  style="color:#fff" href="{{ route('list-issue.create') }}">Add Issues</a></h2>
						<div class="icon-holder" style="  border: #4caf50 !important; background: #4caf50 !important;"> 
							<a  style="color:#fff" href="{{ route('list-issue.create') }}"><i class="fa fa-plus-circle"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 grid-margin stretch-card average-price-card">
			<div class="card text-white" style="    background: #adaf4c !important;">
				<div class="card-body">
					<div class="d-flex justify-content-between pb-2 align-items-center">
						<h2 class="font-weight-semibold mb-0">${{ $hourley_rate }}</h2>
						<div class="icon-holder" style="  border: #adaf4c !important; background: #adaf4c !important;"></div>
					</div>
					<div class="d-flex justify-content-between">
						<h5 class="font-weight-semibold mb-0">Hourly  Rate</h5>
					</div>
				</div>
			</div>
		</div>
		<!--div style="display:none" class="col-md-6 grid-margin stretch-card average-price-card">
			<div class="card text-white" style="    background: #774caf !important;">
			<div class="card-body">
				<div class="d-flex justify-content-between pb-2 align-items-center">
				<h2 class="font-weight-semibold mb-0">Pay Date : {{ $paydate }} </h2>
			</div>
			<div class="d-flex justify-content-between">
			<?php
				if(!empty($last_pay)){$pay_per = ($last_pay/80) * 100; }
				else { $pay_per =0; }
			?>
			<?php if($last_pay <=  79){ ?>
			<style>
			.progress-bar {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-orient: vertical;
			-webkit-box-direction: normal;
			-ms-flex-direction: column;
			flex-direction: column;
			-webkit-box-pack: center;
			-ms-flex-pack: center;
			justify-content: center;
			color: #fff;
			text-align: center;
			white-space: nowrap;
			background-color: #ff2300;
			-webkit-transition: width 0.6s ease;
			transition: width 0.6s ease;
			}
			</style>
			<div class="progress">
			<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $pay_per; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $pay_per; ?>%">
			<h4>{{ $last_pay }} Hours</h4>
			</div>
			</div><?php }elseif($last_pay ==  80){ ?>
			<style>
			.progress-bar {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-orient: vertical;
			-webkit-box-direction: normal;
			-ms-flex-direction: column;
			flex-direction: column;
			-webkit-box-pack: center;
			-ms-flex-pack: center;
			justify-content: center;
			color: #fff;
			text-align: center;
			white-space: nowrap;
			background-color:  #31a16d;
			-webkit-transition: width 0.6s ease;
			transition: width 0.6s ease;
			}
			</style>
			
			<div class="progress">
			<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $pay_per; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $pay_per; ?>%">
			<h4>{{ $last_pay }} Hours</h4>
			</div>
			</div><?php }else{ ?>
			
			<style>
			.progress-bar {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-orient: vertical;
			-webkit-box-direction: normal;
			-ms-flex-direction: column;
			flex-direction: column;
			-webkit-box-pack: center;
			-ms-flex-pack: center;
			justify-content: center;
			color: #fff;
			text-align: center;
			white-space: nowrap;
			background-color:  #3149a1;
			-webkit-transition: width 0.6s ease;
			transition: width 0.6s ease;
			}
			</style>
			<div class="progress">
			<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $pay_per; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $pay_per; ?>%">
			<h4>{{ $last_pay }} Hours</h4>
			</div>
			</div><?php } ?>
			</div>
			</div>
			</div>
			</div-->
		<!--div class="col-md-6 grid-margin stretch-card average-price-card">
			<div class="card text-white" style="    background: #4caf58 !important;">
			<div class="card-body">
				<div class="d-flex justify-content-between pb-2 align-items-center">
				<h2 class="font-weight-semibold mb-0"><a style="color:#fff" href="{{ url('/my/driving-license/upload') }}">Upload Driver License</a></h2>
				<div class="icon-holder" style="  border: #4caf58 !important; background: #4caf58 !important;">
			<i class="fa fa-plus-circle"></i>
				</div>
				</div>				
			</div>
			</div>			
			</div-->
		<!--div class="col-md-6 grid-margin stretch-card average-price-card">
			<div class="card text-white" >
			<div class="card-body">
				<div class="d-flex justify-content-between pb-2 align-items-center">
				<h2 class="font-weight-semibold mb-0">Driver License</h2><br>
				<?php if(Auth::user()->drivers_license != null){ ?>
			<img style=" margin-top: 15px; width: 150px;" src="{{ url('/assets/uploads/driving-license') }}/{{ Auth::user()->drivers_license }}">
			<?php } ?>
			</div>
			</div>
			</div>
			</div-->
		<!--div class="col-md-6 grid-margin stretch-card average-price-card">
			<div style="background:#e40d12" class="card text-white">
			<div class="card-body">
			<h3>"ilogstaffing Contact INFO"</h3>
			<br>
			<ul>
			<li><h5>Tech support  1-844-255-3487  option 1 and 1</h5></li>
			<li><h5>Human Resources  1-844-255-3487  option 1 and 2</h5></li>
			<li><h5>Accounts and Payroll issues 1-844-255-3487  option 1 and 3</h5></li>
			</ul>
			<hr style="    border-top: 1px solid #fff;">
			<ul>
			<li><h5>fax:1-855-933-3487</h5></li>
			<li><h5>email: info@ilogstaffing.com</h5></li>
			<li><h5>email: humanresources@ilogstaffing.com</h5></li>
			<li><h5>email: finance@ilogstaffing.com<h5></li>
			</ul>
			<hr style="    border-top: 1px solid #fff;">
			<ul>
			<li><h5>email: humanresources@ilogstaffing.com</h5></li>
			<li><h5>email: accounts@ilogstaffing.com</h5></li>
			</ul>
			</div>
			</div>
			</div-->
	    </div>
	<div class="row">
		<div class="col-lg-12 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Clock In & Clock Out ({{ date("M d, Y", strtotime($current_date_time)) }})</h4>
					<p  class="card-description">User</p>
					<h4 class="user-naaam">{{ Auth::user()->name }}</h4>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th class="sortStyle unsortStyle">Sr. No <i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle">Name <i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle">Department <i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle"> Company <i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle"> House<i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle"> Time In <i class="mdi mdi-chevron-down"></i> </th>
									<th class="sortStyle unsortStyle"> Time Out <i class="mdi mdi-chevron-down"></i> </th>
								</tr>
							</thead>
							<tbody id="result">
								<?php $count = 1; ?>
								@if($data->count() != 0)
								@foreach ($data as $datas)
								<tr>
									<td><?php echo $count; ?>.</td>
									<td> {{ $datas->users->name  }}</td>
									<td> {{ $datas->users->dept  }} </td>
									<td> {{ $datas->companies->company  }}</td>
									<td> {{ $datas->houses->house_add  }} </td>
									<td> {{ $datas->time_in	  }} </td>
									<td> {{ $datas->time_out	  }} </td>
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
					</div>
				</div>
			</div>
		</div>
	</div>
   <div class="loding"></div>
</div>
@endsection