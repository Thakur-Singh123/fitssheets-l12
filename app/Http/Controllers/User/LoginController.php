<?php

namespace App\Http\Controllers\User;

use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use App\Models\LoginLogouttime;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
    */
    
    
    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    //show admin page
    public function index(){
		$view =  view('user/login');
        return $view;
    }

    //function for submit admin login form
    public function dologin(Request $request){
    	request()->validate([
            'email' => 'required', 'string', 'max:255',
            'password' => 'required', 'string', 'max:255'
        ]);

	   	$username = $request->input('email');
    	$password = $request->input('password');
    	// Check validation
		if (auth()->attempt(['email' => $username, 'password' => $password, 'role' => 'user'])) {
		    /*
	         * Description: 
	         * Params: 
	         */
	        $user = Auth::user();
			$role = $user->role;
			$status = $user->status;
			$user->update([
				'last_login_at' => Carbon::now()->toDateTimeString(),
				'last_login_ip' => $request->getClientIp()
			]);
			$form_data = array(
				'users_id' => $user->id,
				'last_login_at' => Carbon::now()->toDateTimeString(),
			);
			$login_inf = LoginLogouttime::create($form_data);
	        if ($role == "admin") {
	            return redirect('dashboard');
	        }
			
			// elseif ($role == "user" && $status == "1") {
				// return redirect('user-dashboard');
			// } 
			elseif ($role == "user") {
				return redirect('user-dashboard');
			} 
			elseif ($role == "manager" && $status == "1") {
				return redirect('manager-dashboard');
			} 
			elseif (Auth::user()->role == 'supervisor' && Auth::user()->status == 1) {
				return redirect('supervisor-dashboard');
			} 
			// else
			// {
				// Auth::logout();
				// return redirect('/login')->with('status','Your account is not activated, contact to admin');
			// }
		} else {
		    return back()->with('unsuccess','These credentials do not match our records.!');
		}
    }
}
