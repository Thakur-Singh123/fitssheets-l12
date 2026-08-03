<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\TimeSheet;
use App\Models\User;
use App\Models\Company;
use App\Models\Holiday;
use App\Models\House;
use App\Models\UserManager;
use Excel;
use App\Models\AdminMeta;
use Carbon\Carbon;
use App\Models\LoginLogouttime;
use DateTime;
use App\Payperiods;
use Twilio\Rest\Client;
use App\Models\UserVaccatioStatusn;
use App\Models\UserVaccation;
use App\Models\SmsLog;
use App\Models\SmsLogCompany;

class AdminController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
		$admin = User::where('role', '=', "admin")->orderBy('created_at', 'DESC')->get();
		$admins = count($admin );
		$manager = User::where('role', '=', "manager")->orderBy('created_at', 'DESC')->get();
		$managers = count($manager );
		$supervisor = User::where('role', '=', "supervisor")->orderBy('created_at', 'DESC')->get();
		$supervisors = count($supervisor );
		$user = User::where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$users = count($user );
		$appIds = [1, 2, 3];
		$dataapp = TimeSheet::with('companies')->with('users')->with('houses')->where('approve', '=', 2)->orderBy('hours_day', 'DESC')->get();
		$dataapps = count($dataapp );
		
		$datanapp = TimeSheet::with('companies')->with('users')->with('houses')->where('approve', '=', 1)->orderBy('hours_day', 'DESC')->get();
		$datanapps = count($datanapp );
		//dd($dataapp);
		$dt = Carbon::now();
		$current_date_time = $dt->toDateString();
		$date    = explode('-', $current_date_time);
		$date = implode("_", $date);
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('hours_day', '=', $date)->orderBy('hours_day', 'DESC')->paginate(15);
        return view('admin.dashboard',compact('data','current_date_time','admins','managers','supervisors','users','dataapps','datanapps'));
    }
	
	
	/**
     * Logout
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
	public function logout(Request $request) {
	  $form_data = array(
				'users_id' => Auth::user()->id,
				'last_logout_at' => Carbon::now()->toDateTimeString(),
			);
	  $login_inf = LoginLogouttime::create($form_data);
	  Auth::logout();
	  return redirect('/login');
	}

	//Function for edit profile
    public function edit_profile() {
		return view('admin.profile');
    }

	//Function for update password
	public function update_profile(Request $request) {
		//Validate input fields
		$request->validate([
			'fname' => 'required',
			'phone_no' => 'required',
		]);
		//Check if image is exit or not
		$filename = "";
		if($request->hasFile('avtar')) {
			$file = $request->file('avtar');
			$extension = $file->getClientOriginalExtension();
			$filename = time() . '.' . $extension;
			$file->move(public_path('assets/uploads/admin'), $filename);
			//Concreate name
			$name = $request->fname." ".$request->lname;
			//Update user with image
			$use_dl = User::whereId($request->user_id)->update(array(
				'name' => $name,
				'first_name' => $request->fname,
				'last_name' => $request->lname,
				'phone_no' => $request->phone_no,
				'avtar' => $filename,
			));
			//Check if profile updated or not
			if($use_dl) {
			    return back()->with('success','Profile updated successfully.');
			} else {
			    return back()->with('error','Opps something went wrong!');
			}
		} else {
			//Concreate name
			$name = $request->fname." ".$request->lname;
			//Update user without image
		    $use_dl = User::whereId($request->user_id)->update(array(
				'name' => $name,
				'first_name' =>	$request->fname,
				'last_name' =>	$request->lname,
				'phone_no' => $request->phone_no,
			));
			//Check if profile updated or not
			if($use_dl) {
			    return back()->with('success','Profile updated successfully.');
			} else {
				return back()->with('error','Opps something went wrong!');
			}
	    }
	}

	/**
     * User Reset Password
     *
     * @return void
     */
    public function resetpassword()
    {
		return view('admin.change_pass');
    }
	
	
	//function for update profile password
	public function updatepassword(Request $request){
	    request()->validate([
            'current_password' => 'required',
            'new_password' => 'min:6|required_with:confirm_password|same:new_confirm_password',
            'new_confirm_password' => 'required',
        ]);
		//update user password
		// $user = auth()->user(); 
		$user_id = Auth::user()->id;
		$user = User::where('id', '=', $user_id)->first(); 

		if(!Hash::check($request['current_password'], $user->password)){
			return back()->with('error','Current password is incorrect!');
		} else {
			$update_pass = DB::table('users')
			->where('id', $user->id)
			->where('role', $user->role)
			->update([
			'password' => bcrypt($request['new_password']),
			'pass' => $request['new_password'],
			]);
			if($update_pass){
				return back()->with('success','Your Password is updated successfully.');
			} else {
				return back()->with('error','Oops something went wrong');
			}
		}
	}
	
	

	 public function approve_vchour()
    {
		$approve_vchour = UserVaccatioStatusn::orderBy('created_at', 'DESC')->get();
		// $user = User::where("role", "=", "user")->orderBy('created_at', 'DESC')->get();
        return view('admin.approve_vchour',compact('approve_vchour'));
    }

    public function vacc_approve(Request $request)
    {
		$vacc_id = $request->vacc_id;
		$app_id = $request->app_id;
		$data_user_vacc = UserVaccatioStatusn::where('id','=', $vacc_id)->orderBy('created_at', 'DESC')->first();
		if(isset($data_user_vacc) && $data_user_vacc->vacc_status != 1){
			$vacc_user = $data_user_vacc->user_id;
			
			$data_vacc = UserVaccation::where('user_id','=', $vacc_user)->orderBy('created_at', 'DESC')->first();

			$vacc_frm    = explode('_', $data_user_vacc->vacc_start);
			$vacc_frm = implode("-", $vacc_frm);
			$vacc_to    = explode('_', $data_user_vacc->vacc_end);
			$vacc_to = implode("-", $vacc_to);
			$date1 = new DateTime(date('m/d/y', strtotime($vacc_frm)));
			$date2 = new DateTime(date('m/d/y', strtotime($vacc_to)));

			$diff = $date2->diff($date1);

			$days = $diff->days;
			$hours = $diff->h;
			$hours = $hours + ($diff->days*24);
			$hours = floatval(8*$days);
			// return $hours;
			// 	die;
			if(isset($data_vacc)){
				$used_hours = $data_vacc->vacc_vc;
				$avail_hours = $data_vacc->vacc_sl;
				$hours_requested = $hours;
				$check_hours = $avail_hours - $hours_requested;
				// return $check_hours;
				// die;
				if($check_hours >= 0){
					$used_hours = $used_hours + $hours_requested;
					$avail_hours = $avail_hours - $hours_requested;
					
					$form_data = array(
						'vacc_sl' => $avail_hours,
						'vacc_vc' => $used_hours,
						'vacc_aprby' => $app_id,
					);
					$form_dat1 = array(
						'vacc_status' => 1,
						
					);
					$user_update = UserVaccation::where('user_id','=', $vacc_user)->update($form_data);
					$user_status_update = UserVaccatioStatusn::where('user_id','=', $vacc_user)->update($form_dat1);
					if($user_update){
						return "Vaccation Approved";

					}else{
						return "Error!";
					}
				}else{
					return 'Vaccation hours are not left or have used your all hours for this user.';
				}	
			}else{
					return 'Vaccation hours are not assign to this user, please contact admin';
			}
		}else{
			return "Action is already taken!!";
		}
		
    }

    public function vacc_decline(Request $request)
    {
		$vacc_id = $request->vacc_id;
		$app_id = $request->app_id;
		$data_user_vacc = UserVaccatioStatusn::where('id','=', $vacc_id)->orderBy('created_at', 'DESC')->first();
		if(isset($data_user_vacc) && $data_user_vacc->vacc_status != 1){
			$vacc_user = $data_user_vacc->user_id;
			
			$data_vacc = UserVaccation::where('user_id','=', $vacc_user)->orderBy('created_at', 'DESC')->first();

			$vacc_frm    = explode('_', $data_user_vacc->vacc_start);
			$vacc_frm = implode("-", $vacc_frm);
			$vacc_to    = explode('_', $data_user_vacc->vacc_end);
			$vacc_to = implode("-", $vacc_to);
			$date1 = new DateTime(date('m/d/y', strtotime($vacc_frm)));
			$date2 = new DateTime(date('m/d/y', strtotime($vacc_to)));

			$diff = $date2->diff($date1);

			$days = $diff->days;
			$hours = $diff->h;
			$hours = $hours + ($diff->days*24);
			$hours = floatval(8*$days);

			if(isset($data_vacc)){
				$used_hours = $data_vacc->vacc_vc;
				$avail_hours = $data_vacc->vacc_sl;
				$hours_requested = $hours;
				$check_hours = $avail_hours - $hours_requested;
				if($check_hours >= 0){
					$used_hours = $used_hours + $hours_requested;
					$avail_hours = $avail_hours - $hours_requested;
					
					$form_data = array(
						'vacc_sl' => $avail_hours,
						'vacc_vc' => $used_hours,
						'vacc_aprby' => $app_id,
					);
					$form_dat1 = array(
						'vacc_status' => 2,
						// 'vacc_aprby' => $app_id,
					);
					// $user_update = UserVaccation::where('user_id','=', $vacc_user)->update($form_data);
					$user_status_update = UserVaccatioStatusn::where('user_id','=', $vacc_user)->update($form_dat1);
					if($user_status_update){
						return "Vaccation Decline";

					}else{
						return "Error!";
					}
				}else{
					return 'Vaccation hours are not left or have used your all hours for this user.';
				}	
			}else{
					return 'Vaccation hours are not assign to this user, please contact admin';
			}
		}else{
			return "Action is already taken!!";
		}
		
    }

    public static function avail_vacchours($id)
    {
    	$avail_vacchours = UserVaccation::where("user_id", "=", $id)->orderBy('created_at', 'DESC')->get();
    	return $avail_vacchours;
    }
	
	//Function for add send sms
	public function sendSmsview() {
		//Get companies
		$companies = Company::orderBy('created_at', 'DESC')->get();
		//Get usres
		$user = User::where("role", "=", "user")->orderBy('created_at', 'DESC')->get();
        return view('bulksms',compact('user','companies'));
    }

	//Function for send sms
	public function sendSms(Request $request) {
		//Validate input filed
		$request->validate([
			'message' => 'required',
		]);
		//Twilio
		$client = new Client(
			env('TWILIO_SID'),
			env('TWILIO_TOKEN')
		);
		//var
		$numbers = [];
		$userIds = [];
		$companyId = null;
		//Company id
		if (!empty($request->company_id)) {
			$companyId = is_array($request->company_id)
				? implode(',', $request->company_id)
				: $request->company_id;
		}
		//Select all users
		if ($request->all_user == 1) {
			$userIds = User::where('role', 'user')
				->pluck('id')
				->toArray();
		}
		//Company users
		if (!empty($request->company_id)) {
			$companyIds = is_array($request->company_id)
				? $request->company_id
				: explode(',', str_replace(['[', ']'], '', $request->company_id));
			$companyUsers = UserManager::whereIn(
				'users_id',
				$companyIds
			)->pluck('musers_id')->toArray();
			$userIds = array_merge($userIds, $companyUsers);
		}
		//Selected users only
		if (!empty($request->users_id)) {
			$userIds = $request->users_id;
		}
		//Unique users
		$userIds = array_unique($userIds);
		foreach ($userIds as $id) {
			$user = User::find($id);
			//DB number exists
			if ($user && !empty($user->phone_no)) {
				$phone = preg_replace(
					'/[^0-9]/',
					'',
					$user->phone_no
				);
				if (strlen($phone) == 10) {
					$numbers[] = [
						'users_id' => $id,
						'phone_no' => '+91' . $phone
					];
				}
			} else {
				//If DB number null then use optional number
				if (!empty($request->numbers)) {
					foreach (explode(',', $request->numbers) as $no) {
						$phone = preg_replace(
							'/[^0-9]/',
							'',
							trim($no)
						);
						if (strlen($phone) == 10) {
							$numbers[] = [
								'users_id' => $id,
								'phone_no' => '+91' . $phone
							];
						}
					}
				} else {
					//No mobile number found
					$log = SmsLog::create([
						'users_id' => $id,
						'phone_no' => null,
						'message' => $request->message,
						'status' => 'No Mobile Number'
					]);
					if (!empty($request->company_id)) {
						foreach ($companyIds as $cid) {
							SmsLogCompany::create([
								'sms_log_id' => $log->id,
								'company_id' => trim($cid)
							]);
						}
					}
				}
			}
		}
		//Remove Duplicate Numbers
		$numbers = collect($numbers)
			->unique('phone_no')
			->values()
			->toArray();
		//Save original message in DB
		$dbMessage = $request->message;
		//Send branded SMS
		$message = "FitSheets Alert: " .
			$request->message .
			" - FitSheets Team";
		$count = 0;
		//Send SMS
		foreach ($numbers as $data) {
			try {
				$client->messages->create(
					$data['phone_no'],
					[
						'from' => env('TWILIO_FROM'),
						'body' => $message
					]
				);
				//Success log
				$log = SmsLog::create([
					'users_id' => $data['users_id'],
					'phone_no' => $data['phone_no'],
					'message' => $dbMessage,
					'status' => 'Sent'
				]);
				if (!empty($request->company_id)) {
					foreach ($companyIds as $cid) {
						SmsLogCompany::create([
							'sms_log_id' => $log->id,
							'company_id' => trim($cid)
						]);
					}
				}
				$count++;
			} catch (\Exception $e) {
				//Failed log
				$log = SmsLog::create([
					'users_id' => $data['users_id'],
					'phone_no' => $data['phone_no'],
					'message' => $dbMessage,
					'status' => 'Failed',
					'error' => $e->getMessage()
				]);
				if (!empty($request->company_id)) {
					foreach ($companyIds as $cid) {
						SmsLogCompany::create([
							'sms_log_id' => $log->id,
							'company_id' => trim($cid)
						]);
					}
				}
			}
		}
		//Response
		return back()->with(
			'success',
			$count . ' SMS sent successfully!'
		);
	}

	/**
     * User Reset Password
     *
     * @return void
     */
    public function add_billrate()
    {
		$AdminMeta = AdminMeta::where('meta_key', '=', 'billed_rate')->first();
		return view('admin.add_billedrate',compact('AdminMeta') );
    }
	
	
	public function all_users_view(){
		$users = User::where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		return view('admin.reports.all_users_view',compact('users'));
	}
	
	public function all_users_vaccine_report(){
		$users = User::where('role', '=', "user")->orderBy('name', 'ASC')->get();
		return view('admin.reports.all_users_vaccine_report',compact('users'));
	}
	
	public function vaccine_status(Request $request){
		$vaccine_status = $request->vaccine_status;

		if($vaccine_status == '1'){
			$users = User::where('role', '=', "user")->where('covid_report', 'like', '%emp_covid_report%')->orderBy('name', 'ASC')->get();
		}elseif($vaccine_status == '0'){
			$users = User::where('role', '=', "user")->where('covid_report', '=', '0')->orderBy('name', 'ASC')->get();
		}else{
			$users = User::where('role', '=', "user")->where('covid_report', '=', '')->orderBy('name', 'ASC')->get();
		}
		$count = 1;
			  if($users->count() != 0){
				foreach ($users as $datas){
					echo '<tr>';
					  echo '<td>'.$count.'</td>';
					  echo '<td>'.$datas->email.'</td>';
					 echo '<td>'.$datas->name.'</td>';
					  echo '<td>';
					  if($datas->covid_report == ""){ echo "<p style='color: #000;background: yellow;width: 50%;text-align: center;padding: 5px;border-radius: 10px;'>--</p>"; }elseif( $datas->covid_report == '0'){ echo "<p style='color: #fff;background: red;width: 50%;text-align: center;padding: 5px;border-radius: 10px;'>No</p>";}elseif( $datas->covid_report != "" && $datas->covid_report != '0'){ echo "<p style='color: #fff;background: green;width: 50%;text-align: center;padding: 5px;border-radius: 10px;'>yes</p>"; }else{ echo "<p style='color: #000;background: yellow;width: 50%;text-align: center;padding: 5px;border-radius: 10px;'>--</p>"; }
					  echo '</td>';
					  echo '<td>';
					  if($datas->covid_report != null && $datas->covid_report != '0' ){ 
					echo '<img style=" margin-top: 15px; width: 152px;height: 156px;border-radius: inherit !important;" src="'.url('/assets/uploads/covid-report').'/'.$datas->covid_report.'">';
					} else{ 
						echo '<p>No Report Found!</p>';
						
					}  
				echo '</td>';
					echo '</tr>';
				 $count++;
				}
				
				}else{
					echo '<p>Sorry No Data!!</p>';
				}
	}
	
	
	
	//function for all users
	public function all_users(){
		
		$user = User::where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$users_report[] = array('#','Emp ID', 'Last Name','First Name','Name', 'Email','Department', 'Company', 'Hourley Rate($)');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){
				$user_companies = $this->user_companies($userss->id);
				$users_report[] = array(
						'#' => $user_count,
						'Emp ID' => $userss->emp_id,
						'Last Name'   => $userss->last_name,
						'First Name'   => $userss->first_name,
						'Name'  => $userss->name,
						'Email'   => $userss->email,
						'Department'   => $userss->dept,
						'Company'   =>  $user_companies,
						'Hourley Rate'   =>  $userss->hourst_rate,						
					);		
					$user_count++;
			}
		}
		Excel::create('All Users', function($excel) use ($users_report){
			$excel->setTitle('All Users');
			$excel->sheet('All Users', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
	public function all_users_with_id_view(){
		
		$users = User::where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		return view('admin.reports.all_users_with_id_view',compact('users'));
	}
	
	//All user with ID
	public function all_users_with_id(){
	
		$user = User::where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$users_report[] = array('#','Emp ID','Name');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){
				$users_report[] = array(
						'#' => $user_count,
						'Emp ID' => $userss->emp_id,
						'Name'  => $userss->name,
					);		
					$user_count++;
			}
		}
		Excel::create('All Users With ID', function($excel) use ($users_report){
			$excel->setTitle('All Users With ID');
			$excel->sheet('All Users With ID', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
	public function sign_signout_view(){
		$LoginLogouttime = LoginLogouttime::orderBy('created_at', 'DESC')->get();
		return view('admin.reports.sign_signout_view',compact('LoginLogouttime'));
	}
	
		//All user Log out/Log In
	public function sign_signout(){
		
		$LoginLogouttime = LoginLogouttime::orderBy('created_at', 'DESC')->get();
		
		$users_report[] = array('#','Emp ID', 'Last Name','First Name','Name','Type','Status', 'Date', 'Time');
		$user_count = 1;
		if(isset($LoginLogouttime)){
			foreach($LoginLogouttime as $LoginLogouttimes){
				$user = User::where('id', '=', $LoginLogouttimes->users_id)->first();
				
				if($LoginLogouttimes->last_login_at != null){
					$status = "Log In";
					$date = date('M d, Y', strtotime($LoginLogouttimes->last_login_at ));
					$time = date('h:i a', strtotime($LoginLogouttimes->last_login_at));
				}
				if($LoginLogouttimes->last_logout_at != null){
					$status = "Log Out";
					$date = date('M d, Y', strtotime($LoginLogouttimes->last_logout_at));
					$time = date('h:i a', strtotime($LoginLogouttimes->last_logout_at));
				}
				$users_report[] = array(
						'#' => $user_count,
						'Emp ID' => $user->emp_id,
						'Last Name'  => $user->last_name,
						'First Name'  => $user->first_name,
						'Name'  => $user->name,
						'Type'  => $user->role,
						'Status'  => $status,
						'Date'  => $date,
						'Time'  => $time,
					);		
					$user_count++;
			}
		}
		Excel::create('All Users Log In Log Out', function($excel) use ($users_report){
			$excel->setTitle('All Users Log In Log Out');
			$excel->sheet('All Users Log In Log Out', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
	public function all_app_timesheet_view(){
		$user = User::where('role', '=', "user")->orderBy('name', 'DESC')->paginate(15);
		//dd($user);
		return view('admin.reports.all_app_timesheet_view',compact('user'));
	}
	
	public function all_app_search_timesheet(Request $request){
	
		$from_date    = explode('-', $request->frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $request->todate);
		$to_date = implode("_", $to_date);

		$user = User::where('role', '=', "user")->orderBy('name', 'DESC')->get();
		$time_sheet[] = array('Emp ID','Email','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved');
		
		if(isset($user)){
			foreach($user as $users){
				if($from_date == $to_date){
					$data = TimeSheet::with('companies')->with('users')->with('houses')->where('hours_day', $from_date)->where('approve', '=', 2)->where('users_id', '=', $users->id)->orderBy('hours_day', 'DESC')->get();
				}else{
					$data = TimeSheet::with('companies')->with('users')->with('houses')->whereBetween('hours_day', array($from_date, $to_date))->where('approve', '=', 2)->where('users_id', '=', $users->id)->orderBy('hours_day', 'DESC')->get();
				}
				
				if(isset($data)){
					foreach($data as $time)
					{
						
						$hours_day    = explode('_', $time->hours_day);
						 $hours_day = implode("/", $hours_day); 
						 $hours_day = date("M d, Y", strtotime($hours_day)); 
						 if($time->vacation_status == "0"){ 
							$vacation_status = "No"; 
						 }elseif($time->vacation_status == "1"){
							 $vacation_status = "Yes";
							}else{
								$vacation_status = "";
							}
								 
						if($time->approve == "2"){ 
								$approve = "Yes";
						}elseif($time->approve == "1"){
							$approve = "No";
						}else{
							$approve = "Pending"; 
						}
					echo "<tr>";
					 echo "<td>".$time->users->emp_id."</td>";
					 echo "<td>".$time->users->email."</td>";
					   echo "<td>".$time->users->name."</td>";
						 echo "<td>".$time->users->dept."</td>";
						  echo "<td>".$time->companies->company."</td>";
						   echo "<td>".$time->houses->house_add."</td>";
						   echo "<td>".$time->time_in."</td>";
							 echo "<td>".$time->time_out."</td>";
							 echo "<td>".$time->hours_wrk."</td>";
							   echo "<td>".$hours_day."</td>";
							       echo "<td>".$time->users->hourst_rate."</td>";
							 echo "<td>".$vacation_status."</td>";
							  echo "<td>".$approve."</td>";
					echo "</tr>";
					}
				}
			}
		}
		
	}
	public static function exp_user_companies($id)
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
		//All user with ID
	public function all_app_timesheet(){
	
		$user = User::where('role', '=', "user")->orderBy('name', 'DESC')->get();
		$time_sheet[] = array('Emp ID','Email', 'Last Name','First Name','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved');
		
		if(isset($user)){
			foreach($user as $users){
				$data = TimeSheet::with('companies')->with('users')->with('houses')->where('approve', '=', 2)->where('users_id', '=', $users->id)->orderBy('hours_day', 'DESC')->get();
				if(isset($data)){
					foreach($data as $datas)
					{
						 $hours_day    = explode('_', $datas->hours_day);
						 $hours_day = implode("/", $hours_day); 
						 $hours_day = date("M d, Y", strtotime($hours_day)); 
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
							'Emp ID' => $datas->users->emp_id,
							'Email'  => $datas->users->email,
							'Last Name'  => $datas->users->last_name,
							'First Name'  => $datas->users->first_name,
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
					}
				}
			}
		}

		Excel::create('Approved Time Sheet', function($excel) use ($time_sheet){
			$excel->setTitle('Approved Time Sheet');
			$excel->sheet('Approved Time Sheet', function($sheet) use ($time_sheet){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
	public function all_supervisor_assign_user_view(){
		$supervisor = User::where('role', '=', "supervisor")->orderBy('name', 'DESC')->get();
		return view('admin.reports.all_supervisor_assign_user_view',compact('supervisor'));
	}
	
			//All Supervisor Assign user
	public function all_supervisor_assign_user(){
		$suoervisor = User::where('role', '=', "supervisor")->orderBy('name', 'DESC')->get();
		$users_report[] = array('Emp ID', 'Last Name','First Name','Name', 'Email','Department', 'Company', 'Hourley Rate');
		
		if(isset($suoervisor)){
			foreach($suoervisor as $suoervisors){
				$UserManager = UserManager::where('musers_id', '=', $suoervisors->id)->first();
				$company_id = $UserManager->users_id;
				$company = Company::where('id', '=', $company_id)->first();
				$user = User::where('role', '=', "user")->where('companies_id', '=', $company->company)->orderBy('name', 'DESC')->get();
				$users_report[] = array(
								'Emp ID' => "Supervisor",
								'Last Name'   => "",
								'First Name'   => "",
								'Name'  => $suoervisors->name,
								'Email'   => "",
								'Department'   => "",
								'Company'   =>  "",
								'Hourley Rate'   =>  "",						
							);		
				if(isset($user)){
					foreach($user as $userss){
						$user_companies = $this->user_companies($userss->id);
						$users_report[] = array(
								'Emp ID' => $userss->emp_id,
								'Last Name'   => $userss->last_name,
								'First Name'   => $userss->first_name,
								'Name'  => $userss->name,
								'Email'   => $userss->email,
								'Department'   => $userss->dept,
								'Company'   =>  $user_companies,
								'Hourley Rate'   =>  $userss->hourst_rate,						
							);		
					}
				}
			}
		}
		Excel::create('Supervisor Users', function($excel) use ($users_report){
		$excel->setTitle('Supervisor Users');
		$excel->sheet('Supervisor Users', function($sheet) use ($users_report){
			$sheet->fromArray($users_report, null, 'A1', false, false);
		});
		})->download('xlsx');
		
	}
	
	
	public function search_by_payperiod(){
		$payperiods_dates = Payperiods::orderBy('created_at', 'DESC')->get();
		return view('admin.reports.search_by_pay',compact('payperiods_dates'));
	}
	
	public function post_data(Request $request){

		$bet_dates = explode('-',$request->payperiod);
		if(isset($bet_dates)){
			$from_date    = $bet_dates[0];
			$to_date    = $bet_dates[1];
		}
		$xto_date = explode('_',$to_date);
		$xto_date = implode('-',$xto_date);
		$xfrom_date = explode('_',$from_date);
		$xfrom_date = implode('-',$xfrom_date);
		$xpto_date = date('Y-m-d', strtotime($xto_date. ' + 5 days'));
		if($from_date != "" && $to_date != ""){
			$data = TimeSheet::with('companies')
								->with('users')
								->with('houses')
								->whereBetween('hours_day', array($from_date, $to_date))
								->orderBy('created_at', 'DESC')
								->get();
		}else{
			$data = "";
		}
		if(isset($data)){
			$time_sheet[] = array('#','Emp ID','Email', 'Last Name','First Name','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved');
			$time_sheet[] = array(
					'#' => "Pay Period",
					'Emp ID' => "",
					'Email'  => date("M d, Y", strtotime($xfrom_date)),
					'Last Name'   => date("M d, Y", strtotime($xto_date)),
					'First Name'   => "",
					'Name'  => "",
					'Department'   =>"",
					'Company'  =>"",
					'House'  => "",
					'Time In'   => "",
					'Time Out'   => "",
					'Hours Worked'   => "",
					'Day'    => "",
					'Hours Rate'  => "",
					'Vacation'   => "",
					'Approved'    => ""
				);
				$time_sheet[] = array(
					'#' => "Pay Date",
					'Emp ID' => "",
					'Email'  => "",
					'Last Name'   => date("M d, Y", strtotime($xpto_date)),
					'First Name'   => "",
					'Name'  => "",
					'Department'   =>"",
					'Company'  =>"",
					'House'  => "",
					'Time In'   => "",
					'Time Out'   => "",
					'Hours Worked'   => "",
					'Day'    => "",
					'Hours Rate'  => "",
					'Vacation'   => "",
					'Approved'    => ""
				);
			$count = 1;
			foreach($data as $datas)
			{
				 $hours_day    = explode('_', $datas->hours_day);
				 $hours_day = implode("/", $hours_day); 
				 $hours_day = date("M d, Y", strtotime($hours_day)); 
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
		Excel::create('Time Sheet By Payperiod', function($excel) use ($time_sheet){
			$excel->setTitle('Time Sheet By Payperiod');
			$excel->sheet('Time Sheet By Payperiod', function($sheet) use ($time_sheet){
			$sheet->fromArray($time_sheet, null, 'A1', false, false);
		});
		})->download('xlsx');
			
	}else{
		echo "No Timesheet for serached Payperiod!";
	}
		
	}
	//finace report
	public static function finacereport()
    {
        $data = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->paginate(15);
		//dd($data);
		$Supervisor_color = User::where("role", "=", "supervisor")->orderBy('name', 'ASC')->get();
		$payperiods_dates = Payperiods::orderBy('created_at', 'DESC')->get();
		//$payperiods_dates = Payperiods();
			//$payperiods_dates1 = payperiods();
			$payperiods_dates1 = Payperiods::with('companies')->orderBy('created_at', 'DESC')->get();
		if(isset($payperiods_dates1)){
			 $frm_date  = $payperiods_dates1[0]['frm_date'];
			 $t_date = $payperiods_dates1[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		if(!empty($frm_dt) && !empty($to_dt)){
			$frm_date = $frm_dt;
			$t_date = $to_dt;
		}

		//$companies = Company::orderBy('company', 'ASC')->get();
		$companies = Company::orderBy('display_order', 'ASC')->get();
		return view('admin.reports.finace',compact('data','companies','payperiods_dates1','payperiods_dates','frm_date','t_date','Supervisor_color'));
    }
	
	
	public static function payrollreport()
    {
        $data = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->paginate(15);
		//$payperiods_dates = Payperiods::orderBy('created_at', 'DESC')->get();
		$payperiods_dates = Payperiods::with('companies')->orderBy('created_at', 'DESC')->get();
		$companies = Company::orderBy('display_order', 'ASC')->get();
		//$companies = Company::orderBy('company', 'ASC')->get();
		return view('admin.reports.payrollreport',compact('data','companies','payperiods_dates'));
    }
	
	public function serach_payroll(Request $request){
		
		
		/*$holidays = array(  '05/30/2022',
							'07/04/2022',
							'09/06/2022',
							'11/25/2022',
							'12/25/2022',
							
							);*/
		$search_by_comp = $request->search_by_comp;
		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			$user_companies = UserManager::where('users_id', '=', $search_by_comp)->get();
		
			
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$users_arrr[] = $user_company->musers_id;
				}
			}
			
		}

		$users = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();
		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}
		$bet_dates = explode('-',$request->payperiod);
		if(isset($bet_dates)){
			$from_date    = $bet_dates[0];
			$to_date    = $bet_dates[1];
		}
		
		$xto_date = explode('_',$to_date);
		$xto_date = implode('-',$xto_date);
		$xfrom_date = explode('_',$from_date);
		$xfrom_date = implode('-',$xfrom_date);
		$xpto_date = date('Y-m-d', strtotime($xto_date. ' + 5 days'));

		$users_arrr = array_unique($users_arrr);
		if($from_date != "" && $to_date != ""){
			$data = TimeSheet::with('companies')
								->with('users')
								->whereIn('users_id', $users_arrr)
								->whereBetween('hours_day', array($from_date, $to_date))
								->distinct('users_id')
								->orderBy('created_at', 'DESC')
								->get();
		}else{
			$data = "";
		}
		
		$time_sheet_users =array();
		if(isset($data)){
			
			$count = 1;
			foreach($data as $datas)
			{	
				$time_sheet_users[] = $datas->users_id;
			}
			$time_sheet_users = array_unique($time_sheet_users);
			$user_pay = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();
	        $holidays  = Holiday::all();
			if($user_pay){
				foreach($user_pay as $user_pays)
				{	 
					$entry_date = date('m/d/Y', strtotime($xto_date));
					$total_time = $this->ttotal_time($user_pays->id, $from_date, $to_date);
				        $cxto_date = explode('_',$to_date);
                		$cxto_date = implode('-',$cxto_date);
                		$cxfrom_date = explode('_',$from_date);
                		$cxfrom_date = implode('-',$cxfrom_date);
                		$holiday_dt = "";
						$holiday_dt_arr = array();
                		if(isset($holidays)){
                			foreach($holidays as $holiday){
                				$holiday = new DateTime($holiday->date);
                				$cto_date = new DateTime($cxto_date);
                				$cfrom_date  = new DateTime($cxfrom_date);
                				if (
                				  $holiday->format('y-m-d') >= $cfrom_date->format('y-m-d') && 
                				  $holiday->format('y-m-d') <= $cto_date->format('y-m-d')){
                				  //$holiday_dt = $holiday->format('Y/m/d');
								  $holiday_dt_arr[] = $holiday->format('Y-m-d');
                				}
							}
                			
                		}
						
						// echo "<pre>";
						// print_r($holiday_dt_arr);
							// echo $holiday_dt;
							$holiday_tm = 0; 
							$holiday_count = 1;
					if(isset($holiday_dt_arr)){
						foreach($holiday_dt_arr as $holiday_dt_ar){
								$holiday_dt_ar = explode('-',$holiday_dt_ar);
								$holiday_dt_ar = implode('_',$holiday_dt_ar);
								$holiday_time  = TimeSheet::where('users_id','=', $user_pays->id)
										->where('approve', '=', 2)
										->where('hours_day','=', $holiday_dt_ar)
										->orderBy('created_at', 'DESC')
										->first();
										
										if(isset($holiday_time)){
											echo $holiday_time->hours_wrk."|";
											$holiday_tm = $holiday_time->hours_wrk + $holiday_tm;
										}
							
						}
					}
							// echo $holiday_tm."|";
                		// if(!empty($holiday_dt)){
                			// $holiday_dt = explode('/',$holiday_dt);
                			// $holiday_dt = implode('_',$holiday_dt);
                			// $holiday_time = $data = TimeSheet::where('users_id','=', $user_pays->id)
                					// ->where('hours_day','=', $holiday_dt)
                					// ->orderBy('created_at', 'DESC')
                					// ->first();
                					// if(isset($holiday_time)){
                				// $holiday_tm = $holiday_time->hours_wrk;
                					    
                					// }else{
                					    // $holiday_tm = "";
                					// }
                			
                		// }else{
                			// $holiday_tm = "";
                		// }
                		
                		if(isset($holiday_tm) && $holiday_tm > 0){
                		    $total_time = $total_time - $holiday_tm;
                		    $holiday_time = $holiday_tm;
                		}else{
                		     $total_time = $total_time;
                		    $holiday_time = 0;
                		}
					// echo $holiday_time;
					if(isset($total_time) && $total_time > 0){
						echo "<tr>";
					  echo "<td>".$entry_date."</td>";
					 echo "<td>".$user_pays->emp_id."</td>";
					  echo "<td>".$user_pays->last_name."</td>";
					  echo "<td>".$user_pays->first_name."</td>";
					   echo "<td>01</td>";
					   echo "<td>".$total_time."</td>";
					  echo "<td>".$user_pays->hourst_rate."</td>";
					echo "</tr>";
					}
					if(isset($holiday_time) && $holiday_time > 0){
					    foreach($holiday_dt_arr as $holiday_dt_ar){
								
								
								$holiday_dt_ar = explode('-',$holiday_dt_ar);
								$holiday_dt_ar = implode('_',$holiday_dt_ar);
								$holiday_times  = TimeSheet::where('users_id','=', $user_pays->id)
										->where('approve', '=', 2)
										->where('hours_day','=', $holiday_dt_ar)
										->orderBy('created_at', 'DESC')
										->first();
										
										if(isset($holiday_times)){
											$holiday_dt_ar = explode('_',$holiday_dt_ar);
											$holiday_dt_ar = implode('-',$holiday_dt_ar);
											$holiday_dt_ar  = new DateTime($holiday_dt_ar);
											echo "<tr>";
							  echo "<td>". $holiday_dt_ar->format('m-d-Y')."</td>";
							 echo "<td>".$user_pays->emp_id."</td>";
							  echo "<td>".$user_pays->last_name."</td>";
							  echo "<td>".$user_pays->first_name."</td>";
							   echo "<td>R0</td>";
							   echo "<td>".$holiday_times->hours_wrk."</td>";
							  echo "<td>".$user_pays->hourst_rate."</td>";
							echo "</tr>";
										}
								
						}
					}
				}
			}
		}else{
			echo "No Timesheet for serached Payperiod!";
		}
		
	}
	
	
	public function csv_post_ddata($payperiod,$search_by_comp){
		//dd($payperiod);die();
	  /* $holidays = array(  '05/30/2023',
							'07/04/2023',
							'09/06/2023',
							'11/25/2023',
							'12/25/2023',
							);*/
		$search_by_comp = $search_by_comp;
		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			$user_companies = UserManager::where('users_id', '=', $search_by_comp)->get();
		
			
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$users_arrr[] = $user_company->musers_id;
				}
			}
			
		}

		$users = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();

		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}
		$bet_dates = explode('-',$payperiod);
		if(isset($bet_dates)){
			$from_date    = $bet_dates[0];
			$to_date    = $bet_dates[1];
		}
		
		$xto_date = explode('_',$to_date);
		$xto_date = implode('-',$xto_date);
		$xfrom_date = explode('_',$from_date);
		$xfrom_date = implode('-',$xfrom_date);
		$xpto_date = date('Y-m-d', strtotime($xto_date. ' + 5 days'));
		
		$users_arrr = array_unique($users_arrr);
		if($from_date != "" && $to_date != ""){
			$data = TimeSheet::with('companies')
								->with('users')
								->whereIn('users_id', $users_arrr)
								->whereBetween('hours_day', array($from_date, $to_date))
								->distinct('users_id')
								->orderBy('created_at', 'DESC')
								->get();
		}else{
			$data = "";
		}
		
		$time_sheet_users =array();
		if(isset($data)){
			//$time_sheet[] = array('last_name','first_name','ssn', 'title','gusto_employee_id','regular_hours','overtime_hours','double_overtime_hours','holiday_hours','bonus','commission','paycheck_tips','cash_tips','gross_earnings','reimbursement','personal_note' );
			$time_sheet[] = array('last_name','first_name','ssn', 'company','title','regular_hours','gusto_employee_id','overtime_hours','double_overtime_hours','holiday_hours','bonus','commission','paycheck_tips','cash_tips','gross_earnings','reimbursement','personal_note' );
			$count = 1;
			
			foreach($data as $datas)
			{	
				
				$time_sheet_users[] = $datas->users_id;
			}
			$time_sheet_users = array_unique($time_sheet_users);
			$user_pay = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();
	         //dd($user_pay);die();
			 $user_count = 0;
			 $holidays  = Holiday::all();
			if($user_pay){
				
				foreach($user_pay as $user_pays)
				{	 
				$entry_date = date('m/d/y', strtotime($xto_date));
				$total_time = $this->ttotal_time($user_pays->id, $from_date, $to_date);
					
					$cxto_date = explode('_',$to_date);
                		$cxto_date = implode('-',$cxto_date);
                		$cxfrom_date = explode('_',$from_date);
                		$cxfrom_date = implode('-',$cxfrom_date);
						$holiday_dt = "";
						$holiday_dt_arr = array();
                		if(isset($holidays)){
                			foreach($holidays as $holiday){
                				$holiday = new DateTime($holiday->date);
                				$cto_date = new DateTime($cxto_date);
                				$cfrom_date  = new DateTime($cxfrom_date);
                				if (
                				  $holiday->format('y-m-d') >= $cfrom_date->format('y-m-d') && 
                				  $holiday->format('y-m-d') <= $cto_date->format('y-m-d')){
                				 // $holiday_dt = $holiday->format('Y/m/d');
								  $holiday_dt_arr[] = $holiday->format('Y-m-d');
                				}
							}
                			
                		}
						
					$holiday_tm = 0; 
					$holiday_count = 1;
					if(isset($holiday_dt_arr)){
						foreach($holiday_dt_arr as $holiday_dt_ar){
								$holiday_dt_ar = explode('-',$holiday_dt_ar);
								$holiday_dt_ar = implode('_',$holiday_dt_ar);
								$holiday_time  = TimeSheet::where('users_id','=', $user_pays->id)
										->where('approve', '=', 2)
										->where('hours_day','=', $holiday_dt_ar)
										->orderBy('created_at', 'DESC')
										->first();
										
										if(isset($holiday_time)){
											// echo $holiday_time->hours_wrk."|";
											$holiday_tm = $holiday_time->hours_wrk + $holiday_tm;
										}
						}
					}
                		
                			
                		
                		
                		if(isset($holiday_tm) && $holiday_tm > 0){
                		    $total_time = $total_time - $holiday_tm;
                		    $holiday_time = $holiday_tm;
                		}else{
                		     $total_time = $total_time;
                		    $holiday_time = 0;
                		}
					
					if(isset($holiday_time) && $holiday_time > 0){
						$holiday_time_sheet[] = array(
							// 'Entry Date' => $entry_date,
							// 'Emp ID' => $user_pays->emp_id,
							'last_name'           => $user_pays->last_name,
							'first_name'          => $user_pays->first_name,
							'ssn'                 => $user_pays->ssn_no,
							'title'               => 'Residential Counselor',
							'gusto_employee_id'   => '',
							'regular_hours'       => $total_time,
							'overtime_hours'       => $holiday_tm,
							'double_overtime_hours' =>  '',
							'holiday_hours'        => '',
							'bonus'                => '',
							'commission'           => '',
							'paycheck_tips'        => '',
							'cash_tips'            => '',
							'gross_earnings'       => '',
							'reimbursement'        => '',
							'personal_note'        =>  $user_companies,
						);
						
					}
				}
				//$report_name = 'Payroll'.$payperiod;
				//Excel::create($report_name, function($excel) use ($time_sheet,$report_name){
				//		$excel->setTitle($report_name);
					//	$excel->sheet($report_name, function($sheet) use ($time_sheet){
						//$sheet->fromArray($time_sheet, null, 'A1', false, false);
					//});
				//})->download('xlsx');
				$report_name = 'Payroll'.$payperiod;
                Excel::create($report_name, function($excel) use ($time_sheet,$report_name){
			            $excel->setTitle($report_name);
						$excel->sheet('Payroll', function($sheet) use ($time_sheet,$report_name){
				        $sheet->fromArray($time_sheet, null, 'A1', false, false);
				$sheet->row(1, array('','', '','','',$report_name,'', '','', '', '','', '', '',''));
				$sheet->row(2, array('#','last_name','first_name','ssn', 'title','gusto_employee_id', 'regular_hours','overtime_hours', 'double_overtime_hours', 'holiday_hours', 'bonus', 'commission', 'paycheck_tips', 'cash_tips', 'gross_earnings', 'reimbursement', 'personal_note'));
				$sheet->cells('A1:Q1', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(22);
								$cells->setAlignment('center');
							});
				$sheet->cells('A2:Q2', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#BDB76B');
								$cells->setAlignment('center');
							});
				

				
			});
		})->download('xlsx');
				
			}
		}else{
			echo "No Timesheet for serached Payperiod!";
		}
		
	}
	 public function ecsvpost_ddata($payperiod,$search_by_comp){
		 //dd($payperiod);
	   
		$search_by_comp = $search_by_comp;
		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			 if($search_by_comp == 9){
				$searchbycomparr = array($search_by_comp,"12");
				$user_companies = UserManager::whereIn('users_id', $searchbycomparr)->get();
			 } 
			 elseif($search_by_comp == 12){
				$searchbycomparr = array($search_by_comp,"9");
				$user_companies = UserManager::whereIn('users_id', $searchbycomparr)->get();
			 } 
			 else{
				 $user_companies = UserManager::where('users_id', '=',$search_by_comp)->get();
			}
			//$user_companies = UserManager::where('users_id', '=', $search_by_comp)->get();
		
			
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$users_arrr[] = $user_company->musers_id;
				}
			}
			
		}

		$users = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();

		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}
		$bet_dates = explode('-',$payperiod);
		if(isset($bet_dates)){
			$from_date    = $bet_dates[0];
			$to_date    = $bet_dates[1];
		}
		
		$xto_date = explode('_',$to_date);
		$xto_date = implode('-',$xto_date);
		$xfrom_date = explode('_',$from_date);
		$xfrom_date = implode('-',$xfrom_date);
		$xpto_date = date('Y-m-d', strtotime($xto_date. ' + 5 days'));
		
		$users_arrr = array_unique($users_arrr);
		if($from_date != "" && $to_date != ""){
			$data = TimeSheet::with('companies')
								->with('users')
								->whereIn('users_id', $users_arrr)
								->whereBetween('hours_day', array($from_date, $to_date))
								->distinct('users_id')
								->orderBy('created_at', 'DESC')
								->get();
		}else{
			$data = "";
		}
		
		$time_sheet_users =array();
		if(isset($data)){
			$time_sheet[] = array('last_name','first_name','ssn', 'company','title','regular_hours','gusto_employee_id','overtime_hours','double_overtime_hours','holiday_hours','bonus','commission','paycheck_tips','cash_tips','gross_earnings','reimbursement','personal_note' );
			$count = 1;
			
			foreach($data as $datas)
			{	
				// $hours_day = explode('_',$datas->hours_day);
						// $hours_day = implode('-',$hours_day);
				
				$time_sheet_users[] = $datas->users_id;
			}
			$time_sheet_users = array_unique($time_sheet_users);
			$user_pay = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();
	        //$user_company_name = $user_companies;
			$holidays  = Holiday::all();
			if($user_pay){
				
				foreach($user_pay as $user_pays)
				{	 
				$entry_date = date('m/d/y', strtotime($xto_date));
					$total_time = $this->ttotal_time($user_pays->id, $from_date, $to_date);
					
					$cxto_date = explode('_',$to_date);
                		$cxto_date = implode('-',$cxto_date);
                		$cxfrom_date = explode('_',$from_date);
                		$cxfrom_date = implode('-',$cxfrom_date);
						$holiday_dt = "";
						$holiday_dt_arr = array();
                		if(isset($holidays)){
                			foreach($holidays as $holiday){
                				$holiday = new DateTime($holiday->date);
                				$cto_date = new DateTime($cxto_date);
                				$cfrom_date  = new DateTime($cxfrom_date);
                				if (
                				  $holiday->format('y-m-d') >= $cfrom_date->format('y-m-d') && 
                				  $holiday->format('y-m-d') <= $cto_date->format('y-m-d')){
                				  //$holiday_dt = $holiday->format('Y/m/d');
								  $holiday_dt_arr[] = $holiday->format('Y-m-d');
                				}
							}
                			
                		}
						
						// echo "<pre>";
						// print_r($holiday_dt_arr);
							// echo $holiday_dt;
							$holiday_tm = 0; 
							$holiday_count = 1;
					if(isset($holiday_dt_arr)){
						foreach($holiday_dt_arr as $holiday_dt_ar){
								$holiday_dt_ar = explode('-',$holiday_dt_ar);
								$holiday_dt_ar = implode('_',$holiday_dt_ar);
								$holiday_time  = TimeSheet::where('users_id','=', $user_pays->id)
										->where('approve', '=', 2)
										->where('hours_day','=', $holiday_dt_ar)
										->orderBy('created_at', 'DESC')
										->first();
										
										if(isset($holiday_time)){
											// echo $holiday_time->hours_wrk."|";
											$holiday_tm = $holiday_time->hours_wrk + $holiday_tm;
										}
						}
					}
                		
                		
                		if(isset($holiday_tm) && $holiday_tm > 0){
                		    $t_time = $total_time - $holiday_tm;
                		    $holiday_time = $holiday_tm;
                		}else{
                		     $t_time = $total_time;
                		    $holiday_time = 0;
                		}
                		
					$user_companies = $this->exp_user_companies($user_pays->id);
					if(isset($total_time) && $total_time > 0){
							$time_sheet[] = array(
							// 'Entry Date' => $entry_date,
							// 'Emp ID' => $user_pays->emp_id,
							'last_name'  => $user_pays->last_name,
							'first_name'  => $user_pays->first_name,
							'ssn'  => $user_pays->ssn_no,
							'company'=> $user_companies,
							'title'  => "Residential Counselor ",
							// 'Payroll Code' => "01",
							'regular_hours'   => $t_time,
							'gusto_employee_id'   => '',
							'overtime_hours'       => $holiday_tm,
							'double_overtime_hours' =>  '',
							'holiday_hours'        => '',
							'bonus'                => '',
							'commission'           => '',
							'paycheck_tips'        => '',
							'cash_tips'            => '',
							'gross_earnings'       => '',
							'reimbursement'        => '',
							'personal_note'        => $user_companies,
						);
					}
				}
				$report_name = 'Payroll'.$payperiod;
				Excel::create($report_name, function($excel) use ($time_sheet,$report_name){
						$excel->setTitle($report_name);
						$excel->sheet($report_name, function($sheet) use ($time_sheet){
						$sheet->fromArray($time_sheet, null, 'A1', false, false);
					});
				})->download('csv');
				
			}
		}else{
			echo "No Timesheet for serached Payperiod!";
		}
		
	}
	
	
	
	public function post_ddata($payperiod,$search_by_comp){
	  /* $holidays = array(  '05/30/2022',
							'07/04/2022',
							'09/06/2022',
							'11/25/2022',
							'12/25/2022',
							);*/
		$search_by_comp = $search_by_comp;
		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			 if($search_by_comp == 9){
				$searchbycomparr = array($search_by_comp,"12");
				$user_companies = UserManager::whereIn('users_id', $searchbycomparr)->get();
			 } 
			 elseif($search_by_comp == 12){
				$searchbycomparr = array($search_by_comp,"9");
				$user_companies = UserManager::whereIn('users_id', $searchbycomparr)->get();
			 } 
			 else{
				 $user_companies = UserManager::where('users_id', '=',$search_by_comp)->get();
			}
			//$user_companies = UserManager::where('users_id', '=', $search_by_comp)->get();
		
			
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$users_arrr[] = $user_company->musers_id;
				}
			}
			
		}

		$users = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();

		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}
		$bet_dates = explode('-',$payperiod);
		if(isset($bet_dates)){
			$from_date    = $bet_dates[0];
			$to_date    = $bet_dates[1];
		}
		
		$xto_date = explode('_',$to_date);
		$xto_date = implode('-',$xto_date);
		$xfrom_date = explode('_',$from_date);
		$xfrom_date = implode('-',$xfrom_date);
		$xpto_date = date('Y-m-d', strtotime($xto_date. ' + 5 days'));
		
		$users_arrr = array_unique($users_arrr);
		if($from_date != "" && $to_date != ""){
			$data = TimeSheet::with('companies')
								->with('users')
								->whereIn('users_id', $users_arrr)
								->whereBetween('hours_day', array($from_date, $to_date))
								->distinct('users_id')
								->orderBy('created_at', 'DESC')
								->get();
		}else{
			$data = "";
		}
		
		$time_sheet_users =array();
		if(isset($data)){
			$time_sheet[] = array('last_name','first_name','ssn', 'company','title','regular_hours','gusto_employee_id','overtime_hours','double_overtime_hours','holiday_hours','bonus','commission','paycheck_tips','cash_tips','gross_earnings','reimbursement','personal_note' );
			$count = 1;
			
			foreach($data as $datas)
			{	
				// $hours_day = explode('_',$datas->hours_day);
						// $hours_day = implode('-',$hours_day);
				
				$time_sheet_users[] = $datas->users_id;
			}
			$time_sheet_users = array_unique($time_sheet_users);
			$user_pay = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();
	        //$user_company_name = $user_companies;
			$holidays  = Holiday::all();
			if($user_pay){
				
				foreach($user_pay as $user_pays)
				{	 
				$entry_date = date('m/d/y', strtotime($xto_date));
					$total_time = $this->ttotal_time($user_pays->id, $from_date, $to_date);
					
					$cxto_date = explode('_',$to_date);
                		$cxto_date = implode('-',$cxto_date);
                		$cxfrom_date = explode('_',$from_date);
                		$cxfrom_date = implode('-',$cxfrom_date);
						$holiday_dt = "";
						$holiday_dt_arr = array();
                		if(isset($holidays)){
                			foreach($holidays as $holiday){
                				$holiday = new DateTime($holiday->date);
                				$cto_date = new DateTime($cxto_date);
                				$cfrom_date  = new DateTime($cxfrom_date);
                				if (
                				  $holiday->format('y-m-d') >= $cfrom_date->format('y-m-d') && 
                				  $holiday->format('y-m-d') <= $cto_date->format('y-m-d')){
                				  //$holiday_dt = $holiday->format('Y/m/d');
								  $holiday_dt_arr[] = $holiday->format('Y-m-d');
                				}
							}
                			
                		}
						
						// echo "<pre>";
						// print_r($holiday_dt_arr);
							// echo $holiday_dt;
							$holiday_tm = 0; 
							$holiday_count = 1;
					if(isset($holiday_dt_arr)){
						foreach($holiday_dt_arr as $holiday_dt_ar){
								$holiday_dt_ar = explode('-',$holiday_dt_ar);
								$holiday_dt_ar = implode('_',$holiday_dt_ar);
								$holiday_time  = TimeSheet::where('users_id','=', $user_pays->id)
										->where('approve', '=', 2)
										->where('hours_day','=', $holiday_dt_ar)
										->orderBy('created_at', 'DESC')
										->first();
										
										if(isset($holiday_time)){
											// echo $holiday_time->hours_wrk."|";
											$holiday_tm = $holiday_time->hours_wrk + $holiday_tm;
										}
						}
					}
                		
                		
                		if(isset($holiday_tm) && $holiday_tm > 0){
                		    $t_time = $total_time - $holiday_tm;
                		    $holiday_time = $holiday_tm;
                		}else{
                		     $t_time = $total_time;
                		    $holiday_time = 0;
                		}
                		
					$user_companies = $this->exp_user_companies($user_pays->id);
					if(isset($total_time) && $total_time > 0){
							$time_sheet[] = array(
							// 'Entry Date' => $entry_date,
							// 'Emp ID' => $user_pays->emp_id,
							'last_name'  => $user_pays->last_name,
							'first_name'  => $user_pays->first_name,
							'ssn'  => $user_pays->ssn_no,
							'company'=> $user_companies,
							'title'  => "Residential Counselor ",
							// 'Payroll Code' => "01",
							'regular_hours'   => $t_time,
							'gusto_employee_id'   => '',
							'overtime_hours'       => $holiday_tm,
							'double_overtime_hours' =>  '',
							'holiday_hours'        => '',
							'bonus'                => '',
							'commission'           => '',
							'paycheck_tips'        => '',
							'cash_tips'            => '',
							'gross_earnings'       => '',
							'reimbursement'        => '',
							'personal_note'        => $user_companies,
						);
					}
				}
				$report_name = 'P_R_'.$payperiod;
				Excel::create($report_name, function($excel) use ($time_sheet,$report_name){
						$excel->setTitle($report_name);
						$excel->sheet($report_name, function($sheet) use ($time_sheet){
						$sheet->fromArray($time_sheet, null, 'A1', false, false);
					});
				})->download('csv');
				
			}
		}else{
			echo "No Timesheet for serached Payperiod!";
		}
		
	}
	
	
    public function hpost_ddata($payperiod,$search_by_comp){
	    // $holidays = array( '05/30/2022',
		// 					'07/04/2022',
		// 					'09/06/2022',
		// 					'11/25/2022',
		// 					'12/25/2022',
		// 					);
		$search_by_comp = $search_by_comp;
		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			$user_companies = UserManager::where('users_id', '=', $search_by_comp)->get();
		
			
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$users_arrr[] = $user_company->musers_id;
				}
			}
			
		}

		$users = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();

		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}
		$bet_dates = explode('-',$payperiod);
		if(isset($bet_dates)){
			$from_date    = $bet_dates[0];
			$to_date    = $bet_dates[1];
		}
		
		$xto_date = explode('_',$to_date);
		$xto_date = implode('-',$xto_date);
		$xfrom_date = explode('_',$from_date);
		$xfrom_date = implode('-',$xfrom_date);
		$xpto_date = date('Y-m-d', strtotime($xto_date. ' + 5 days'));
		
		$users_arrr = array_unique($users_arrr);
		if($from_date != "" && $to_date != ""){
			$data = TimeSheet::with('companies')
								->with('users')
								->whereIn('users_id', $users_arrr)
								->whereBetween('hours_day', array($from_date, $to_date))
								->distinct('users_id')
								->orderBy('created_at', 'DESC')
								->get();
		}else{
			$data = "";
		}
		
		$time_sheet_users =array();
		if(isset($data)){
			$holiday_time_sheet[] = array('last_name','first_name','ssn','title','gusto_employee_id','regular_hours','overtime_hours','double_overtime_hours','holiday_hours','bonus','commission','paycheck_tips','cash_tips','gross_earnings','reimbursement','personal_note');
			$count = 1;
			
			foreach($data as $datas)
			{	
				// $hours_day = explode('_',$datas->hours_day);
						// $hours_day = implode('-',$hours_day);
				
				$time_sheet_users[] = $datas->users_id;
			}
			$time_sheet_users = array_unique($time_sheet_users);
			$user_pay = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();
	$holidays  = Holiday::all();
			if($user_pay){
				
				foreach($user_pay as $user_pays)
				{	 
				$entry_date = date('m/d/y', strtotime($xto_date));
					$total_time = $this->ttotal_time($user_pays->id, $from_date, $to_date);
					
					$cxto_date = explode('_',$to_date);
                		$cxto_date = implode('-',$cxto_date);
                		$cxfrom_date = explode('_',$from_date);
                		$cxfrom_date = implode('-',$cxfrom_date);
						$holiday_dt = "";
						$holiday_dt_arr = array();
                		if(isset($holidays)){
                			foreach($holidays as $holiday){
                				$holiday = new DateTime($holiday->date);
                				$cto_date = new DateTime($cxto_date);
                				$cfrom_date  = new DateTime($cxfrom_date);
                				if (
                				  $holiday->format('y-m-d') >= $cfrom_date->format('y-m-d') && 
                				  $holiday->format('y-m-d') <= $cto_date->format('y-m-d')){
                				  //$holiday_dt = $holiday->format('Y/m/d');
								  $holiday_dt_arr[] = $holiday->format('Y-m-d');
                				}
							}
                			
                		}
						
						// echo "<pre>";
						// print_r($holiday_dt_arr);
							// echo $holiday_dt;
							$holiday_tm = 0; 
							$holiday_count = 1;
					if(isset($holiday_dt_arr)){
						foreach($holiday_dt_arr as $holiday_dt_ar){
								$holiday_dt_ar = explode('-',$holiday_dt_ar);
								$holiday_dt_ar = implode('_',$holiday_dt_ar);
								$holiday_time  = TimeSheet::where('users_id','=', $user_pays->id)
										->where('approve', '=', 2)
										->where('hours_day','=', $holiday_dt_ar)
										->orderBy('created_at', 'DESC')
										->first();
										
										if(isset($holiday_time)){
											// echo $holiday_time->hours_wrk."|";
											$holiday_tm = $holiday_time->hours_wrk + $holiday_tm;
										}
						}
					}
                		
                		if(isset($holiday_tm) && $holiday_tm > 0){
                		    $total_time = $total_time - $holiday_tm;
                		    $holiday_time = $holiday_tm;
                		}else{
                		     $total_time = $total_time;
                		    $holiday_time = 0;
                		}
						
						
                		$t_time = $total_time - $holiday_tm;
					$user_companies = $this->exp_user_companies($user_pays->id);
					if(isset($holiday_time) && $holiday_time > 0){
						$holiday_time_sheet[] = array(
							// 'Entry Date' => $entry_date,
							// 'Emp ID' => $user_pays->emp_id,
							'last_name'           => $user_pays->last_name,
							'first_name'          => $user_pays->first_name,
							'ssn'                 => $user_pays->ssn_no,
							'title'               => 'Residential Counselor',
							'gusto_employee_id'   => '',
							'regular_hours'       => $total_time,
							'overtime_hours'       => $holiday_tm,
							'double_overtime_hours' =>  '',
							'holiday_hours'        => '',
							'bonus'                => '',
							'commission'           => '',
							'paycheck_tips'        => '',
							'cash_tips'            => '',
							'gross_earnings'       => '',
							'reimbursement'        => '',
							'personal_note'        =>  $user_companies,
						);
						
					}
				}
				$report_name = 'h_P_R'.$payperiod;
				if(isset($holiday_time_sheet)){
					Excel::create($report_name, function($excel) use ($holiday_time_sheet,$report_name){
						$excel->setTitle($report_name);
							$excel->sheet($report_name, function($sheet) use ($holiday_time_sheet){
							$sheet->fromArray($holiday_time_sheet, null, 'A1', false, false);
						});
					})->download('csv');
				}else{
					echo "No Timesheet for serached Payperiod!";
				}
				
			}
		}else{
			echo "No Timesheet for serached Payperiod!";
		}
		
	}
	
	public function all_applicants_view(){
		$users = User::where('role', '=', "user")->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		return view('admin.reports.all_applicant_view',compact('users'));
	}
	
	//function for all users
	public function all_applicants(){
		
		$user = User::where('role', '=', "user")->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		$users_report[] = array('#','Name', 'Company', 'Emp ID','Department');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){
				$user_companies = $this->user_companies($userss->id);
				$users_report[] = array(
						'#' => $user_count,
						'Name'  => $userss->name,
						'Company'   =>  $user_companies,
						'Emp ID' => $userss->emp_id,						
						'Department'   => $userss->dept,
					);		
					$user_count++;
			}
		}
		Excel::create('All Applicants', function($excel) use ($users_report){
			$excel->setTitle('All Applicants');
			$excel->sheet('All Applicants', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
	public function all_new_applicants_view(){
		$users = User::where('role', '=', "user")->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		return view('admin.reports.all_new_applicant_view',compact('users'));
	}
	
	public function search_all_new_applicants(Request $request){
		$aap_month = $request->aap_month;
		$aap_year  = $request->aap_year;
		
		$users = User::where('role', '=', "user")->whereMonth('created_at', date($aap_month))->whereYear('created_at', date($aap_year))->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		$count = 1;
		// echo "<pre>";
		// print_r($users);
		// die;
		if(isset($users)){
				foreach($users as $user){
					
					echo '<tr>';
					  echo '<td>'.$count.'</td>';
					  echo '<td>'.$user->name.'</td>';
					  echo '<td>';
					  $user_companies = $this->user_companies($user->id);
					  echo '<ul class="comp_list">'.$user_companies.'</ul>';
					  echo '</td>';
					  echo '<td>'.$user->emp_id.'</td>';
					  echo '<td>'.$user->dept.'</td>';
					echo '</tr>';
					
					$count++; 
				}
		}
		
	}
	
	//function for all users
	public function all_new_applicants(){
		
		$user = User::where('role', '=', "user")->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		$users_report[] = array('#','Name', 'Company', 'Emp ID','Department');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){
				$user_companies = $this->user_companies($userss->id);
				$users_report[] = array(
						'#' => $user_count,
						'Name'  => $userss->name,
						'Company'   =>  $user_companies,
						'Emp ID' => $userss->emp_id,						
						'Department'   => $userss->dept,
					);		
					$user_count++;
			}
		}
		Excel::create('Current Applicants', function($excel) use ($users_report){
			$excel->setTitle('Current Applicants');
			$excel->sheet('Current Applicants', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
	public function all_new_applicants_by_month($aap_month,$aap_year){
		
		$aap_month = $aap_month;
		$aap_year  = $aap_year;

		$user = User::where('role', '=', "user")->whereMonth('created_at', date($aap_month))->whereYear('created_at', date($aap_year))->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		$users_report[] = array('#','Name', 'Company', 'Emp ID','Department');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){
				$user_companies = $this->user_companies($userss->id);
				$users_report[] = array(
						'#' => $user_count,
						'Name'  => $userss->name,
						'Company'   =>  $user_companies,
						'Emp ID' => $userss->emp_id,						
						'Department'   => $userss->dept,
					);		
					$user_count++;
			}
		}
		Excel::create('Current Applicants', function($excel) use ($users_report){
			$excel->setTitle('Current Applicants');
			$excel->sheet('Current Applicants', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
	
	public function all_applicants_without_id_view(){
		$users = User::where('role', '=', "user")->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		return view('admin.reports.all_user_without_id',compact('users'));
	}
	
	//function for all users
	public function all_applicants_without_id(){
		
		$user = User::where('role', '=', "user")->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		$users_report[] = array('#','Name','First Name','Last Name','Email', 'Company','Department');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){
				$user_companies = $this->user_companies($userss->id);
				$users_report[] = array(
						'#' => $user_count,
						'Name'  => $userss->name,
						'First Name'  => $userss->first_name,
						'Last Name'  => $userss->last_name,
						'Email'  => $userss->email,
						'Company'   =>  $user_companies,						
						'Department'   => $userss->dept,
					);		
					$user_count++;
			}
		}
		Excel::create('All Applicants Without ID', function($excel) use ($users_report){
			$excel->setTitle('All Applicants Without ID');
			$excel->sheet('All Applicants Without ID', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
	public function all_user_lst_login_logout_view(){
		$users = User::where('role', '=', "user")->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		return view('admin.reports.user_last_login_logout',compact('users'));
	}
	
	//function for all users
	public function all_user_lst_login_logout(){
		
		$user = User::where('role', '=', "user")->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		$users_report[] = array('#','Name','Last Login Date','Last Login Time');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){
				$user_companies = $this->user_companies($userss->id);
				
				if($userss->last_login_at != null){
					$last_login_date =  date('M d, Y', strtotime($userss->last_login_at));		
					$last_login_time =  date('h:i a', strtotime($userss->last_login_at));	
				}else{
					$last_login_date = "";
					$last_login_time =  "";
				}
				
				$users_report[] = array(
						'#' => $user_count,
						'Name'  => $userss->name,
						'Last Login Date'  => $last_login_date,
						'Last Login Time'  => $last_login_time,
					);		
					$user_count++;
			}
		}
		Excel::create('Applicants Last Login', function($excel) use ($users_report){
			$excel->setTitle('Applicants Last Login');
			$excel->sheet('Applicants Last Login', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
	public function inactive_employees_view(){
		$users = User::where('role', '=', "user")->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		return view('admin.reports.inactive_employees_view',compact('users'));
	}
	
	
	public function inactive_employees_search(Request $request){
		$from_month = $request->from_month;
		$to_month  = $request->to_month;
		
		
		$users = User::where('role', '=', "user")->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		$count = 1;

		if(isset($users)){
				foreach($users as $user){
					echo '<tr>';
					  echo '<td>'.$count.'</td>';
					  $user_companies = $this->user_companies($user->id);
					  echo '<td>'.$user->name.' <b>('.$user_companies.')</b></td>';
					  $user_last_clock = $this->user_last_clock_time($user->id,$from_month,$to_month);
					  echo '<td>';  
						if(isset($user_last_clock)) {
							if($user_last_clock->hours_day != null){
								$user_last_clock_day =  explode('_',$user_last_clock->hours_day);
								$user_last_clock_day =  implode('-',$user_last_clock_day);
								echo date('M d, Y', strtotime($user_last_clock_day));
							}
						} 
					  echo '</td>';
					  echo '<td>';  
						if(isset($user_last_clock)) {
							if($user_last_clock->time_in != null){
								echo $user_last_clock->time_in;
							}
						}
					  echo '</td>';
					  echo '<td>';  
						if(isset($user_last_clock)) {
							if($user_last_clock->time_out != null){
								echo $user_last_clock->time_out;
							}
						} 
					  echo '</td>';
					echo '</tr>';
					
					$count++; 
				}
		}
	}
	
	//function for all users
	public function inactive_employees(){
		
		$user = User::where('role', '=', "user")->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		$users_report[] = array('#','Name','Last Clock In Date','Last Clock In Time','Last Clock Out Time');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){
				$user_companies = $this->user_companies($userss->id);
				$user_last_clock = $this->user_last_clock($userss->id);
				if(isset($user_last_clock)){
					if($user_last_clock->hours_day != null){
						$user_last_clock_day =  explode('_',$user_last_clock->hours_day);
						$user_last_clock_day =  implode('-',$user_last_clock_day);
						$user_last_clock_day = date('M d, Y', strtotime($user_last_clock_day));
					}else{
						$user_last_clock_day = "";
					}
					if($user_last_clock->time_in != null){
						$user_last_clock_in =  $user_last_clock->time_in;
					}else{
						$user_last_clock_in = "";
					}
					if($user_last_clock->time_out != null){
						$user_last_clock_out = $user_last_clock->time_out;
					}else{
						$user_last_clock_out = "";
					}
							
				}else{
					$user_last_clock_day = "";
					$user_last_clock_in = "";
					$user_last_clock_out =  "";
				}
				
				$users_report[] = array(
						'#' => $user_count,
						'Name'  => $userss->name.'('.$user_companies.')',
						'Last Clock In Date'  => $user_last_clock_day,
						'Last Clock In Time'  => $user_last_clock_in,
						'Last Clock Out Time'  => $user_last_clock_out,
					);		
					$user_count++;
			}
		}
		Excel::create('Inactive Employees', function($excel) use ($users_report){
			$excel->setTitle('Inactive Employees');
			$excel->sheet('Inactive Employees', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
		//function for all users
	public function inactive_employees_by_month($from_date,$to_date){
		
		$user = User::where('role', '=', "user")->whereNotIn('id', [276])->orderBy('name', 'ASC')->get();
		$users_report[] = array('#','Name','Last Clock In Date','Last Clock In Time','Last Clock Out Time');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){
				$user_companies = $this->user_companies($userss->id);
				$user_last_clock = $this->user_last_clock_time($userss->id,$from_date,$to_date);
				if(isset($user_last_clock)){
					if($user_last_clock->hours_day != null){
						$user_last_clock_day =  explode('_',$user_last_clock->hours_day);
						$user_last_clock_day =  implode('-',$user_last_clock_day);
						$user_last_clock_day = date('M d, Y', strtotime($user_last_clock_day));
					}else{
						$user_last_clock_day = "";
					}
					if($user_last_clock->time_in != null){
						$user_last_clock_in =  $user_last_clock->time_in;
					}else{
						$user_last_clock_in = "";
					}
					if($user_last_clock->time_out != null){
						$user_last_clock_out = $user_last_clock->time_out;
					}else{
						$user_last_clock_out = "";
					}
							
				}else{
					$user_last_clock_day = "";
					$user_last_clock_in = "";
					$user_last_clock_out =  "";
				}
				
				$users_report[] = array(
						'#' => $user_count,
						'Name'  => $userss->name.'('.$user_companies.')',
						'Last Clock In Date'  => $user_last_clock_day,
						'Last Clock In Time'  => $user_last_clock_in,
						'Last Clock Out Time'  => $user_last_clock_out,
					);		
					$user_count++;
			}
		}
		Excel::create('Inactive Employees', function($excel) use ($users_report){
			$excel->setTitle('Inactive Employees');
			$excel->sheet('Inactive Employees', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
		
	}
	
	public function test_route(){
		$clean = User::get();
		Excel::create('Report', function($excel) use($clean){
        $excel->sheet('Sheet 1', function($sheet) use($clean){
            $sheet->row(1, array('Room Attendant Name', 'Room Number', 'Verified'));

            $i = 2;

            foreach ($clean as $cleans) {
					$sheet->row($i, function($color) {
						$color->setBackground('#008000');
					});

					$sheet->row($i, array($cleans->name, $cleans->first_name, $cleans->id));
					$i++;
				}

				$sheet->setAutoFilter();
			});
		})->download('xlsx');
		
		
		
	}
	
	
	public static function user_info( $id ){
		$user = User::where('id', '=', $id)->first();
		return $user;
	}
	
	
	public static function timesheet_data( $id ){
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('approve', '=', 2)->where('users_id', '=', $id)->orderBy('hours_day', 'DESC')->get();
		return $data;
	}
	
	
	public static function UserManager( $id ){
		$UserManager = UserManager::where('musers_id', '=', $id)->first();
		$company_id = $UserManager->users_id;
		$company = Company::where('id', '=', $company_id)->first();
		$user = User::where('role', '=', "user")->where('companies_id', '=', $company->company)->orderBy('name', 'DESC')->get();
		return $user;
	}
	
	public static function user_companies($id)
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
	
	public static function user_last_clock($id)
    {
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $id)->orderBy('created_at', 'DESC')->first();
		
		return $data;
	}
	
	
	public static function user_last_clock_time($id,$from_month,$to_month){
		$startDate = Carbon::createFromFormat('Y-m-d', $from_month);
        $endDate = Carbon::createFromFormat('Y-m-d',$to_month);
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'DESC')->first();
		
		return $data;
	} 
	
	public static function ttotal_time($id, $from_date, $to_date)
    {
        // $total_time = TimeSheet::where('users_id', '=', $id)->sum('hours_wrk');
		if($from_date == $to_date){
			$total_time = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('approve', '=', 2)
								->where('hours_day', $from_date)
								->where('users_id', '=', $id)
								->sum('hours_wrk');
		}else{
			$total_time = TimeSheet::with('companies')
								->whereBetween('hours_day', array($from_date, $to_date))
								->where('approve', '=', 2)
								->where('users_id', '=', $id)
								->sum('hours_wrk');
		}
		return $total_time;
    }
	public function user_searchs(Request $request)
    {
//dd($request->all());die();
		$searchTerm = $request->srch_users;
		$data = User::where('role', 'user')
						->where('name', 'LIKE', "%{$searchTerm}%") 
						->orWhere('email', 'LIKE', "%{$searchTerm}%")
						->orWhere('emp_id', '=', $searchTerm)
						->orderBy('name', 'ASC')
						->get();
								//dd($data);die();
			if(isset($data)){
$count = 1; 
			  if($data->count() != 0){
				foreach ($data as $datas){
					$approved_by = $this->approved_by($datas->id); 
					$color_info =  $this->color_info($datas->id); 
					
					
					if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					}
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
				 $user_companies = $this->user_companies($datas->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
				  echo "<td>".$datas->hourst_rate."</td>";
			   echo "<td>";
			  if($datas->last_login_at != null) { echo date('h:i a', strtotime($datas->last_login_at)) ; }
			  echo "</td>";
				  echo "<td>";
				  if($datas->last_login_at != null) { echo date('M d, Y', strtotime($datas->last_login_at));  }
				  echo "</td>";
					  echo "<td>".date('M d, Y', strtotime($datas->created_at))."</td>";
					  
					  echo "<td>";
	$total_hours = $this->total_time($datas->id);
						if($total_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_hours."</p>";
						}elseif($total_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_hours."</p>";
						}
				  echo "</td>";
				   echo "<td>";
				   $approved_hours = $this->approved_time($datas->id);
						if($approved_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_hours."</p>";
						}elseif($approved_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_hours."</p>";
						}
				  echo "</td>";
				   echo "<td>";
				   $denied_hours = $this->denied_time($datas->id);
						if($denied_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_hours."</p>";
						}elseif($denied_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_hours."</p>";
						}
				  echo "</td>";

					 echo "</tr>";
				$count++; 
				}
				
			  }else{
					echo "<p>Sorry No Data!!</p>";
			  }
				
				
			}
    }

    public function staff_list(Request $request)
    {
    	
    	$company = Company::where('id', '=', $request->id)->first();
    	if(isset($company)){
    		$company_nm = $company->company;
    	}
    	$user = User::where('role', '=', "user")->where('companies_id', '=', $company_nm)->orderBy('name', 'ASC')->get();
		$users_report[] = array('ID','DOB','Name');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){

				
				$users_report[] = array(
						'ID' => $userss->emp_id,
						'DOB'  => $userss->dob,
						'Name'  => $userss->name
					);		
					$user_count++;
			}
		}
		Excel::create('Staff List', function($excel) use ($users_report){
			$excel->setTitle('Staff List');
			$excel->sheet('Staff List', function($sheet) use ($users_report){
				$sheet->fromArray($users_report, null, 'A1', false, false);
			});
		})->download('xlsx');
    }

    public function payroll_file(Request $request)
    {
		
		// print_r($request->all()); die();
		$bet_dates = explode('-',$request->payperiod);
		//print_r($bet_dates); die();
		$search_by_compa = $request->cid;
		$company = Company::where('id', '=', $request->cid)->first();
    	if(isset($company)){
    		$company_nm = $company->company;
    	}
		$users_arrr = array();
		if(isset($search_by_compa) && $search_by_compa != 0){
			$user_companies = UserManager::where('users_id', '=',$search_by_compa)->get();
			
			 /* if($search_by_comp == 9){
				$searchbycomparr = array($search_by_comp,"12");
				$user_companies = UserManager::whereIn('users_id', $searchbycomparr)->get();
			 } 
			 elseif($search_by_comp == 12){
				$searchbycomparr = array($search_by_comp,"9");
				$user_companies = UserManager::whereIn('users_id', $searchbycomparr)->get();
			 } 
			 else{
				 $user_companies = UserManager::where('users_id', '=',$search_by_comp)->get();
			}
			*/
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$users_arrr[] = $user_company->musers_id;
				}
			}
			
		}
		if(isset($bet_dates)){
			$from_date    = $bet_dates[0];
			$to_date    = $bet_dates[1];
		}
		
		$xto_date = explode('_',$to_date);
		$xxto_date = implode('/',$xto_date);
		$xto_date = implode('-',$xto_date);
		$xfrom_date = explode('_',$from_date);
		$xxfrom_date = implode('/',$xfrom_date);
		$xfrom_date = implode('-',$xfrom_date);
		$paydate = date('Y-m-d', strtotime($xto_date. ' + 5 days'));
		$paydatee = date('Y/m/d', strtotime($xto_date. ' + 5 days'));
		
		$users_arrr = array_unique($users_arrr);
		$users = User::with('companies')->whereIn('id', $users_arrr)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
		//$user_arr = array();
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
				$reg_hours = 0;
				$holidy_hours = 0;
				$approved_hours = 0;
				$denied_hours = 0;
				$htotal_pays=0;
				  if($data->count() != 0){
					foreach ($data as $datas){
						//$reg_hours = $datas->hours_wrk;
						$total_hours = $total_hours + $datas->hours_wrk;
						if($datas->approve == "2"){ 
								$approved_hours					  = $approved_hours + $datas->hours_wrk;
						}elseif($datas->approve == "1"){
							$denied_hours					  = $denied_hours + $datas->hours_wrk;
						}
						
					}
				  }
			  
			  
			  // Holiday Hours
			    $holidays  = Holiday::all();
				//$holidays = $holidays->date;
				//dd($holidays);
				$cxto_date = explode('_',$to_date);
				$cxto_date = implode('-',$cxto_date);
				$cxfrom_date = explode('_',$from_date);
				$cxfrom_date = implode('-',$cxfrom_date);
				$holiday_dt = "";
				$holiday_dt_arr = array();
				if(isset($holidays)){
					foreach($holidays as $holiday){
						//dd($holiday->date);
						$holiday = new DateTime($holiday->date);
						$cto_date = new DateTime($cxto_date);
						$cfrom_date  = new DateTime($cxfrom_date);
						if (
						  $holiday->format('y-m-d') >= $cfrom_date->format('y-m-d') && 
						  $holiday->format('y-m-d') <= $cto_date->format('y-m-d')){
						  //$holiday_dt = $holiday->format('Y/m/d');
						  $holiday_dt_arr[] = $holiday->format('Y-m-d');
						}
					}
					
				}
				$holiday_time = 0;
				$holiday_tm = 0; 
				$holiday_count = 1;
				$test =0;
				
				if(isset($holiday_dt_arr)){
						foreach($holiday_dt_arr as $holiday_dt_ar){
								$holiday_dt_ar = explode('-',$holiday_dt_ar);
								$holiday_dt_ar = implode('_',$holiday_dt_ar);
								$holiday_time  = TimeSheet::where('users_id','=', $userss->id)
										->where('approve', '=', 2)
										->where('hours_day','=', $holiday_dt_ar)
										->orderBy('created_at', 'DESC')
										->first();
										
										if(isset($holiday_time)){
											$holiday_tm = $holiday_time->hours_wrk + $holiday_tm;
										}
                         //  $test = $holiday_time->hours_wrk;
						}
					//	print_r($holiday_time);die();
						
					}
					
				if(isset($holiday_tm) && $holiday_tm > 0){
					$approved_hours = $approved_hours - $holiday_tm;
					$holiday_time = $holiday_tm;
				}else{
					$approved_hours = $approved_hours;
					$holiday_time = 0;
				}
				if(isset($userss->hourst_rate)){
					$hr_rate = $userss->hourst_rate;
				}else{
					$hr_rate = number_format("12.5",2);
				}
			  $total_pay = $approved_hours * $hr_rate;
			  
			  if($hr_rate > 0){
				  $billed_rate =$hr_rate + number_format("6",2);
			  }else{
				  $billed_rate = 0;
			  }
			  if($billed_rate > 0){
				 // $holiday_hourley =$billed_rate * number_format("1.5",2);
				 $holiday_hourley = $hr_rate * number_format("1.5",2);
			  }else{
				  $holiday_hourley = 0;
			  }
			  
			  $total_billed = $approved_hours * $billed_rate;
			  $profit = $total_billed-$total_pay;
		      $htotal_pay = $holiday_time * $holiday_hourley;
			  $Total_with_holiday = $htotal_pay + $total_billed;
			  $rate = $holiday_hourley + number_format("6",2);
			 // $rate = 0;
		     $htotal_pays=$holidy_hours * $holiday_hourley;
			 
			  $usersd = TimeSheet::where('users_id','=', $userss->id)->first();
			  
			  $holidy_hours = TimeSheet::where('users_id','=', $userss->id)
				   				   ->where('approve', '=', 2)
								   ->where('hours_day', $from_date)
								   ->whereIn('users_id', $user_arr)
								   ->sum('hours_wrk');
								
				$reg_hours = $approved_hours - $holiday_tm;				  
								 
				$dob = explode('_',$userss->dob);
				$dob = implode('/',$dob);

			  $time_sheet[] = array(

						'ID' => $userss->emp_id,
						'DOB' => $dob,
						'Name'   => $userss->name,
						'Regular Hours' => $reg_hours,
						'Over Time' => "-",
						'Total Hours'   => $approved_hours,
						'Rate'   => $hr_rate,
						'Gross'   => $total_pay,
						'Child Sup_Other' => "-",
						'Health' => '-',

						
						
					);			  	
				$user_count++;					
			}
		}


				
		$stitle1 = "For the Pay Period from ".$xxto_date." to ".$xxfrom_date;
		$stitle = "Pay Date ".$paydatee;
		$file_nam = "Export Payroll File for ".$company_nm;

		//$stitle1 = "Employee Details";
		Excel::create($file_nam, function($excel) use ($time_sheet,$file_nam,$stitle,$stitle1,$paydate,$xfrom_date,$xto_date){
			$excel->setTitle($file_nam);
			$excel->sheet($file_nam, function($sheet) use ($time_sheet,$stitle,$stitle1,$paydate,$xfrom_date,$xto_date){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
				
				$sheet->row(1, array('','',$stitle1, '','','','', '','', ''));
				$sheet->row(2, array('','',$stitle, '','','','', '','', ''));
				$sheet->row(3, array('','', '','','','','', '','', ''));
				$sheet->row(4, array('','', '','','','','', '','', ''));

				$sheet->row(5,array('ID','DOB','Name','Regular Hours','Over Time','Total Hours','Rate','Gross','Child Sup/Others','Health'));
				$sheet->cells('A1:O1', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Arial');
								$cells->setFontSize(14);
								$cells->setAlignment('left');
							});
				$sheet->cells('A1:O2', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Arial');
								$cells->setFontSize(14);
								$cells->setAlignment('left');
							});
				$sheet->cells('A1:O5', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Arial');
								$cells->setFontSize(14);
								$cells->setFontWeight("bold");
								$cells->setAlignment('left');
							});

				$i = 6;
				$total_hours = 0;
				$Rate = 0;
				$Gross = 0;
				$ChildSup_Other = 0;
				$Health = 0;

				foreach ($time_sheet as $cleans) {

					$sheet->row($i, array($cleans['ID'], $cleans['DOB'], $cleans['Name'],$cleans['Regular Hours'], $cleans['Over Time'], $cleans['Total Hours'],  $cleans['Rate'], $cleans['Gross'],$cleans['Child Sup_Other'],$cleans['Health']));
					$sheet->cell('A'.$i.':O'.$i, function($cell) {
							$cell->setFontColor('#000000');
								$cell->setFontFamily('Arial');
								$cell->setFontSize(12);
								$cell->setFontWeight("bold");
								$cell->setAlignment('right');
						});

					$total_hours += (float)$cleans['Total Hours'];
					$Rate += (float)$cleans['Rate'];
					$Gross += (float)$cleans['Gross'];
					$ChildSup_Other  += (float)$cleans['Child Sup_Other'];
					$Health += (float)$cleans['Health'];
						$i++;
				}

				$row2 = $i+1;	
				$sheet->row($row2, array('','-','TOTALS', '','',$total_hours,$Rate,$Gross,$ChildSup_Other,$Health));
				$sheet->cell('A'.$row2.':O'.$row2, function($cell) {
							$cell->setFontColor('#000000');
								$cell->setFontFamily('Arial');
								$cell->setFontSize(12);
								$cell->setFontWeight("bold");
								$cell->setAlignment('right');
						});
				$row1 = $i+2;	
				$un = $i-6;
				$sheet->row($row1, array('','', '',$un,'','','', '', '',''));
				$sheet->cell('A'.$row1.':O'.$row1, function($cell) {
							$cell->setFontColor('#000000');
								$cell->setFontFamily('Arial');
								$cell->setFontSize(12);
								$cell->setFontWeight("bold");
								$cell->setAlignment('right');
						});
													
                
			}); 
		})->download('xlsx');

    }
	
	
}
