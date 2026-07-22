<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Excel;
use App\TimeSheet;
use App\User;
use App\Company;
use App\House;
use App\UserManager;
use Carbon\Carbon;
use App\UserSupervisorRel;
use App\UserCasemanagerRel;

class TimesheetssaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $user)->orderBy('hours_day', 'DESC')->paginate(15);
		return view('admin.satimesheet.ts_view',compact('data'));
    }
	
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
		$user = $id;
		$user_com = User::where('id', '=', $user)->first();
		$Company = $user_com->companies_id;
		$companies = Company::orderBy('created_at', 'DESC')->get();
		if($Company == "Quantumleap, Inc"){
			$houses = House::where('companies_id', '=', $Company)->orderBy('created_at', 'DESC')->get();
		}else{
			$Com = array();
			$Com[] = "Quantumleap, Inc";
			$houses = House::whereNotIn('companies_id', $Com)->orderBy('created_at', 'DESC')->get();
		}
        return view('admin.satimesheet.ts_add', compact('companies','user','houses'));
    }
	
	 /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$rules = [
			'company_id'     =>  'required',
			'house_id'     =>  'required',
			'hours_day'    =>  'required',
			'time_in' 	   =>  'required',
			'time_out' 	   =>  'required',
		];
		$customMessages = [
			'company_id'     =>  'Please select company',
			'house_id'     =>  'Please select house',
			'hours_day'    =>  'Please add date',
			'time_in' 	   =>  'required',
			'time_out' 	   =>  'required',
		];
		$this->validate($request, $rules, $customMessages);
				
		if(!empty($request->vacc)){
			$vacc = $request->vacc;
		}else{
			$vacc = 0;
		}
		$date    = explode('-', $request->hours_day);
		$date = implode("_", $date);
		substr($request->time_in, 0, -2);
		substr($request->time_out, 0, -2);
		$starttimestamp = strtotime($request->time_in);
		$endtimestamp = strtotime($request->time_out);
		if (strpos($request->time_in, 'pm') !== false && strpos($request->time_out, 'am') !== false) {
				$hours = (abs(($endtimestamp - $starttimestamp)/3600));
				if($hours < 0){
					$hours = abs(($starttimestamp - $endtimestamp)/3600);
					$hours = 24-$hours;
				}else{
					$hours = 24-$hours;
				}
		}elseif($starttimestamp == $endtimestamp){
			$hours = 24;
		}else{
			$hours = (abs(($endtimestamp - $starttimestamp)/3600));
		}
		$form_data = array(
				'companies_id' => $request->company_id,
				'houses_id' => $request->house_id,
				'users_id' => $request->user_id,
				'hours_day' => $date,
				'time_in'  => $request->time_in,
				'time_out'  => $request->time_out,
				'hours_wrk' => $hours,
				'hours_price' => $request->hour_rate,
				'vacation_status' => $vacc,
				'approve' => $request->approved,
		);
		
		
		$ts_store = TimeSheet::create($form_data);
			
		if($ts_store){
			return redirect('/muser/timesheets/'.$request->user_id)->with(['success' => 'Hours Added Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while Adding Hours!!']);
		}
		
    }
	
	
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

		$companies = Company::orderBy('created_at', 'DESC')->get();
		$houses = House::orderBy('created_at', 'DESC')->get();
		$data = TimeSheet::where('id', '=', $id)->get();
		return view('admin.satimesheet.ts_edit',compact('data','companies','houses'));
		
    }
	
	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

		$rules = [
			'company_id'     =>  'required',
			'house_id'     =>  'required',
			'hours_day'    =>  'required',
			'time_in' 	   =>  'required',
			'time_out' 	   =>  'required',
		];
		$customMessages = [
			'company_id'     =>  'Please select company',
			'house_id'     =>  'Please select house',
			'hours_day'    =>  'Please add date',
			'time_in' 	   =>  'required',
			'time_out' 	   =>  'required',
		];
		$this->validate($request, $rules, $customMessages);
		$this->validate($request, $rules, $customMessages);
		if(!empty($request->vacc)){
			$vacc = $request->vacc;
		}else{
			$vacc = 0;
		}
		$date    = explode('-', $request->hours_day);
		$date = implode("_", $date);
		substr($request->time_in, 0, -2);
		substr($request->time_out, 0, -2);
		$starttimestamp = strtotime($request->time_in);
		$endtimestamp = strtotime($request->time_out);
		if (strpos($request->time_in, 'pm') !== false && strpos($request->time_out, 'am') !== false) {
				$hours = (abs(($endtimestamp - $starttimestamp)/3600));
				if($hours < 0){
					$hours = abs(($starttimestamp - $endtimestamp)/3600);
					$hours = 24-$hours;
				}else{
					$hours = 24-$hours;
				}
		}elseif($starttimestamp == $endtimestamp){
			$hours = 24;
		}else{
			$hours = (abs(($endtimestamp - $starttimestamp)/3600));
		}
		$form_data = array(
				'companies_id' => $request->company_id,
				'houses_id' => $request->house_id,
				'users_id' => $request->user_id,
				'hours_day' => $date,
				'time_in'  => $request->time_in,
				'time_out'  => $request->time_out,
				'hours_wrk' => $hours,
				'hours_price' => $request->hour_rate,
				'vacation_status' => $vacc,
				'approve' => $request->approved,
		);
		$ts_update = TimeSheet::whereId($request->hidden_id)->update($form_data);

		if($ts_update){
			return redirect('/muser/timesheets/'.$request->user_id)->with(['success' => 'Hours Updated Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while updating Hours!!']);
		}
    }
	

	 /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = TimeSheet::findOrFail($id);
        $data->delete();
    }
	
	public function srch_time(Request $request)
    {
		// print_r($request->all());
		// die;
		$user = $request->user_id;
		$from_date    = explode('-', $request->frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $request->todate);
		$to_date = implode("_", $to_date);
		
	
if($from_date == $to_date){
			$data = TimeSheet::with('companies')
								->with('users')
								->with('houses')
								->where('hours_day', $from_date)
								->where('users_id', '=', $user)
								->orderBy('created_at', 'DESC')
								->get();
		}else{
			$data = TimeSheet::with('companies')
								->with('users')
								->with('houses')
								->where('users_id', '=', $user)
								->whereBetween('hours_day', array($from_date, $to_date))
								->orderBy('created_at', 'DESC')
								->get();
		}
		 $count = 1;
		 $total_hours = 0;
		$approved_hours = 0;
		$denied_hours = 0;
		$cm_check = $this->checkCM($user);
			  if($data->count() != 0){
				foreach ($data as $datas){
					$total_hours = $total_hours + $datas->hours_wrk;
					$color_info =  $this->color_info($datas->id); 
					
					
					if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					}
					 echo '<td>'.$count.'</td>';
					  echo '<td>'.$datas->users->emp_id.'</td>';
					   if(!empty($cm_check)){ 
					   if($datas->cmcheck_status == 2){ 
					     $caseManager = $this->caseManager($datas->cm_id);
							 
							echo  '<td>';
								if(!empty($caseManager)){
									echo '<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" >'.$caseManager.'</label><br>';
									echo '<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" >'.date('M d, Y h:i a', strtotime($datas->cm_update_at )).'</label><br>';
									echo '<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" >'.date('(D)', strtotime($datas->cm_update_at )).'</label>';
									
								}
							echo '</td>';
							 
						}else{
							echo '<td ></td>';
					  
						}
					 }
					    echo  '<td>';
						echo '<a  href="'.url('/').'/user/edit/timesheets/'.$datas->id.'" title="Edit"><i class="fa fa-pencil"></i></a>';
						echo '<a style="margin-left: 5px;" data-baseURL="'.url('/').'" data-ID="'.$datas->id.'" class="delete_ts" title="Delete"><i class="fa fa-trash-o"></i></a>';
					  echo '</td>';
					   echo '<td><input type="checkbox" class="time_id"  value="'.$datas->id.'" name="time_id[]" ></td>';
					  echo '<td><input type="checkbox" class="time_idd"  value="'.$datas->id.'" name="time_idd[]" ></td>';
					  echo '<td><input type="checkbox" class="time_idde"  value="'.$datas->id.'" name="time_idde[]" ></td>';
					  echo '<td>'.$datas->time_in.'</td>';
					  echo '<td>'.$datas->time_out.'</td>';
					  $hours    = explode('_', $datas->hours_wrk);
					  $hours = implode(":", $hours);
					  echo '<td>'. $hours.'</td>';
					  $hours_day    = explode('_', $datas->hours_day);$hours_day = implode("/", $hours_day); $hours_day = date("M d, Y(D)", strtotime($hours_day));
					  echo '<td>'. $hours_day.'</td>';
					   echo '<td>'.str_limit($datas->users->name , 10).'</td>';
					    echo '<td>'.str_limit($datas->users->dept, 10).'</td>';
					  echo '<td>'.str_limit($datas->companies->company, 10) .'</td>';
					  echo '<td>'.str_limit($datas->houses->house_add, 20).'</td>';
					  
					  echo '<td>'. $datas->users->hourst_rate.'</td>';
					  echo '<td>';
					  if($datas->vacation_status == "0"){ echo "<h5 style='color:green'>No</h5>"; }elseif($datas->vacation_status == "1"){ echo "<h5 style='color:red'>Yes</h5>"; }
					  echo '</td>';
					 echo  '<td>';
					 if($datas->approve == "2"){ $approved_hours = $approved_hours + $datas->hours_wrk; echo "<h5 style='color:green'>Yes</h5>"; }
					 elseif($datas->approve == "1"){ $denied_hours = $denied_hours + $datas->hours_wrk;  echo "<h5 style='color:red'>No</h5>"; }
					 else{ echo "<h5 style='color:#a5a548'>Pending</h5>"; } 
					  echo '</td>';
					
				$count++;
				}
				echo '</tr>';
									echo '<tr>';
					 echo '<td></td>';
					    echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					   echo '<td>Payperiod</td>';
					    echo '<td>'.date("d", strtotime($from_date)).'-'.date("d M", strtotime($to_date)).'</td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					 echo  '<td></td>';
					   echo  '<td></td>';
					    echo  '<td></td>';
					   echo  '<td></td>';
					echo '</tr>';
					echo '<tr>';
					 echo '<td></td>';
					    echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td>Total Hours Worked</td>';
					    echo '<td>'.$total_hours.'</td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					 echo  '<td></td>';
					   echo  '<td></td>';
					    echo  '<td></td>';
					   echo  '<td></td>';
					echo '</tr>';
					echo '<tr>';
					 echo '<td></td>';
					    echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					   echo '<td>Approved Hours</td>';
					    echo '<td>'.$approved_hours.'</td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					 echo  '<td></td>';
					   echo  '<td></td>';
					    echo  '<td></td>';
					   echo  '<td></td>';
					echo '</tr>';
					echo '<tr>';
					 echo '<td></td>';
					    echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td>Declined Hours</td>';
					    echo '<td>'.$denied_hours.'</td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					  echo '<td></td>';
					 echo  '<td></td>';
					   echo  '<td></td>';
					    echo  '<td></td>';
					   echo  '<td></td>';
					echo '</tr>';
			  }else{
					echo "<p>Sorry No Data!!</p>";
			  }
    }
	
	
		/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export_data($id)
    {
		$user = $id;
        $data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $user)->orderBy('hours_day', 'DESC')->get();
		$time_sheet[] = array('#','Emp ID','Email', 'Last Name','First Name','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved');
		$count = 1;
		foreach($data as $datas)
		{
			 $hours_day    = explode('_', $datas->hours_day);
			 $hours_day = implode("/", $hours_day); 
			 $hours_day = date("M d, Y(D)", strtotime($hours_day)); 
			 if($datas->vacation_status == "0"){ 
				$vacation_status = "No"; 
			 }elseif($datas->vacation_status == "1"){
				 $vacation_status = "Yes";
				}else{
					$vacation_status = "";
				}
					 
			if($datas->approve == "2"){ 
					$approve = "Yes";
			}elseif($datas->approve == "1"){
				$approve = "No";
			}else{
				$approve = "Pending"; 
			}
			$time_sheet[] = array(
				'#' => $count,
				'Emp ID' => $datas->users->emp_id,
				'Email'  => $datas->users->email,
				'Last Name'   => $datas->users->last_name,
				'First Name'   => $datas->users->first_name,
				'Name'  => $datas->users->name,
				'Department'   => $datas->users->dept,
				'Company'  => $datas->companies->company,
				'House'  => $datas->houses->house_add,
				'Time In'   => $datas->time_in,
				'Time Out'   => $datas->time_out,
				'Hours Worked'   => $datas->hours_wrk,
				'Day'    => $hours_day,
				'Hours Rate'  => $datas->users->hourst_rate,
				'Vacation'   => $vacation_status,
				'Approved'    => $approve
			);
			$count++;
		}
		Excel::create('Time Sheet', function($excel) use ($time_sheet){
			$excel->setTitle('Time Sheet');
			$excel->sheet('Time Sheet', function($sheet) use ($time_sheet){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
			});
		})->download('xlsx');
    }
	
	
		/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function srcexport_data($frmdate,$todate, $id)
    {
		$user = $id;
        $from_date    = explode('-', $frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $todate);
		$to_date = implode("_", $to_date);
		
	
		if($from_date == $to_date){
			$data = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('hours_day', $from_date)
								->where('users_id', '=', $user)
								->orderBy('hours_day', 'DESC')
								->get();
		}else{
			$data = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('users_id', '=', $user)
								->whereBetween('hours_day', array($from_date, $to_date))
								->orderBy('hours_day', 'DESC')
								->get();
		}
		
		$time_sheet[] = array('#','Emp ID','Email', 'Last Name','First Name','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved');
		$count = 1;
		foreach($data as $datas)
		{
			 $hours_day    = explode('_', $datas->hours_day);
			 $hours_day = implode("/", $hours_day); 
			 $hours_day = date("M d, Y(D)", strtotime($hours_day)); 
			 if($datas->vacation_status == "0"){ 
				$vacation_status = "No"; 
			 }elseif($datas->vacation_status == "1"){
				 $vacation_status = "Yes";
				}else{
					$vacation_status = "";
				}
				
			if($datas->approve == "2"){ 
					$approve = "Yes";
			}elseif($datas->approve == "1"){
				$approve = "No";
			}else{
				$approve = "Pending"; 
			}
			$time_sheet[] = array(
				'#' => $count,
				'Emp ID' => $datas->users->emp_id,
				'Email'  => $datas->users->email,
				'Last Name'   => $datas->users->last_name,
				'First Name'   => $datas->users->first_name,
				'Name'  => $datas->users->name,
				'Department'   => $datas->users->dept,
				'Company'  => $datas->companies->company,
				'House'  => $datas->houses->house_add,
				'Time In'   => $datas->time_in,
				'Time Out'   => $datas->time_out,
				'Hours Worked'   => $datas->hours_wrk,
				'Day'    => $hours_day,
				'Hours Rate'  => $datas->users->hourst_rate,
				'Vacation'   => $vacation_status,
				'Approved'    => $approve
			);
			$count++;
		}
		Excel::create('Time Sheet', function($excel) use ($time_sheet){
			$excel->setTitle('Time Sheet');
			$excel->sheet('Time Sheet', function($sheet) use ($time_sheet){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
			});
		})->download('xlsx');
    }
	public function search_payperiod(Request $request)
    {
		$from_date    = explode('-', $request->frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $request->todate);
		$to_date = implode("_", $to_date);
		$sasearch_by_comp = $request->sasearch_by_comp;
		$user = $request->user;
		
		$time_sheet[] = array('#', 'Last Name','First Name','Name', 'Department','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Vacation', 'Approved');
		
		
		$usersm = User::where('id', '=', $user)->first();
		$user_f_name = $usersm->first_name;
		
		$users_arrr = array();
		$company_id = array();
		if(isset($sasearch_by_comp) && $sasearch_by_comp != 0){
			$user_companies = UserManager::where('musers_id', '=', $user)->where('users_id', '=', $sasearch_by_comp)->get();
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$company_id[] = $user_company->users_id;
				}
			}
			$users_id = UserManager::whereIn('users_id', $company_id)->get();
			if(isset($users_id)){
				foreach($users_id as $users_ids){
					$users_arrr[] = $users_ids->musers_id;
				}
			}
		}else{
			$companies = UserManager::where('musers_id', '=', $user)->get();
			if(isset($companies)){
				foreach($companies as $company){
					$company_id[] = $company->users_id;
				}
			}
			$users_id = UserManager::whereIn('users_id', $company_id)->get();
			if(isset($users_id)){
				foreach($users_id as $users_ids){
					$users_arrr[] = $users_ids->musers_id;
				}
			}
		}
		
		$users = User::with('companies')->whereIn('id', $users_arrr)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
		$user_arr = array();
		$user_count = 1;
		if(isset($users)){
			foreach($users as $userss){
				$user_arr[] = $userss->id;
			}
		}
		if($from_date == $to_date){
			$data = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('hours_day', $from_date)
								->whereIn('users_id', $user_arr)
								->distinct()->get(['users_id']);
		}else{
			$data = TimeSheet::with('companies')
								->whereBetween('hours_day', array($from_date, $to_date))
								->whereIn('users_id', $user_arr)
								->distinct()->get(['users_id']);
		}
		
		$user_time = array();
		if(isset($data)){
			foreach($data as $datas){
				$user_time[] = $datas->users_id;
			}
		}
		$users_data = User::with('companies')->whereIn('id', $user_time)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
		$ucount = 1;
		
		if(isset($users_data)){
			foreach($users_data as $user_data){
				$color_info =  $this->color_info($user_data->id); 
					
					
					if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					}
				  echo "<td>".$ucount."</td>";
				  echo "<td>".$user_data->emp_id."</td>";
				   echo "<td>";
					 echo '<a style="margin-left: 5px;"  href="'.url('/user/suser/timesheets').'/'.$user.'/'.$user_data->id.'/'.$from_date.'/'.$to_date.'" title="Time Sheets"><i class="fa fa-book"></i></a>';
				  echo "</td>";
				   echo "<td>".$user_data->last_name." ".$user_data->first_name."</td>";
				 echo "<td>".$user_data->dept."</td>";
				  echo "<td>".$user_data->companies_id."</td>";
				  echo "<td>";
				  if($user_data->last_login_at != null){ echo date('h:i a', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				   echo "<td>";
				  if($user_data->last_login_at != null){ echo date('M d, Y', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				  echo "<td>".date('M d, Y(D)', strtotime($user_data->created_at))."</td>";
				  				   echo "<td>";
				   if($user_data->status == 1){ echo "<h5 style='color:green'>Active</h5>"; }else{ echo "<h5 style='color:red'>Inactive</h5>"; } 
				   echo "</td>";
				 				    				  echo "<td>";
					$total_time = $this->ttotal_time($user_data->id, $from_date, $to_date);
						if($total_time <=  79){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_time."</p>";
						}elseif($total_time == 80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_time."</p>";
						}
				  echo "</td>";
				   echo "<td>";
					$approved_time = $this->tapproved_time($user_data->id, $from_date, $to_date);
						if($approved_time <=  79){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_time."</p>";
						}elseif($approved_time == 80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_time."</p>";
						}
				  echo "</td>";
				   echo "<td>";
					$denied_time = $this->tdenied_time($user_data->id, $from_date, $to_date);
						if($denied_time <=  79){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_time."</p>";
						}elseif($denied_time == 80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_time."</p>";
						}
				  echo "</td>";
				 echo "</tr>";
				$ucount++;
			}
		}
		
	}
		public static function ttotal_time($id, $from_date, $to_date)
    {
        // $total_time = TimeSheet::where('users_id', '=', $id)->sum('hours_wrk');
		if($from_date == $to_date){
			$total_time = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('hours_day', $from_date)
								->where('users_id', '=', $id)
								->sum('hours_wrk');
		}else{
			$total_time = TimeSheet::with('companies')
								->whereBetween('hours_day', array($from_date, $to_date))
								->where('users_id', '=', $id)
								->sum('hours_wrk');
		}
		return $total_time;
    }
	
	public static function tapproved_time($id, $from_date, $to_date)
    {
		if($from_date == $to_date){
			$approved_time = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('hours_day', $from_date)
								->where('users_id', '=', $id)
								->where('approve', '=', 2)
								->sum('hours_wrk');
		}else{
			$approved_time = TimeSheet::with('companies')
								->whereBetween('hours_day', array($from_date, $to_date))
								->where('users_id', '=', $id)
								->where('approve', '=', 2)
								->sum('hours_wrk');
		}
		return $approved_time;
    }
	
	public static function tdenied_time($id, $from_date, $to_date)
    {
		if($from_date == $to_date){
			$denied_time = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('hours_day', $from_date)
								->where('users_id', '=', $id)
								->where('approve', '=', 1)->sum('hours_wrk');
		}else{
			$denied_time = TimeSheet::with('companies')
								->whereBetween('hours_day', array($from_date, $to_date))
								->where('users_id', '=', $id)
								->where('approve', '=', 1)->sum('hours_wrk');
		}
		return $denied_time;
    }
	public static function total_time($id)
    {
        $total_time = TimeSheet::where('users_id', '=', $id)->sum('hours_wrk');
		return $total_time;
    }
	
	public static function approved_time($id)
    {
        $approved_time = TimeSheet::where('users_id', '=', $id)->where('approve', '=', 2)->sum('hours_wrk');
		return $approved_time;
    }
	
	public static function denied_time($id)
    {
        $denied_time = TimeSheet::where('users_id', '=', $id)->where('approve', '=', 1)->sum('hours_wrk');
		return $denied_time;
    }
	

	public function allexport_data($frmdate,$todate,$user,$sasearch_by_comp)
    {
        $from_date    = explode('-', $frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $todate);
		$to_date = implode("_", $to_date);
		$user = $user;
		// $time_sheet[] = array('#','Emp ID', 'Last Name','First Name','Name', 'Department','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Vacation', 'Approved', 'Approved By');
			
		$user_idss = array();
		$company_id = array();
		if(isset($sasearch_by_comp) && $sasearch_by_comp != 0){
			$companies = UserManager::where('users_id', '=', $sasearch_by_comp)->where('musers_id', '=', $user)->get();
			
			if(isset($companies)){
				foreach($companies as $company){
					$company_id[] = $company->users_id;
				}
			}
			$users_id = UserManager::whereIn('users_id', $company_id)->get();

			if(isset($users_id)){
				foreach($users_id as $users_ids){
					$user_idss[] = $users_ids->musers_id;
				}
			}
		}else{
			$companies = UserManager::where('musers_id', '=', $user)->get();

			if(isset($companies)){
				foreach($companies as $company){
					$company_id[] = $company->users_id;
				}
			}
			$users_id = UserManager::whereIn('users_id', $company_id)->get();
			
			if(isset($users_id)){
				foreach($users_id as $users_ids){
					$user_idss[] = $users_ids->musers_id;
				}
			}
		}
		
		$users = User::with('companies')->whereIn('id', $user_idss)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
		
		
		
		$user_arr = array();
		$user_count = 1;
		if(isset($users)){

			foreach($users as $userss){
				$user_arr[] = $userss->id;
				
				if($from_date == $to_date){
					$data = TimeSheet::with('companies')
										->with('houses')
										->with('users')
										->where('hours_day', $from_date)
										->where('users_id', $userss->id)
										->orderBy('hours_day', 'DESC')
										->get();
				}else{
					$data = TimeSheet::with('companies')
										->with('houses')
										->with('users')
										->whereBetween('hours_day', array($from_date, $to_date))
										->where('users_id', $userss->id)
										->orderBy('hours_day', 'DESC')
										->get();
				}
				
			$count = 1;
			$total_hours = 0;
			$approved_hours = 0;
			$denied_hours = 0;
			  if($data->count() != 0){
				foreach ($data as $datas){
					if($datas->approved_by != 0){
						$user_id = User::where('id', '=', $datas->approved_by )->first();
						$approved_by = $user_id->name;
					}else{
						$approved_by = "";
					}
					
					 $hours_day    = explode('_', $datas->hours_day);
					 $hours_day = implode("/", $hours_day); 
					 $hours_day = date("M d, Y(D)", strtotime($hours_day));		
					$total_hours					  = $total_hours + $datas->hours_wrk;
					 if($datas->vacation_status == "0"){ 
						$vacation_status = "No"; 
					 }elseif($datas->vacation_status == "1"){
						 $vacation_status = "Yes";
						}else{
					$vacation_status = "";
				}
						$approve = "";
					if($datas->approve == "2"){ 
							$approve = "Yes";
							$approved_hours					  = $approved_hours + $datas->hours_wrk;
					}elseif($datas->approve == "1"){
						$denied_hours					  = $denied_hours + $datas->hours_wrk;
						$approve = "No";
					}else{
						$approve = "Pending"; 
					}
					$color_info = $this->color_info($datas->users_id); 
					$time_sheet[] = array(

						'#' => $count,
						'Emp ID' =>  $datas->users->emp_id,
						'Last Name'   => $datas->users->last_name,
						'First Name'   => $datas->users->first_name,
						'Name'  => $datas->users->name,
						'Department'   => $datas->users->dept,
						'House'  => $datas->houses->house_add,
						'Time In'   => $datas->time_in,
						'Time Out'   => $datas->time_out,
						'Hours Worked'  => $datas->hours_wrk,
						'Day'    => $hours_day,
						'Vacation'   => $vacation_status,
						'Approved'    => $approve,
						'Approved By' => $approved_by,
						'approver_color' => $color_info,
						
					);
					$count++;
				}
			  }
			  if($total_hours != 0){
				  $time_sheet[] = array(

						'#' => "",
						'Emp ID' =>  "",
						'Last Name'   => "",
						'First Name'   => "",
						'Name'  => "",
						'Department'   => "",
						'House'  => "",
						'Time In'   => "",
						'Time Out'   => "Total Hours",
						'Hours Worked'  => $total_hours,
						'Day'    => "",
						'Vacation'   => "",
						'Approved'    => "",
						'Approved By' => "",
						'approver_color' => "",
						
					);
				  $time_sheet[] = array(

						'#' => "",
						'Emp ID' =>  "",
						'Last Name'   => "",
						'First Name'   => "",
						'Name'  => "",
						'Department'   => "",
						'House'  => "",
						'Time In'   => "",
						'Time Out'   => "Approval",
						'Hours Worked'  => "Approved",
						'Day'    => "Denied",
						'Vacation'   => "",
						'Approved'    => "",
						'Approved By' => "",
						'approver_color' => "",
						
					);
					$time_sheet[] = array(

						'#' => "",
						'Emp ID' =>  "",
						'Last Name'   => "",
						'First Name'   => "",
						'Name'  => "",
						'Department'   => "",
						'House'  => "",
						'Time In'   => "",
						'Time Out'   => "Hours",
						'Hours Worked'  => $approved_hours,
						'Day'    => $denied_hours,
						'Vacation'   => "",
						'Approved'    => "",
						'Approved By' => "",
						'approver_color' => "",
						
					);
					$time_sheet[] = array(

						'#' => "",
						'Emp ID' =>  "",
						'Last Name'   => "",
						'First Name'   => "",
						'Name'  => "",
						'Department'   => "",
						'House'  => "",
						'Time In'   => "",
						'Time Out'   => "Payperiod",
						'Hours Worked'  => date("d", strtotime($frmdate)).'-'.date("d M", strtotime($todate)),
						'Day'    => "",
						'Vacation'   => "",
						'Approved'    => "",
						'Approved By' => "",
						'approver_color' => "",
						
					);
					$time_sheet[] = array(

						'#' => "",
						'Emp ID' =>  "",
						'Last Name'   =>"",
						'First Name'   => "",
						'Name'  => "",
						'Department'   => "",
						'House'  => "",
						'Time In'   => "",
						'Time Out'   => "",
						'Hours Worked'  => "",
						'Day'    => "",
						'Vacation'   => "",
						'Approved'    => "",
						'Approved By' => "",
						'approver_color' => "",
						
					);	 
			  }
			  		
			}
		}
		
		// echo "<pre>";
		// print_r($time_sheet);
		// die;
		Excel::create('Time Sheet', function($excel) use ($time_sheet){
			$excel->setTitle('Time Sheet');
			$excel->sheet('Time Sheet', function($sheet) use ($time_sheet){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
				$sheet->row(1, array('#','Emp ID', 'Last Name','First Name','Name', 'Department','House', 'Time In','Time Out', 'Hours Worked', 'Day', 'Vacation', 'Approved', 'Approved By'));
				$sheet->cells('A1:L1', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#BDB76B');
								$cells->setAlignment('center');
							});
				$i = 2;

				foreach ($time_sheet as $cleans) {

					$sheet->row($i, array($cleans['#'], $cleans['Emp ID'], $cleans['Last Name'],$cleans['First Name'], $cleans['Name'], $cleans['Department'], $cleans['House'],$cleans['Time In'], $cleans['Time Out'], $cleans['Hours Worked'], $cleans['Day'], $cleans['Vacation'], $cleans['Approved'], $cleans['Approved By']));
				
					if($cleans['approver_color'] != ""){
						$sheet->cell('O'.$i, function($cell) {
							$cell->setValue('');
						});
						// $sheet->cell('N'.$i, function($cell) {
							// $cell->setValue('');
						// });
						$bgcolor = $cleans['approver_color'];
						$sheet->cells('A'.$i.':L'.$i, function ($cells) use ($bgcolor) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground($bgcolor);
							$cells->setAlignment('center');
						});
					}else{
						$sheet->cell('O'.$i, function($cell) {
							$cell->setValue('');
						});
						// $sheet->cell('N'.$i, function($cell) {
							// $cell->setValue('');
						// });
						$sheet->cells('A'.$i.':L'.$i, function ($cells) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground('#ffffff');
							$cells->setAlignment('center');
						});
					}
					$i++;
				}
			});
		})->download('xlsx');
    }
	
	
	
	
	public function approve_time(Request $request)
    {
		$data = json_decode($request->ids_value, true);
		// print_r($data);
		$user = Auth::user()->id;
		if(isset($data)){
			foreach($data as $datas){
				$form_data = array(
					'approve' => 2,
					'approved_by' => $user,
					'approved_at' => Carbon::now()->toDateTimeString(),
				);
				$ts_update = TimeSheet::whereId($datas)->update($form_data);
			}
		}
		echo "Approved Successfully";
	}
	
	public function decline_time(Request $request)
    {
		$data = json_decode($request->ids_value, true);
		$user = Auth::user()->id;
		if(isset($data)){
			foreach($data as $datas){
				$form_data = array(
					'approve' => 1,
					'approved_by' => $user,
					'approved_at' => Carbon::now()->toDateTimeString(),
				);
				$ts_update = TimeSheet::whereId($datas)->update($form_data);
			}
		}
		echo "Declined Successfully";
	}
	
	public function delete_time(Request $request)
    {
		$data = json_decode($request->ids_value, true);
		if(isset($data)){
			foreach($data as $datas){
				$data = TimeSheet::findOrFail($datas);
				$data->delete();;
			}
		}
		echo "Deleted Successfully";
	}
	
	public static function export_user_companies($id)
    {
		$user_companies = UserManager::where('musers_id', '=', $id)->get();
		$com_out = "";
		if(isset($user_companies)){
			foreach($user_companies as $user_company){
				$company = Company::where('id', '=', $user_company->users_id)->first();
				
				$com_out .= $company->company;
			}
		}
		
		return $com_out;
	}
	
	public static function checkCM($id)
    {
		$Casemanager = UserCasemanagerRel::where('users_id', "=", $id)->first();
		
		$name = "";
		if(isset($Casemanager)){
			$cmUser = User::where('id', "=", $Casemanager->casemanager_id)->first();
			$name = $cmUser->name;
		}
		
		return $name;
		
	}
	
	public static function caseManager($id)
    {
		$user = User::where('id', "=", $id)->first();
		$name = "";
		if(isset($user)){
			$name = $user->name;
		}
		return $name;
		
	}
	
	public static function color_info($id)
    {
		$UserSupervisorRel = UserSupervisorRel::where('users_id', $id)->first();
		if(isset($UserSupervisorRel)){
			$color_info = User::where("id", $UserSupervisorRel->supervisor_id)->first();
			if(isset($color_info)){
					$bgcolor = $color_info->color_field;
				}else{
					$bgcolor = "";
				}
		}else{
			$bgcolor = "";
		}
		
		return $bgcolor;
	}
}
