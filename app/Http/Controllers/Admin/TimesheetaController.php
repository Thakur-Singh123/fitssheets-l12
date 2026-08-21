<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Excel;
use App\Models\TimeSheet;
use App\Models\User;
use App\Models\Company;
use App\Models\House;
use App\Models\UserCasemanagerRel;

class TimesheetaController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

	//Function for all timesheets
    public function index() {
		//Get timesheet
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $user)->orderBy('hours_day', 'DESC')->paginate(10);
		return view('admin.timesheet.ts_view',compact('data'));
    }
	
	/**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
	
	//Function for create timesheet
    public function create($id) {
		//Get user id
		$user = $id;
		$user_com = User::where('id', '=', $user)->first();
		//Get company
		$Company = $user_com->companies_id;
		$companies = Company::orderBy('display_order', 'ASC')->get();
		if($Company == "Quantumleap, Inc"){
			$houses = House::where('companies_id', '=', $Company)->orderBy('created_at', 'DESC')->get();
		}else{
			$Com = array();
			$Com[] = "Quantumleap, Inc";
			$houses = House::whereNotIn('companies_id', $Com)->orderBy('created_at', 'DESC')->get();
		}
        return view('admin.timesheet.ts_add', compact('companies','user', 'houses'));
    }
	
	/**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

	//Function for submit timesheet
    public function store(Request $request) {
		//Validate input fields
		$request->validate([
			'company_id' => 'required',
			'house_id' => 'required',
			'hours_day' => 'required',
			'time_in' => 'required',
			'time_out' => 'required',
		]);
		//$rules = [
		//'company_id' =>'required',
		//'house_id'  =>'required',
		//'hours_day' =>'required',
		//'time_in' =>'required',
		// 	'time_out' =>'required',
		// ];
		// $customMessages = [
		//'company_id' =>'Please select company',
		//'house_id' =>'Please select house',
		//'hours_day' =>'Please add date',
		//'time_in' =>'required',
		//'time_out' =>'required',
		//];
		//$this->validate($request, $rules, $customMessages);	
		if(!empty($request->vacc)) {
			$vacc = $request->vacc;
		} else {
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
			if($hours < 0) {
				$hours = abs(($starttimestamp - $endtimestamp)/3600);
				$hours = 24-$hours;
			} else {
				$hours = 24-$hours;
			}
		} elseif($starttimestamp == $endtimestamp) {
			$hours = 24;
		} else {
			$hours = (abs(($endtimestamp - $starttimestamp)/3600));
		}
		//form data
		$form_data = array(
			'companies_id' => $request->company_id,
			'houses_id' => $request->house_id,
			'users_id' => $request->user_id,
			'hours_day' => $date,
			'time_in'  => $request->time_in,
			'time_out'  => $request->time_out,
			'hours_wrk' => $hours,
			'hours_price' => $request->hour_rate,
			'remarks' => $request->remarks,
			'vacation_status' => $vacc,
			'approve' => $request->approved,
		);
		//Craete timesheet
		$ts_store = TimeSheet::create($form_data);
	    //Check if timesheet created or not
		if($ts_store) {
			return redirect('/user/timesheets/'.$request->user_id)->with(['success' => 'Hours added successfully.']);
		} else {
			return redirect()->back()->with(['success' => 'Error while adding hours!']);
		}
    }
	
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function show($id) {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function edit($id) {
		//Get companies
		$companies = Company::orderBy('display_order', 'ASC')->get();
		//Get house
		$houses = House::orderBy('created_at', 'DESC')->get();
		//Get timesheet
		$data = TimeSheet::where('id', '=', $id)->get();
		return view('admin.timesheet.ts_edit',compact('data','companies','houses'));
    }
	
	/**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

	//Function for update timesheet
    public function update(Request $request) {
		//Validate input fields
		$request->validate([
			'company_id' => 'required',
			'house_id' => 'required',
			'hours_day' => 'required',
			'time_in' => 'required',
			'time_out' => 'required',
		]);
		//$rules = [
		//'company_id' =>'required',
		//'house_id'  =>'required',
		//'hours_day' =>'required',
		//'time_in' =>'required',
		//'time_out' =>'required',
		//];
		//$customMessages = [
		//'company_id' =>'Please select company',
		//'house_id' =>'Please select house',
		//'hours_day' =>'Please add date',
		//'time_in' =>'required',
		//'time_out' =>'required',
		//];
		//$this->validate($request, $rules, $customMessages);
		//$this->validate($request, $rules, $customMessages);
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
			if($hours < 0) {
				$hours = abs(($starttimestamp - $endtimestamp)/3600);
				$hours = 24-$hours;
			} else {
				$hours = 24-$hours;
			}
		} elseif($starttimestamp == $endtimestamp) {
			$hours = 24;
		} else {
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
			'remarks' => $request->remarks,
			'vacation_status' => $vacc,
			'approve' => $request->approved,
		);
		//Update timesheet
		$ts_update = TimeSheet::whereId($request->hidden_id)->update($form_data);
        //Check if timesheet updated or not
		if($ts_update) {
			return redirect('/user/timesheets/'.$request->user_id)->with(['success' => 'Hours updated successfully.']);
		} else {
			return redirect()->back()->with(['success' => 'Error while updating Hours!!']);
		}
    }
	
	/**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    // public function destroy($id) {
    //     $data = TimeSheet::findOrFail($id);
    //     $data->delete();
    // }
	
	//Function for delete timesheet
    public function destroy(Request $request) {
		//Get ajax request for timesheet id
		$utimests_id = $request->utimests_id;
		//Delete timesheet
		$is_delete_timesheet = TimeSheet::where('id', $utimests_id)->delete();
		//Check if timesheet record deleted or not
		if($is_delete_timesheet){
			echo '<p style="color:green;">Timesheet record deleted successfully.</p>';
			// Corrected JavaScript code
			echo '<script>setTimeout(function(){ window.location.href = ""; }, 3000);</script>';
		} else {
			echo '<p style="color:green;">Oops something wrong.</p>';
		}
    }

	//Function for search time
	public function srch_time(Request $request) {
		//Get user
		$user = $request->user_id;
		$from_date    = explode('-', $request->frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $request->todate);
		$to_date = implode("_", $to_date);
		if($from_date == $to_date) {
			$data = TimeSheet::with('companies')
				->with('users')
				->with('houses')
				->where('hours_day', $from_date)
				->where('users_id', '=', $user)
				->orderBy('created_at', 'DESC')
				->get();
		} else {
			$data = TimeSheet::with('companies')
				->with('users')
				->with('houses')
				->where('users_id', '=', $user)
				->whereBetween('hours_day', array($from_date, $to_date))
				->orderBy('created_at', 'DESC')
				->get();
		}
		
		$count = 1;
		$cm_check = $this->checkCM($user);
		if($data->count() != 0) {
			foreach ($data as $datas) {
				echo '<tr>';
				echo '<td>'.$count.'.</td>';
				echo '<td>'.$datas->users->emp_id.'</td>';
				if(!empty($cm_check)){ 
					if($datas->cmcheck_status == 2) { 
						$caseManager = $this->caseManager($datas->cm_id);
						echo  '<td>';
						if(!empty($caseManager)) {
							echo '<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" >'.$caseManager.'</label><br>';
							echo '<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" >'.date('M d, Y h:i a', strtotime($datas->cm_update_at )).'</label><br>';
							echo '<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" >'.date('(D)', strtotime($datas->cm_update_at )).'</label>';
						}
						echo '</td>';
					} else {
						echo '<td ></td>';
					}
				}
				echo  '<td>';
				echo '<a  href="'.url('/').'/user/edit/timesheets/'.$datas->id.'" title="Edit"><i class="fa fa-pencil"></i></a>';
				echo '<a style="margin-left: 5px;" data-baseURL="'.url('/').'" data-ID="'.$datas->id.'" class="delete_ats" title="Delete"><i class="fa fa-trash-o"></i></a>';
				echo '</td>';
				echo '<td>'.$datas->users->name.'</td>';
				echo '<td>'.$datas->users->dept.'</td>';
				echo '<td>'.$datas->companies->company.'</td>';
				echo '<td>'.substr($datas->houses->house_add, 0, 4).'</td>';
				echo '<td>'.$datas->time_in.'</td>';
				echo '<td>'.$datas->time_out.'</td>';
				$hours    = explode('_', $datas->hours_wrk);
				$hours = implode(":", $hours);
				echo '<td>'. $hours.'</td>';
				$hours_day    = explode('_', $datas->hours_day);$hours_day = implode("/", $hours_day); $hours_day = date("M d, Y(D)", strtotime($hours_day));
				echo '<td>'. $hours_day.'</td>';
				echo '<td>'. $datas->users->hourst_rate.'</td>';
				echo '<td>';
				if($datas->vacation_status == "0") { echo "<h5 style='color:green'>No</h5>"; } elseif($datas->vacation_status == "1") { echo "<h5 style='color:red'>Yes</h5>"; }
				echo '</td>';
				echo  '<td>';
				if($datas->approve == "2") { echo "<h5 style='color:green'>Yes</h5>"; }
				elseif($datas->approve == "1") { echo "<h5 style='color:red'>No</h5>"; }
				else { echo "<h5 style='color:#a5a548'>Pending</h5>"; } 
				echo '</td>';
				echo "<td>";
				if(!empty($datas->approved_by)){ echo $this->userName($datas->approved_by); }else{ echo "--"; }
				echo "</td>";
				echo '<td>'. date('M d, Y h:i a', strtotime($datas->created_at )).'</td>';
				echo "<td>";
				if(!empty($datas->approved_at)){ echo date('M d, Y h:i a', strtotime($datas->approved_at )); }else{ echo "--"; }
				echo "</td>";
				echo '</tr>';
			$count++;
			}
		} else {
			echo '<tr>
			<td colspan="15" class="no-data">
				Sorry, No data found!
			</td>
			</tr>';
		}
    }
	
	//Function for search time
	public function srch_times(Request $request) {
		//Get auth detail
		$user = $request->user_id;
		
		$from_date    = explode('-', $request->frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $request->todate);
		$to_date = implode("_", $to_date);
		//print_r($from_date);die();
		if($from_date == $to_date) {
			$data = TimeSheet::with('companies')
				->with('users')
				->with('houses')
				->where('hours_day', $from_date)
				->where('users_id', '=', $user)
				->orderBy('created_at', 'DESC')
				->get();
		} else {
			$data = TimeSheet::with('companies')
				->with('users')
				->with('houses')
				->where('users_id', '=', $user)
				->whereBetween('hours_day', array($from_date, $to_date))
				->orderBy('created_at', 'DESC')
				->get();
		}
		$count = 1;
		$cm_check = $this->checkCM($user);
		if($data->count() != 0) {
			foreach ($data as $datas) {
				echo '<tr>';
				echo '<td>'.$count.'.</td>';
				echo '<td>'.$datas->users->emp_id.'</td>';
				if(!empty($cm_check)) { 
					if($datas->cmcheck_status == 2) { 
						$caseManager = $this->caseManager($datas->cm_id);
						echo  '<td>';
						if(!empty($caseManager)) {
							echo '<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" >'.$caseManager.'</label><br>';
							echo '<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" >'.date('M d, Y h:i a', strtotime($datas->cm_update_at )).'</label><br>';
				     		echo '<label style="background:green;color:#fff;padding:5px;border-radius: 5px;" >'.date('(D)', strtotime($datas->cm_update_at )).'</label>';
						}
						echo '</td>';
					} else {
						echo '<td ></td>';
					}
				}
				echo  '<td>';
				echo '<a  href="'.url('/').'/user/edit/timesheets/'.$datas->id.'" title="Edit"><i class="fa fa-pencil"></i></a>';
				echo '<a style="margin-left: 5px;" data-baseURL="'.url('/').'" data-ID="'.$datas->id.'" class="delete_ats" title="Delete"><i class="fa fa-trash-o"></i></a>';
				echo '</td>';
				echo '<td>'.$datas->users->name.'</td>';
				echo '<td>'.$datas->users->dept.'</td>';
				echo '<td>'.$datas->companies->company.'</td>';
				echo '<td>'.substr($datas->houses->house_add, 0, 4).'</td>';
				echo '<td>'.$datas->time_in.'</td>';
				echo '<td>'.$datas->time_out.'</td>';
				$hours    = explode('_', $datas->hours_wrk);
				$hours = implode(":", $hours);
				echo '<td>'. $hours.'</td>';
				$hours_day    = explode('_', $datas->hours_day);$hours_day = implode("/", $hours_day); $hours_day = date("M d, Y(D)", strtotime($hours_day));
				echo '<td>'. $hours_day.'</td>';
				echo '<td>'. $datas->users->hourst_rate.'</td>';
				echo '<td>';
				if($datas->vacation_status == "0"){ echo "<h5 style='color:green'>No</h5>"; }elseif($datas->vacation_status == "1"){ echo "<h5 style='color:red'>Yes</h5>"; }
				echo '</td>';
				echo  '<td>';
				if($datas->approve == "2"){ echo "<h5 style='color:green'>Yes</h5>"; }
				elseif($datas->approve == "1"){ echo "<h5 style='color:red'>No</h5>"; }
				else{ echo "<h5 style='color:#a5a548'>Pending</h5>"; } 
				echo '</td>';
				echo "<td>";
				if(!empty($datas->approved_by)){ echo $this->userName($datas->approved_by); }else{ echo "--"; }
				echo "</td>";
				echo '<td>'. date('M d, Y h:i a', strtotime($datas->created_at )).'</td>';
				echo "<td>";
				if(!empty($datas->approved_at)){ echo date('M d, Y h:i a', strtotime($datas->approved_at )); }else{ echo "--"; }
				echo "</td>";
				echo '</tr>';
				$count++;
			}
		} else {
			echo '<tr>
			<td colspan="15" class="no-data">
			Sorry, No data found!
			</td>
			</tr>';
		}	  
    }
	
	/**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

	//Function for export timesheet
    public function export_data($id) {
		//Get auth detail
        $user = $id;
        $data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $user)->orderBy('hours_day', 'DESC')->get();
		$time_sheet[] = array('#','Emp ID','Email', 'Last Name','First Name','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved');
		$count = 1;
		foreach($data as $datas) {
			$hours_day    = explode('_', $datas->hours_day);
			$hours_day = implode("/", $hours_day); 
			$hours_day = date("M d, Y", strtotime($hours_day)); 
			if($datas->vacation_status == "0") { 
			$vacation_status = "No"; 
			} elseif($datas->vacation_status == "1") {
				$vacation_status = "Yes";
			} else {
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

	//Function for search export data
    public function srcexport_data($frmdate,$todate,$id) {
		//Get auth
        $user = $id;
        $from_date    = explode('-', $frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $todate);
		$to_date = implode("_", $to_date);
		
		if($from_date == $to_date) {
			$data = TimeSheet::with('companies')
				->with('houses')
				->with('users')
				->where('hours_day', $from_date)
				->where('users_id', '=', $user)
				->orderBy('created_at', 'DESC')
				->get();
		} else {
			$data = TimeSheet::with('companies')
				->with('houses')
				->with('users')
				->where('users_id', '=', $user)
				->whereBetween('hours_day', array($from_date, $to_date))
				->orderBy('created_at', 'DESC')
				->get();
		}
		//Header
		$time_sheet[] = array('#','Emp ID','Email', 'Last Name','First Name','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved');
		$count = 1;
		foreach($data as $datas) {
			$hours_day    = explode('_', $datas->hours_day);
			$hours_day = implode("/", $hours_day); 
			$hours_day = date("M d, Y", strtotime($hours_day)); 
			if($datas->vacation_status == "0") { 
			$vacation_status = "No"; 
			} elseif($datas->vacation_status == "1") {
				$vacation_status = "Yes";
			} else {
				$vacation_status = "";
			}
			if($datas->approve == "2") { 
				$approve = "Yes";
			} elseif($datas->approve == "1") {
				$approve = "No";
			} else { 
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
	
	
	//Function for user name
	public static function userName($id) {
		//Get user
		$user = User::where('id', "=", $id)->first();
		$name = $user->name;
		return $name;
	}
	
	//Function for checkCm
	public static function checkCM($id) {
		//Get case manager
		$Casemanager = UserCasemanagerRel::where('users_id', "=", $id)->first();
		$name = "";
		if(isset($Casemanager)) {
			$cmUser = User::where('id', "=", $Casemanager->casemanager_id)->first();
			$name = $cmUser->name;
		}
		return $name;
	}
	
	//Function for case manager
	public static function caseManager($id) {
		//Get user
		$user = User::where('id', "=", $id)->first();
		$name = "";
		if(isset($user)){
			$name = $user->name;
		}
		return $name;
	}
}
