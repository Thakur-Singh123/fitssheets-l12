@extends('layouts.master')
@section('content') 
<?php
use App\Http\Controllers\Admin\UserInfoController;
?>
<div class="content-wrapper">
	@if(\Session::has('success'))
		<div class="alert alert-success">
			<h4>{{\Session::get('success')}}</h4>
		</div>
	@endif
	<?php/* echo'hiii'; die();*/?>

	<div class="row">
	  <div class="col-lg-12 grid-margin stretch-card">
		<div class="card">
		  <div class="card-body">
			
			<table class="table table-striped table-responsive">
			  <thead>
				<tr>
				  <th> # </th>
				  <th> SSN </th>
				  <th> Action </th>
				  <th> Approve </th>
				  <th> Status </th>
				  <th> Driver License </th>
				  <th> Covid Report </th>
				  <th> Email </th>
				  <th> Name </th>
				  <th> Password </th>
				  <th> Department </th>
				  <th> Company </th>
				  <th> Hours Rate</th>
				  <th> Created_at </th>
				  <th>  Updated_at </th>
				</tr>
			  </thead>
			  <tbody id="srch_result">
			  <?php
               if(isset($data)){
			  $count = 1; 
			  $approved_by = 0;
			  if($data->count() != 0){
				foreach ($data as $datas){
			//dd($datas); die();
					
					  echo "<td>".$count."</td>";
					  echo "<td>".$datas->emp_id."</td>";
					  echo "<td>";
						echo '<a  href="'.url('/').'/users/'.$datas->id.'/edit" title="Edit"><i class="fa fa-pencil"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/').'/user/changepassword/'.$datas->id.'" title="Change Password"><i class="fa fa-unlock"></i></a>';
						echo '<a style="margin-left: 5px;cursor:pointer" data-baseURL="'.url('/').'" data-ID="'.$datas->id.'" class="delete_user" title="Delete"><i class="fa fa-trash-o"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/user/timesheets').'/'.$datas->id.'" title="Time Sheets"><i class="fa fa-book"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/user/driver/license').'/'.$datas->id.'" title="Driver License"><i class="fa fa-drivers-license"></i></a>';
					   
					   echo "</td>";
					   echo "<td>".$approved_by."</td>";
					 
					
					  echo "<td>";
					  if($datas->status == 1 ){ echo "<h5 style='color:green'>Active</h5>"; }
					  else{ echo "<h5 style='color:red'>Inactive</h5>"; }
					  echo "</td>";
					   echo "<td>"; 
					   
					   if($datas->drivers_license != null){ 
					echo '<img style=" margin-top: 15px; width: 152px;height: 156px;" src="'.url('/').'/assets/uploads/driving-license/'.$datas->drivers_license.'">';
					} else{ 
						echo '<p>No License Found!</p>';
						echo '<a  href="'.url('/').'/users/'.$datas->id.'/edit" title="Edit">Upload Here</a>';
					} 
					echo "</td>";
					 echo "<td>"; 
					   
					   if($datas->covid_report != null){ 
					echo '<img style=" margin-top: 15px; width: 152px;height: 156px;" src="'.url('/').'/assets/uploads/covid-report/'.$datas->covid_report.'">';
					} else{ 
						echo '<p>No Report Found!</p>';
				} 
					echo "</td>";
					  echo "<td>".$datas->email."</td>";
					  echo "<td>".$datas->name."</td>";
					 if(Auth::user()->id == 72 || Auth::user()->id == 50 || Auth::user()->id == 282 ){
						 }else{
					  	 echo "<td>".$datas->pass."</td>";
						 }
					 echo "<td>".$datas->dept."</td>";
					  echo "<td>";
				   $user_companies = UserInfoController::user_companies($datas->id); echo '<ul class="comp_list">'.$user_companies.'</ul>'; "</td>";
				
				 
				 echo "<td>";
				 
				
				  echo "<td>".$datas->hourst_rate."</td>";
				   echo "<td>".date('M d, Y', strtotime($datas->created_at))."</td>";
			   echo "<td>";
			  if($datas->last_login_at != null) { echo date('h:i a', strtotime($datas->last_login_at)) ; }
			  echo "</td>";
				  echo "<td>";
				  if($datas->last_login_at != null) { echo date('M d, Y', strtotime($datas->last_login_at));  }
				  echo "</td>";
					 
					  
			

					 echo "</tr>";
				 
				 $count++; 
				}
			  }
				
				else{
					echo "<p>Sorry No Data!!</p>";
				}
			   }				?>
			  </tbody>
			</table>

		  </div>
		</div>
	  </div>
	</div>
	<div class="loding"></div>
	</div>
@endsection	