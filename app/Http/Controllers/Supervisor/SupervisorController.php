<?php

namespace App\Http\Controllers\Supervisor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\TimeSheet;
use App\Models\User;
use App\Models\Company;
use App\Models\House;
use Excel;
use Carbon\Carbon;
use App\Models\UserManager;
use App\Models\LoginLogouttime;
use App\Models\Payperiods;
use App\Models\UserVaccatioStatusn;
use DateTime;
use App\Models\UserVaccation;

class SupervisorController extends Controller
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
		$user = Auth::user()->id;
		$companies = UserManager::where('musers_id', '=', $user)->get();
		$company_id = array();
		
		if(isset($companies)){
			foreach($companies as $company){
				$companies = Company::where('id', '=', $company->users_id)->first();
				$company_id[] = $companies->company;
			}
		}
		$users = User::with('companies')->whereIn('companies_id', $company_id)->where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$user_arr = array();
		$user_count = 1;
		if(isset($users)){
			foreach($users as $userss){
				$user_arr[] = $userss->id;
				$user_count++;
			}
		}
		$dt = Carbon::now();
		$current_date_time = $dt->toDateString();
		$date    = explode('-', $current_date_time);
		$date = implode("_", $date);
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('hours_day', '=', $date)->whereIn('users_id', $user_arr)->orderBy('created_at', 'DESC')->get();
        return view('supervisor.dashboard',compact('data','current_date_time','user_count'));
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
		return view('supervisor.profile');
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
			$file->move(public_path('assets/uploads/supervisors'), $filename);
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
		return view('supervisor.change_pass');
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
    	$user = Auth::user()->id;
		$companies = UserManager::where('musers_id', '=', $user)->get();
		$company_id = array();
		
		if(isset($companies)){
			foreach($companies as $company){
				$companies = Company::where('id', '=', $company->users_id)->first();
				$company_id[] = $companies->company;
			}
		}
		$users = User::with('companies')->whereIn('companies_id', $company_id)->where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$user_arr = array();
		$user_count = 1;
		if(isset($users)){
			foreach($users as $userss){
				$user_arr[] = $userss->id;
				$user_count++;
			}
		}
		$approve_vchour = UserVaccatioStatusn::whereIn('user_id', $user_arr)->orderBy('created_at', 'DESC')->paginate(10);
		// $user = User::where("role", "=", "user")->orderBy('created_at', 'DESC')->get();
        return view('supervisor.approve_vchour',compact('approve_vchour'));
    }
    
	//Function for voccation view
	public function vaccation_view(Request $request) {
		//Get user vaccation
		$data = UserVaccatioStatusn::find($request->id);
		return view('supervisor.vaccation-view', compact('data'));
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
					);
					$form_dat1 = array(
						'vacc_status' => 1,
						// 'vacc_aprby' => $app_id,
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
					
					// $form_data = array(
					// 	'vacc_sl' => $avail_hours,
					// 	'vacc_vc' => $used_hours,
					// );
					$form_dat1 = array(
						'vacc_status' => 2,
						'vacc_aprby' => $app_id,
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


	public function all_users_view(){
		
		$users_arrr = array();
		$company_id = array();
		$user_id = Auth::user()->id;
		$companies = UserManager::where('musers_id', '=', $user_id)->get();
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
		$users = User::where('role', '=', "user")->whereIn('id', $users_arrr)->orderBy('created_at', 'DESC')->paginate(10);
		return view('supervisor.reports.all_users_view',compact('users'));
	}
	

	public function timesheet(){
		$users_arrr = array();
		$company_id = array();
		$user_id = Auth::user()->id;
		$companies = UserManager::where('musers_id', '=', $user_id)->get();
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
		$users = User::where('role', '=', "user")->whereIn('id', $users_arrr)->orderBy('created_at', 'DESC')->get();
		return view('supervisor.users.timesheet',compact('users'));	
	}
	//function for all users
	public function all_users(){
		
		$users_arrr = array();
		$company_id = array();
		$user_id = Auth::user()->id;
		$companies = UserManager::where('musers_id', '=', $user_id)->get();
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
		$user = User::where('role', '=', "user")->whereIn('id', $users_arrr)->orderBy('created_at', 'DESC')->get();
		$users_report[] = array('#','Emp ID', 'Last Name','First Name','Name', 'Email','Department', 'Company', 'Hourley Rate($)');
		$user_count = 1;
		if(isset($user)){
			foreach($user as $userss){
				$user_companies = $this->export_user_companies($userss->id);
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
	
	//Function for sign or signout
	public function sign_signout_view() {
		//Get auth login
		$user_id = Auth::id();
        //Get user mannager
		$company_id = UserManager::where('musers_id', $user_id)->pluck('users_id');
        //Get company
		$users_arrr = UserManager::whereIn('users_id', $company_id)->pluck('musers_id');
        //Get loginlogouttime
		$LoginLogouttime = LoginLogouttime::with('user')
			->whereIn('users_id', $users_arrr)
			->whereHas('user', function ($q) {
				$q->where('role', 'user');
			})
			->latest()
			->paginate(10);

		return view('supervisor.reports.sign_signout_view', compact('LoginLogouttime'));
	}
	
	//All user Log out/Log In
	public function sign_signout(){
		$users_arrr = array();
		$company_id = array();
		$user_id = Auth::user()->id;
		$companies = UserManager::where('musers_id', '=', $user_id)->get();
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
		$LoginLogouttime = LoginLogouttime::whereIn('users_id', $users_arrr)->orderBy('created_at', 'DESC')->get();
		
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
				if(isset($user) && $user->role == 'user'){
				$users_report[] = array(
						'#' => $user_count,
						'Emp ID' => $user->emp_id,
						'Last Name'   => $user->last_name,
						'First Name'   => $user->first_name,
						'Name'  => $user->name,
						'Type'  => $user->role,
						'Status'  => $status,
						'Date'  => $date,
						'Time'  => $time,
					);		
				}
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
	
	public function search_by_payperiod(){
		$payperiods_dates = Payperiods::orderBy('created_at', 'DESC')->get();
		return view('supervisor.reports.search_by_pay',compact('payperiods_dates'));
	}
	
	public function post_data(Request $request){

		$users_arrr = array();
		$company_id = array();
		$user_id = Auth::user()->id;
		$companies = UserManager::where('musers_id', '=', $user_id)->get();
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
								->whereIn('users_id', $users_arrr)
								->whereBetween('hours_day', array($xfrom_date, $xto_date))
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
	
	//Function for payroll report
	public static function payrollreport() {
		//Get users
        $data = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->paginate(15);
		//Get companies
		$companies = Company::orderBy('display_order', 'ASC')->get();
		//Get payperiods
		$payperiods_dates = Payperiods::orderBy('created_at', 'DESC')->get();

		return view('supervisor.reports.payrollreport',compact('data','companies','payperiods_dates'));
    }
	
	public function serach_payroll(Request $request){
		$search_by_comp = $request->search_by_comp;
		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0) {
			$user_companies = UserManager::where('users_id', '=', $search_by_comp)->get();
		
			if(isset($user_companies)) {
				foreach($user_companies as $user_company) {
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
	if($data->count() > 0){
			
			$count = 1;
			foreach($data as $datas)
			{	
						$hours_day = explode('_',$datas->hours_day);
						$hours_day = implode('-',$hours_day);
					
				$time_sheet_users[] = $datas->users_id;
			}
			$time_sheet_users = array_unique($time_sheet_users);
			$user_pay = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();
	
			if($user_pay){
				foreach($user_pay as $user_pays)
				{	 
					$entry_date = date('m/d/y', strtotime($xto_date));
					$total_time = $this->ttotal_time($user_pays->id, $from_date, $to_date); 
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
				}
			}
		} else {
		    echo "<tr>
				<td colspan='7' class='no-data'>
					No Timesheet found for selected pay period!
				</td>
			</tr>";
		}
	}
	
	public function post_ddata($payperiod,$search_by_comp){
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
			// $time_sheet[] = array('Entry Date','Emp ID','Last Name','First Name','Payroll Code', 'Hours');
			$count = 1;
			$time_sheet[] = array(
				'Entry Date' => "TC",
				'Emp ID' => "",
				'Last Name'  => "",
				'First Name'  => "",
				'Payroll Code' => "",
				'Hours Worked'   => "",
			);
			$time_sheet[] = array(
				'Entry Date' => "00001",
				'Emp ID' => "",
				'Last Name'  => "",
				'First Name'  => "",
				'Payroll Code' => "",
				'Hours Worked'   => "",
			);
			foreach($data as $datas)
			{	
				$hours_day = explode('_',$datas->hours_day);
						$hours_day = implode('-',$hours_day);
				
				$time_sheet_users[] = $datas->users_id;
			}
			$time_sheet_users = array_unique($time_sheet_users);
			$user_pay = User::with('companies')->whereIn('id', $users_arrr)->where("role", "=", "user")->orderBy('name', 'ASC')->get();
	
			if($user_pay){
				
				foreach($user_pay as $user_pays)
				{	 
				$entry_date = date('m/d/y', strtotime($xto_date));
					$total_time = $this->ttotal_time($user_pays->id, $from_date, $to_date); 
					if(isset($total_time) && $total_time > 0){
							$time_sheet[] = array(
							'Entry Date' => $entry_date,
							'Emp ID' => $user_pays->emp_id,
							'Last Name'  => $user_pays->last_name,
							'First Name'  => $user_pays->first_name,
							'Payroll Code' => "01",
							'Hours Worked'   => $total_time,
						);
					}
				}
				Excel::create('Payroll Report', function($excel) use ($time_sheet){
						$excel->setTitle('Payroll Report');
						$excel->sheet('Payroll Report', function($sheet) use ($time_sheet){
						$sheet->fromArray($time_sheet, null, 'A1', false, false);
					});
				})->download('csv');
			}
		}else{
			echo "No Timesheet for serached Payperiod!";
		}
		
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
				
				$com_out .= '<li>'.$company->company.'</li>';
			}
		}
		
		return $com_out;
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
	
}
