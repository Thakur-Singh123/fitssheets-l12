<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\AdminMeta;
use App\Models\Company;
use App\Models\Department;
use App\Models\UserManager;
use DateTime;

class SupervisorInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {
		$TodayDate = new DateTime();
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
        $origin = new DateTime('2020-12-21');
        $interval = $origin->diff($TodayDate);
        $date_diff =  $interval->format('%a');
        if($date_diff == 0){
            	$frm_date = "2020_12_21";
		        $t_date = "2021_01_03";
        }
        
		$data = User::where("role", "=", "supervisor")->orderBy('name', 'ASC')->get();
		return view('admin.susers.user_view',compact('data','frm_date','t_date'));
    }
	
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.user_add');
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
			'name'     =>  'required|string|max:255',
			'email'    =>  'required|string|email|max:255|unique:users',
			'role' 	   =>  'required',
			'dept'     =>  'required',
			'password' =>  'required|string|min:8|same:confirmed',
		];
		$customMessages = [
			'name'     =>  'Please add user name',
			'email'    =>  'Please add user email',
			'role' 	   =>  'Please add user role',
			'dept'     =>  'Please add user department',
			'password' =>  'Add password or same as password entered before',
		];
		$this->validate($request, $rules, $customMessages);
				
		$form_data = array(
				'name' => $request->name,
				'email' => $request->email,
				'color_field' => $request->color_field,
				'role' => $request->role,
				'dept' => $request->dept,
				'status'     =>  '0',
				'password' => Hash::make($request->password),
		);
		
		$user_store = User::create($form_data);
			
		if($user_store){
			return redirect('/users')->with(['success' => 'User Created Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while creating User!!']);
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
		$data = User::where('id', '=', $id)->get();
		return view('admin.users.user_edit',compact('data'));
		
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
			'name'     =>  'required',
			'email'    =>  'required',
			'role' 	   =>  'required',
			'dept'     =>  'required',
			'password' =>  'required',
		];
		$customMessages = [
			'name'     =>  'Please add user name',
			'email'    =>  'Please add user email',
			'role' 	   =>  'Please add user role',
			'dept'     =>  'Please add user department',
			'password' =>  'Please add user password',
		];
		$this->validate($request, $rules, $customMessages);

		$form_data = array(
				'name' => $request->name,
				'email' => $request->email,
				'color_field' => $request->color_field,
				'role' => $request->role,
				'dept' => $request->dept,
				'status'     =>  '0',
				'password' => Hash::make($request->password),
		);
		$user_update = User::whereId($request->hidden_id)->update($form_data);

		if($user_update){
			return redirect('/users')->with(['success' => 'User Updated Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while updating User!!']);
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
        $data = User::findOrFail($id);
        $data->delete();
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
}
