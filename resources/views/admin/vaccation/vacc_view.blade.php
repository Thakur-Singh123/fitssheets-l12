@extends('layouts.master')

@section('content')
<?php use App\Http\Controllers\Admin\VaccationController; ?>
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
			<h4 class="card-title">Vacations</h4>
			<p  class="card-description"><a href="{{ route('vaccations.create') }}"> Add Vacation <i class="fa fa-plus-circle"></i></a></p>
			<table class="table table-striped">
			  <thead>
				<tr>
				  <th> # </th>
				  <th> User </th>
				   <th> Total Vaccation Hrs</th>
				   <th> Valid Upto </th>
				  <th> Created </th>
				  <th> Actions </th>
				</tr>
			  </thead>
			  <tbody>
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
					<tr>
					  <td><?php echo $count; ?></td>
					  <td style="display:none">
						<img src="../../../assets/images/faces-clipart/pic-1.png" alt="image"> </td>
					  <td> 
					  		<?php echo $user = VaccationController::user($datas->user_id); 
				?>

					   </td>
					   <td> 
					  		<?php echo $num = (int) $datas->vacc_sl + (int) $datas->vacc_vc + (int) $datas->vacc_be + (int) $datas->vacc_jd; ?>

					   </td>
					    <td> 
					    	<?php
					    	$vacc_frm    = explode('_', $datas->vacc_frm);
					    	$vacc_frm = implode("/", $vacc_frm); 
					    	$vacc_frm = date("M d", strtotime($vacc_frm));
					    	$vacc_to    = explode('_', $datas->vacc_to);
					    	$vacc_to = implode("/", $vacc_to); 
					    	$vacc_to = date("M d, Y", strtotime($vacc_to));
					    	?>
					  		<?php echo $vacc_frm." to ".$vacc_to; ?>

					   </td>
					  <td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>
					  <td>
						<a  href="{{ route('enter-vaccation.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
						<a style="margin-left: 5px;" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_vaccationsstn" title="Delete"><i class="fa fa-trash-o"></i></a>
					  </td>
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
	<div class="loading"></div>
	</div>
@endsection		
