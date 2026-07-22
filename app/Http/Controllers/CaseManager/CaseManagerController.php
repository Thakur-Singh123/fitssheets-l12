<?php

namespace App\Http\Controllers\CaseManager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use App\TimeSheet;
use App\User;
use App\Company;
use App\House;
use Carbon\Carbon;
use App\UserManager;
use App\UserCasemanagerRel;
use App\LoginLogouttime;


class CaseManagerController extends Controller
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
		$users = UserCasemanagerRel::where('casemanager_id', '=', $user)->get();
		// $company_id = array();
		
		// if(isset($companies)){
		// 	foreach($companies as $company){
		// 		$companies = Company::where('id', '=', $company->users_id)->first();
		// 		$company_id[] = $companies->company;
		// 	}
		// }
		// $users = User::with('companies')->whereIn('companies_id', $company_id)->where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$user_arr = array();
		$user_count = 1;
		if(isset($users)){
			foreach($users as $userss){
				$user_arr[] = $userss->users_id;
				$user_count++;
			}
		}
		$dt = Carbon::now();
		$current_date_time = $dt->toDateString();
		$date    = explode('-', $current_date_time);
		$date = implode("_", $date);
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('hours_day', '=', $date)->whereIn('users_id', $user_arr)->orderBy('created_at', 'DESC')->get();
        return view('casemanager.dashboard',compact('data','current_date_time','user_count'));
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
	
	/**
     * User Reset Password
     *
     * @return void
     */
    public function resetpassword()
    {
		return view('casemanager.change_pass');
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
			return back()->with('Pass_Success','Password does not match');
		} else {
			$update_pass = DB::table('users')
			->where('id', $user->id)
			->where('role', $user->role)
			->update([
			'password' => bcrypt($request['new_password']),
			'pass' => $request['new_password'],
			]);
			if($update_pass){
				return back()->with('Pass_Success','Your Password is updated successfully!');
			} else {
				return back()->with('Pass_Success','Oops something went wrong');
			}
		}
	}
}
