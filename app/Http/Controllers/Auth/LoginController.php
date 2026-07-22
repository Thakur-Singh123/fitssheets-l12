<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	
	 protected function authenticated(Request $request, $user)
    {
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
		elseif ($role == "casemanager" && $status == "1") {
			return redirect('casemanager-dashboard');
		} 
		elseif ($role == "supervisor" && $status == "1") {
			return redirect('supervisor-dashboard');
		} 
		else
		{
			Auth::logout();
			return redirect('/login')->with('status','Your account is not activated, contact to admin');
		}
    }
}
