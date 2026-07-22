@extends('layouts.master')

@section('content')
<?php use App\Http\Controllers\Admin\SupervisorInfoController; ?>
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
			<h4 class="card-title">Supervisors</h4>
			<p  class="card-description"><a href="{{ route('users.create') }}"> Add Supervisor <i class="fa fa-plus-circle"></i></a> </p>
			<form class="form-sample">
			  <div class="row">
				<div class="col-md-10">
				  <div class="form-group row">
					<label class="col-sm-3 col-form-label">Search User</label>
					<div class="col-sm-9">
					  <input type="text" class="form-control" id="srch_user" name="srch_user">
					</div>
				  </div>
				</div>
				<div class="col-md-2">
				 <button data-baseURL="{{ url('/') }}" id="submit_user" type="submit" class="btn btn-success mr-2">Submit</button>
				 </div>
			  </div>
			  
			</form>
			<table id="sortable-table-1" class="table table-striped table-responsive">
			  <thead>
				<tr>
					<th class="sortStyle unsortStyle"> #<i class="mdi mdi-chevron-down"></i> </th>
					<th class="sortStyle unsortStyle"> Supervisor Color<i class="mdi mdi-chevron-down"></i> </th>
					<th class="sortStyle unsortStyle"> Emp ID<i class="mdi mdi-chevron-down"></i> </th>
					<th>Actions </th>
					<th class="sortStyle unsortStyle"> Email<i class="mdi mdi-chevron-down"></i> </th>
					<th class="sortStyle unsortStyle"> Name<i class="mdi mdi-chevron-down"></i> </th>
					 <?php if(Auth::user()->id == 72 || Auth::user()->id == 50 || Auth::user()->id == 282 ){ ?>
						<?php }else{ ?>
					<th class="sortStyle unsortStyle"> User Password<i class="mdi mdi-chevron-down"></i> </th>
						<?php } ?>
					<th class="sortStyle unsortStyle"> Department<i class="mdi mdi-chevron-down"></i> </th>
					<th class="sortStyle unsortStyle"> Company<i class="mdi mdi-chevron-down"></i> </th>
					<th class="sortStyle unsortStyle">Time<i class="mdi mdi-chevron-down"></i></th>
					<th class="sortStyle unsortStyle">Day<i class="mdi mdi-chevron-down"></i></th>
					<th class="sortStyle unsortStyle"> Created<i class="mdi mdi-chevron-down"></i> </th>
					<th>Status </th>
				</tr>
			  </thead>
			  <tbody id="result">
			  <?php $count = 1; ?>
			  @if($data->count() != 0)
				@foreach ($data as $datas)
					<tr>
					  <td><?php echo $count; ?></td>
					  <td> 
					  <?php if($datas->color_field != ""){ ?>
					  <div class="supre_vi_color"><p style="text-align: center;
    background: {{ $datas->color_field  }};
    padding: 12px 10px 11px 4px;
    border-radius: 35px;
    width: 29%;
    font-size: 13px;
    color: #fff;">color</p></div>
					  <?php } ?>
	</td>
					  <td> {{ $datas->emp_id  }} </td>
					  <td>
						<a  href="{{ route('users.edit', $datas->id) }}" title="Edit"><i class="fa fa-pencil"></i></a>
						 <?php if(Auth::user()->id == 72 || Auth::user()->id == 50 || Auth::user()->id == 282 ){ ?>
						<?php }else{ ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/changepassword') }}/{{ $datas->id  }}" title="Change Password"><i class="fa fa-unlock"></i></a>
						<?php } ?>

						<a style="margin-left: 5px;cursor:pointer" data-baseURL="{{ url('/') }}" data-ID="{{ $datas->id  }}" class="delete_user" title="Delete"><i class="fa fa-trash-o"></i></a>
						<?php if($datas->role == "user"){ ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/timesheets') }}/{{ $datas->id  }}" title="Time Sheets"><i class="fa fa-book"></i></a>
						<?php } ?>
						<?php if($datas->role == "supervisor"){ ?>
						<a  style="margin-left: 5px;"  href="{{ url('/user/suser') }}/{{ $datas->id  }}/{{ $frm_date }}/{{ $t_date }}" title="Supervisor User"><i class="fa fa-book"></i></a>
						<?php } ?>
					  </td>
					  <td style="display:none">
						<img src="../../../assets/images/faces-clipart/pic-1.png" alt="image"> </td>
					  <td> {{ $datas->email  }} </td>
					  <td> {{ $datas->last_name  }} {{ $datas->first_name  }} </td>
					   <?php if(Auth::user()->id == 72 || Auth::user()->id == 50 ){ ?>
						<?php }else{ ?>
					  <td> {{ $datas->pass  }} </td>
					  	<?php } ?>
					  <td> {{ $datas->dept  }} </td>
					  <td > <?php $user_companies = SupervisorInfoController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; ?></td>
					    <td> @if($datas->last_login_at != null) {{ date('h:i a', strtotime($datas->last_login_at)) }} @endif</td>
					   <td> @if($datas->last_login_at != null) {{ date('M d, Y', strtotime($datas->last_login_at)) }} @endif</td>
					  <td> {{ date('M d, Y', strtotime($datas->created_at)) }} </td>
					  <td> <?php if($datas->status == 1){ echo "<h5 style='color:green'>Active</h5>"; }else{ echo "<h5 style='color:red'>Inactive</h5>"; } ?></td>

					</tr>
				<?php $count++; ?>
				@endforeach
				
				@else
					<p>Sorry No Data!!</p>
				@endif
			  </tbody>
			</table>
			<div class="pagination">
    
</div>
		  </div>
		</div>
	  </div>
	</div>
	<div class="loding"></div>
	</div>
@endsection		
