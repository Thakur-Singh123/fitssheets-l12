<?php
namespace App\Http\Controllers\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\TimeSheet;
use App\Models\User;
use App\Models\Company;
use App\Models\House;
use Carbon\Carbon;
use Image;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\LoginLogouttime;
use App\Models\UserManager;

class UserController extends Controller
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
	
	//Function for show index
    public function index() {
		//Get auth detail
		$user_id = Auth::user()->id;
		$dt = Carbon::now();
		$current_date_time = $dt->toDateString();
		$date    = explode('-', $current_date_time);
		$date = implode("_", $date);
		//Get companies
		$companies = UserManager::where('musers_id', '=', $user_id)->get();
		$company_id = array();
		if(isset($companies)){
			foreach($companies as $company){
				$company_id[] = $company->users_id;
			}
		}
		
		if(isset($c_id)){
			$c_id = $company_id[0];
		}else{
			$c_id = 0;
		}
		//Get payperiods
		$payperiods_dates = paychecks($c_id);
		if(isset($payperiods_dates)) {
			$frm_date  = $payperiods_dates[0]['frm_date'];
			$t_date = $payperiods_dates[0]['t_date'];
			$xfrm_date  = $payperiods_dates[0]['xfrm_date'];
			$xt_date = $payperiods_dates[0]['xt_date'];
		} else {
			$frm_date  = "";
			$t_date = "";
			$xfrm_date  = "";
			$xt_date = "";
		}
		if(!empty($frm_dt) && !empty($to_dt)) {
			$frm_date = $frm_dt;
			$t_date = $to_dt;
		}
		
		if($frm_date && $t_date) {
			$from_date    = explode('-', $frm_date);
			$from_date = implode("_", $from_date);
			$to_date    = explode('-', $t_date);
			$to_date = implode("_", $to_date);
		}
		//Get last payperiod
		$last_payperiod = $payperiods_dates[0]['payperiod'];
		//Get timesheet data
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('hours_day', '=', $date)->where('users_id', $user_id)->orderBy('created_at', 'DESC')->get();
		//Get last timesheet data
        $last_pay = TimeSheet::with('companies')
			->with('users')
			->with('houses')
			->where('users_id', '=', $user_id)
			->whereBetween('hours_day', array($from_date, $to_date))
			->orderBy('created_at', 'DESC')
			->sum('hours_wrk');
		if($xfrm_date && $xt_date){
			$paydate = date('M d, Y', strtotime($xt_date. ' + 5 days'));
		}					
		$user = User::where('id', '=', $user_id)->first(); 
		$hourley_rate = $user->hourst_rate;
		$total_pay = $last_pay * $hourley_rate;
		$pay_work = "";

        return view('user.dashboard',compact('data','current_date_time','last_pay','last_payperiod','hourley_rate','total_pay','paydate','pay_work'));
    } 
	
	/**
    * Logout
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
	//Function for logout
	public function logout(Request $request) {
		//Get auth login 
		$form_data = array(
			'users_id' => Auth::user()->id,
			'last_logout_at' => Carbon::now()->toDateTimeString(),
		);
		//Get logout time
		$login_inf = LoginLogouttime::create($form_data);
		Auth::logout();
	    return redirect('/login');
	}
	
	/**
    * User Reset Password
    *
    * @return void
    */
	//Function for show password
    public function resetpassword() {
		return view('user.change_pass');
    }
	
	//Function for update password
	public function updatepassword(Request $request) {
		//validate input fields
		$request->validate([
            'current_password' => 'required',
            'new_password' => 'min:6|required_with:confirm_password|same:new_confirm_password',
            'new_confirm_password' => 'required',
        ]);
		//update user password
		//$user = auth()->user(); 
		$user_id = Auth::user()->id;
		$user = User::where('id', '=', $user_id)->first(); 
        //Check if current password match or not
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
			//Check if password updated or not
			if($update_pass){
				return back()->with('success','Your Password is updated successfully.');
			} else {
				return back()->with('error','Oops something went wrong!');
			}
		}
	}
	/**
    * User Reset Password
    *
    * @return void
    */
	//Function for upload driving licence
    public function uploaddl() {
		return view('user.add_driving_license');
    }

	//Function for submit driving licence
	public function submitdl(Request $request){
		// $request->all();
		$rules = [
			'driving_license' => 'mimes:jpeg,jpg,png,gif|required|max:10240',
		];
		$customMessages = [
			'driving_license' => 'upload file should be image and less than 5MB',
			
		];
		$this->validate($request, $rules, $customMessages);
		if ($request->hasFile('driving_license')) {
			if( Auth::user()->drivers_license != '' ) {
				unlink(public_path() . '/assets/uploads/driving-license/' .Auth::user()->drivers_license);
			}
			$image = $request->file('driving_license');
			$name = 'emp_driving_license'.time().'.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/assets/uploads/driving-license/');
			$image->move($destinationPath, $name);
			$use_dl = User::whereId($request->user_id)->update(array('drivers_license' => $name));
			return back()->with('success','Image Upload successfully');
		}
	}
	
	/**
    * User Reset Password
    *
    * @return void
    */
	//Function for uploacor
    public function uploacor() {
		return view('user.add_covid_report');
    }
	
	//Function for submit uploacor
	public function submitcor(Request $request) {
		//$request->all();
		$rules = [
			'covid_report' => 'mimes:jpeg,jpg,png|required|max:10240',
		];
		$customMessages = [
			'covid_report' => 'upload file should be image and less than 5MB',
			
		];
		$this->validate($request, $rules, $customMessages);
		if ($request->hasFile('covid_report')) {
			if( Auth::user()->covid_report != '' && Auth::user()->covid_report != '0' ){
				unlink(public_path() . '/assets/uploads/covid-report/' .Auth::user()->covid_report);
			}
			$image = $request->file('covid_report');
			$name = 'emp_covid_report'.time().'.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/assets/uploads/covid-report/');
			$image->move($destinationPath, $name);
			$use_dl = User::whereId($request->user_id)->update(array('covid_report' =>$name));

			return back()->with('success','Report Upload successfully');
		}
	}
	
	//Function for update ncor
	public function update_ncor() {
		//Get id
		$use_dl = User::whereId(Auth::user()->id)->update(array(
			'covid_report'    =>	0,
		));

		return redirect('/user-dashboard');
	}
	
	/**
    * User Reset Password
    *
    * @return void
    */
    //Function for show user profile
    public function nameedit() {
		return view('user.name_edit');
    }
	
	//Function for update password
	public function nameupdate(Request $request) {
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
			$file->move(public_path('assets/uploads/users'), $filename);
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
	
	//Function for time status
	public static function time_status($id) {
		//Get timesheet
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $id)->orderBy('hours_day', 'DESC')->get();
		$data_count = count($data);
		return $data_count;
	}
}
