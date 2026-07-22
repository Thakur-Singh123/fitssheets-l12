<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Company;
use App\Models\UserManager;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user-dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
			'company_id' => ['required'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
		$name = $data['first_name']." ".$data['last_name'];
        $last_id = DB::select('SELECT id FROM users WHERE role="user" ORDER BY id DESC LIMIT 1');
        $last_idd = $last_id[0]->id;
        $last_idd = $last_idd+1;
        $last_idd =  str_pad($last_idd, 3, "0", STR_PAD_LEFT); 
        $emp_id = "FITSS-".$last_idd;
        if(isset($data['company_id']) ){
            $companies = Company::where('company', '=', $data['company_id'])->orderBy('created_at', 'DESC')->get();
            $company_id = $companies[0]->id;
        }
        $user = User::create([
            'name' => $name,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'emp_id' => $emp_id,
            'email' => $data['email'],
            'role' => 'user',
            // 'dept' => $data['dept'],
            'companies_id' => $data['company_id'],
            'status'     =>  '0',
            'pass' => $data['password'],
            'password' => Hash::make($data['password']),
        ]);
        $manager_id = DB::getPdo()->lastInsertId();
        if(isset($company_id) ){
            // foreach($request->companys_id as $company){
                $user_comp = array('musers_id' => $manager_id, 'users_id' => $company_id);
                UserManager::create($user_comp);
            // }
        }   
        return $user;
    }
}
