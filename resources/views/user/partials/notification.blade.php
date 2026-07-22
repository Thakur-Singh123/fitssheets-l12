<?php
// echo "<pre>";
// print_r($data);
// die;
?>
<style>
.modal-body p{
	color:#fff;
	font-size:28px;
	text-align: center;
}
</style>
@if($data->count() != 0)
<div id="myModal" class="modal fade">
	<div class="modal-dialog">
		<div style="top: 100px;background: black;height:70vh" class="modal-content">
			<div class="modal-header">
				<button style=" display:none;color: #fff;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div style="padding-top:45px" class="modal-body">
				<?php foreach ($data as $datas) { ?>
				    <?php if(Auth::user()->id == $datas->users->id) { ?>
					    {!!html_entity_decode($datas->emp_notfications->not_text)!!}
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
@endif
@if(Auth::user()->drivers_license == "" && Auth::user()->drivers_license == '1')
<div style="display:none" id="myModal" class="modal fade">
	<div class="modal-dialog">
		<div style="top: 100px;background: black;height:70vh" class="modal-content">
			<div class="modal-header">
				<button style=" display:none;color: #fff;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div style="padding-top:45px" class="modal-body">
				<?php $date_now = date("2021-01-01"); ?>
				<b style="color:#fff;font-size: 25px;">Please upload your drivers license or ID to start the activation process. </b><br><br><br>
				<b style="color:#fff;font-size: 25px;">After uploading, call 1-844-255-3487 <?php if(date('y-m-d', strtotime(Auth::user()->created_at)) < $date_now ){ echo "opt 1 and 1"; }else{ echo "opt 1 and 9"; } ?> and leave your name and position applied for.  A Human Resources representative will call you.</b> 
			</div>
		</div>
	</div>
</div>
@endif
@if(Auth::user()->covid_report == "" || Auth::user()->covid_report == "0")
<div  style="display:none" id="covid-myModal" class="modal fade">
	<div class="modal-dialog">
		<div style="top: 100px;background: #14a800;height:70vh" class="modal-content">
			<div class="modal-header">
				<button style=" display:none;color: #fff;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div style="padding-top:45px;text-align: center;" class="modal-body">
				<?php $date_now = date("2021-01-01"); ?>
				<b style="color:#fff;font-size: 45px;">Are you vaccinated?</b><br><br>
				<div style="display: flex;flex-flow: row;justify-content: space-evenly;align-items: center;" class="vaccine_anw">
					<b style="color:orange;font-size: 50px;"><a style="color:orange;" href="{{ url('/') }}/my/covid-report/upload">Yes</a></b>
					<b style="color:red;font-size: 50px;"><a style="color:red;" href="{{ url('/') }}/my/covid-report/update_ne">No</a></b>
				</div>
			</div>
		</div>
	</div>
</div>
@endif
<div id="myModal" class="modal fade">
	<div style="display:none" class="modal-dialog">
		<div style="top: 100px;background: black;height:70vh" class="modal-content">
			<div class="modal-header">
				<button style=" display:none;color: #fff;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div style="padding-top:45px" class="modal-body">
				<?php $date_now = date("2021-01-01"); ?>
				<b style="color:#fff;font-size: 25px;">Ilog is migrating from BUSINESS ONLINE PAYROLL to GUSTO payroll SOON"</b> 
			</div>
		</div>
	</div>
</div>