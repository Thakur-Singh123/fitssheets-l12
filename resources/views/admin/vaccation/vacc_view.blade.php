@extends('layouts.master')
@section('content')
<?php use App\Http\Controllers\Admin\VaccationController; ?>
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
			<h4 class="card-title">Vacations</h4>
			<div class="d-flex justify-content-end align-items-center mb-3">
				<a href="{{ route('vaccations.create') }}" class="btn btn-primarys" style="background-color:#1c45ef; color: white; border-radius: 6px; padding: 10px 15px; font-size: 14px;">
					<i class="fa fa-plus mr-1"></i>
					Add Vacation
				</a>
			</div>
			<table class="table table-striped">
				<thead>
					<tr>
						<th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i> </th>
						<th class="sortStyle unsortStyle"> User Name<i class="mdi mdi-chevron-down"></i></th>
						<th class="sortStyle unsortStyle"> Total Vaccation Hrs<i class="mdi mdi-chevron-down"></i></th>
						<th class="sortStyle unsortStyle"> Valid Upto<i class="mdi mdi-chevron-down"></i> </th>
						<th class="sortStyle unsortStyle"> Created <i class="mdi mdi-chevron-down"></i></th>
						<th class="sortStyle unsortStyle"> Actions <i class="mdi mdi-chevron-down"></i></th>
					</tr>
				</thead>
			  <tbody>
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
					<tr>
						<td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}.</td>
						<td style="display:none">
							<img src="../../../assets/images/faces-clipart/pic-1.png" alt="image"> </td>
						<td> 
							<?php echo $user = VaccationController::user($datas->user_id); ?>
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
							<a href="{{ url('/vaccations/' . $datas->id . '/edit') }}" title="Edit">
								<i class="fa fa-pencil"></i>
							</a>
							{{-- <a style="margin-left: 5px;" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_vaccationsstn" title="Delete"><i class="fa fa-trash-o"></i></a> --}}
							<a
								style="margin-left: 5px"
								data-baseURL="{{ url('/') }}"
								data-vocation_id="{{ $datas->id  }}"
								class="delete_vocation_record"
								title="Delete"
								><i class="fa fa-trash-o"></i
							></a>
						</td>
					</tr>
				<?php $count++; ?>
				@endforeach
				@else
					<tr>
					<td colspan="6" class="no-data">
						Sorry, No data found!
					</td>
                   </tr>
				@endif
			  </tbody>
			</table>
			{{ $data->links('pagination::bootstrap-5') }}
		  </div>
		</div>
	  </div>
	</div>
	<div class="loading"></div>
</div>
@endsection		
