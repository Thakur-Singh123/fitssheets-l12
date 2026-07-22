<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Validator;
use App\TimeSheet;
use Excel;
use App\User;
use App\AdminMeta;
use App\Company;
use App\Holiday;
use App\Department;
use App\UserManager;
use DateTime;
use App\Payperiods;
use App\UserSupervisorRel;
use App\UserCasemanagerRel;
use App\UserVaccatioStatusn;
use App\UserVaccation;


class UserInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 

	 public function timesheetapproval(){
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		$data = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->paginate(15);
		//$companies = Company::orderBy('company', 'ASC')->get();
		$companies = Company::orderBy('display_order', 'ASC')->get();
		$Supervisor_color = User::where("role", "=", "supervisor")->orderBy('name', 'ASC')->get();
		//dd($Supervisor_color); die();
		return view('admin.users.timesheet',compact('data', 'companies','frm_date','t_date','Supervisor_color'));
	 }
    public function index()
    {
		//print_r('hii');die();
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		
			$payperiods_dates1 = Payperiods::with('companies')->orderBy('created_at', 'DESC')->get();
			//dd($payperiods_dates1);
		$data = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->paginate(15);
		$datauser = User::where('role', 'user')->orderBy('name', 'ASC')->get();
	$companies = Company::orderBy('company', 'ASC')->get();
		//$companies = Company::orderBy('display_order', 'ASC')->get();
		//print_r($companies);
		$Supervisor_color = User::where("role", "=", "supervisor")->orderBy('name', 'ASC')->get();
		return view('admin.users.user_view',compact('data', 'companies','frm_date','t_date','Supervisor_color','datauser','payperiods_dates1'));
    }
	
	public function user_with_date($frm_dt,$to_dt)
    {
		
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		if(!empty($frm_dt) && !empty($to_dt)){
			$frm_date = $frm_dt;
			$t_date = $to_dt;
		}
		$data = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->paginate(15);
		//$companies = Company::orderBy('company', 'ASC')->get();
		$companies = Company::orderBy('display_order', 'ASC')->get();
		return view('admin.users.user_view',compact('data', 'companies','frm_date','t_date'));
    }
	
	public function user_with_com($frm_dt,$to_dt,$search_by_comp)
    {
		
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		if(!empty($frm_dt) && !empty($to_dt)){
			$frm_date = $frm_dt;
			$t_date = $to_dt;
		}
		
		if($frm_date && $to_dt){
		$from_date    = explode('_', $frm_dt);
		$from_date = implode("-", $from_date);
		$to_date    = explode('_', $to_dt);
		$to_date = implode("-", $to_date);
		}
		
		
		$company_id = array();
		$user_idss = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			$companies = UserManager::where('users_id', '=', $search_by_comp)->get();
			
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
			$companies = UserManager::get();
			
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
		$users = User::with('companies')->whereIn('id', $user_idss)->where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$user_arr = array();
		$user_count = 1;
				if(isset($users)){

			foreach($users as $userss){
			$user_arr[] = $userss->id;
			}
		}
		if($frm_date == $t_date){
			$data = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('hours_day', $frm_date)
								->whereIn('users_id', $user_arr)
								->distinct()->get(['users_id']);
		}else{
			$data = TimeSheet::with('companies')
								->whereBetween('hours_day', array($frm_date, $t_date))
								->whereIn('users_id', $user_arr)
								->distinct()->get(['users_id']);
		}
		
		$user_time = array();
		if(isset($data)){
			foreach($data as $datas){
				$user_time[] = $datas->users_id;
			}
		}
		// $data = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->paginate(15);
		$data = User::with('companies')->whereIn('id', $user_time)->where('role', '=', "user")->orderBy('created_at', 'DESC')->paginate(35);
		$companies = Company::orderBy('display_order', 'ASC')->get();
		//$companies = Company::orderBy('company', 'ASC')->get();
		//print($data);
		return view('admin.users.user_view',compact('data', 'companies','from_date','to_date','frm_date','t_date','search_by_comp'));
	}
	
	
	public function timesheets($id)
    {

		$payperiods_dates = payperiods();
		
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
			 $sfrm_date  = $payperiods_dates[1]['sfrm_date'];
			 $st_date = $payperiods_dates[1]['st_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
			$sfrm_date  = "";
			$st_date = "";
		}
		$user = User::where("role", "=", "user")->where('id', '=', $id)->orderBy('created_at', 'DESC')->first();
		$name = $user->name;
		$data = TimeSheet::with('companies')->whereBetween('hours_day', array($sfrm_date, $st_date))->where('users_id', '=', $id)->orderBy('created_at', 'DESC')->get();
		$payperiods_dates1 = Payperiods::orderBy('created_at', 'DESC')->get();
        $companies = Company::orderBy('display_order', 'ASC')->get();
		//$companies = Company::orderBy('company', 'ASC')->get();
		return view('admin.timesheet.ts_view',compact('data', 'id','name','frm_date','t_date','companies','payperiods_dates1'));
    }
	
	public function timesheets_company($id,$frm_dt,$to_dt,$search_by_comp)
    {

		$payperiods_dates = payperiods();
		
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
			 $sfrm_date  = $payperiods_dates[1]['sfrm_date'];
			 $st_date = $payperiods_dates[1]['st_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
			$sfrm_date  = "";
			$st_date = "";
		}
		
		if(!empty($frm_dt) && !empty($to_dt)){
			$frm_date = $frm_dt;
			$t_date = $to_dt;
			$sfrm_date = explode('-',$frm_dt);
			$sfrm_date = implode('_',$sfrm_date);
			$st_date = explode('-',$to_dt);
			$st_date = implode('_',$st_date);
		}
		$user = User::where("role", "=", "user")->where('id', '=', $id)->orderBy('created_at', 'DESC')->first();
		$name = $user->name;
		$data = TimeSheet::with('companies')->whereBetween('hours_day', array($sfrm_date, $st_date))->where('users_id', '=', $id)->orderBy('created_at', 'DESC')->get();
		return view('admin.timesheet.ts_view',compact('data', 'id','name','frm_date','t_date','search_by_comp'));
    }
	
	public function musers($id)
    {
		$user = UserManager::with('users')->where("musers_id", "=", $id)->orderBy('created_at', 'DESC')->get();
		return view('admin.musers.muser_view',compact('user', 'id'));
    }
	
	public function mcreate($id)
    {
		$user = User::where("role", "=", "user")->orderBy('created_at', 'DESC')->get();
        return view('admin.musers.muser_add', compact('user','id'));
    }
	
	 public function mstore(Request $request)
    {
		$rules = [
			'user_id'    =>  'required',
		];
		$customMessages = [
			'user_id'    =>  'Please Select User',
		];
		$this->validate($request, $rules, $customMessages);
				
		$form_data = array(
				'musers_id' => $request->muser_id,
				'users_id' => $request->user_id,
		);
		
		$muser_store = UserManager::create($form_data);
			
		if($muser_store){
			return redirect('/user/musers/'.$request->muser_id)->with(['success' => 'User Added Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while creating User!!']);
		}
		
    }
	
	 public function mdestroy($id)
    {
        $data = UserManager::findOrFail($id);
        $data->delete();
    }
	
	
	/**
     * User Reset Password
     *
     * @return void
     */
    public function resetpassword($id)
    {
		$data = User::where('id', '=', $id)->get();
		return view('admin.users.user_change_pass', compact('data', 'id'));
    }
	
	
	//function for update profile password
	public function updatepassword(Request $request){
		request()->validate([
            'current_password' => 'required',
            'new_password' => 'min:6|required_with:confirm_password|same:new_confirm_password',
            'new_confirm_password' => 'required',
        ]);
		//update user password
		$user = User::where('id', '=', $request->hidden_id)->first(); 

		if(!Hash::check($request['current_password'], $user->password)){
			return back()->with('Pass_Success','Password does not match');
		} else {
			$update_pass = DB::table('users')
			->where('id', $user->id)
			->update([
			'password' => bcrypt($request['new_password']),
			'pass' => $request['new_password'],
			]);
			if($update_pass){
				return back()->with('Pass_Success','Password is updated successfully!');
			} else {
				return back()->with('Pass_Success','Oops something went wrong');
			}
		}
	}
	
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$department = Department::orderBy('department', 'ASC')->get();
		$companies = Company::orderBy('created_at', 'DESC')->get();
        return view('admin.users.user_add',compact('department','companies'));
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
			'first_name'     =>  'required',
			'email'    =>  'required|email|unique:users',
			'role' 	   =>  'required',
			'driving_license' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
		];
		$customMessages = [
			'first_name'     =>  'Please add user first name',
			'email'    =>  'Please add user email and must be unique.',
			'role' 	   =>  'Please add user role',
		];
		$this->validate($request, $rules, $customMessages);
		if ($request->hasFile('driving_license')) {
			$image = $request->file('driving_license');
			$iname = 'emp_driving_license'.time().'.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/assets/uploads/driving-license/');
			$image->move($destinationPath, $iname);
		}else{
			$iname = "";
		}
		$name = $request->first_name." ".$request->last_name;
		// $emp_id = "ILS".rand(100,10000);
		$last_id = DB::select('SELECT id FROM users WHERE role="user" ORDER BY id DESC LIMIT 1');
        $last_idd = $last_id[0]->id;
        $last_idd = $last_idd+1;
        $last_idd =  str_pad($last_idd, 3, "0", STR_PAD_LEFT); 
        $emp_id = "FITSS-".$last_idd;

        if(isset($request->companys_id) ){
        	$companies = Company::where('id', '=', $request->companys_id[0])->orderBy('created_at', 'DESC')->get();
        	$company_nm = $companies[0]->company;
		}else{
			$company_nm = "";
		}
		$date    = explode('-', $request->hours_day);
		$date = implode("_", $date);
		$form_data = array(
				'name' => $name,
				'username' => $request->username,
				'first_name' => $request->first_name,
				'last_name' => $request->last_name,
				'emp_id' => $request->emp_id,
				'dob' => $date,
				'phone_no' => $request->phone_no,
				'ssn_no' => $request->ssn_no,
				'phone_no' => $request->phone_no,
				'child_sup' => $request->child_sup,
				'health_insurance' => $request->health_insurance,
				'email' => $request->email,
				'role' => $request->role,
				'dept' => $request->dept,
				'status'     =>  $request->status,
				'companies_id' => $company_nm,
				'hourst_rate' => $request->hour_rate,
				'password' => Hash::make($request->password),
				'pass' => $request->password,
		);
				
		$user_store = User::create($form_data);
		$manager_id = DB::getPdo()->lastInsertId();
		if(isset($request->companys_id) ){
			foreach($request->companys_id as $company){
				$user_comp = array('musers_id' => $manager_id, 'users_id' => $company);
				UserManager::create($user_comp);
			}
		}	
		if($user_store){
			if($request->role == "user"){
				return redirect('/users')->with(['success' => 'User Created Successfully!!']);
			}elseif($request->role == "manager"){
				return redirect('/managers')->with(['success' => 'Manager Created Successfully!!']);
			}elseif($request->role == "supervisor"){
				return redirect('/supervisors')->with(['success' => 'Supervisor Created Successfully!!']);
			}
			
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
		$alluser = User::where("role", "=", "user")->orderBy('name', 'ASC')->get();
		$department = Department::orderBy('department', 'ASC')->get();
		$companies = Company::orderBy('created_at', 'DESC')->get();
		$data = User::where('id', '=', $id)->get();
		$company_id = UserManager::where('musers_id', '=', $id)->get();
		$UserSupervisorRel = UserSupervisorRel::where('supervisor_id', '=', $id)->get();
		$UserCasemanagerRel = UserCasemanagerRel::where('casemanager_id', '=', $id)->get();
		return view('admin.users.user_edit',compact('data','department','companies','company_id','alluser','UserSupervisorRel','UserCasemanagerRel'));
		
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
			'first_name'     =>  'required',
			'email'    =>  'required|email',
			'role' 	   =>  'required',
			'dept'     =>  'required',
		];
		$customMessages = [
			'first_name'     =>  'Please add user first name',
			'email'    =>  'Please add user email and must be unique.',
			'role' 	   =>  'Please add user role',
			'dept'     =>  'Please add user department',
		];
		$this->validate($request, $rules, $customMessages);
		$user = User::whereId($request->hidden_id)->first();
		
		if ($request->hasFile('driving_license')) {
			if( $user->drivers_license != '' ){
				unlink(public_path() . '/assets/uploads/driving-license/' .$user->drivers_license);
			}
			$image = $request->file('driving_license');
			$iname = 'emp_driving_license'.time().'.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/assets/uploads/driving-license/');
			$image->move($destinationPath, $iname);
		}else{
			$iname = "";
		}
		$name = $request->first_name." ".$request->last_name;
		if(isset($request->companys_id) ){
        	$companies = Company::where('id', '=', $request->companys_id[0])->orderBy('created_at', 'DESC')->get();
        	$company_nm = $companies[0]->company;
		}else{
			$company_nm = "";
		}
		$date    = explode('-', $request->hours_day);
		$date = implode("_", $date);
		$form_data = array(
				'name' => $name,
				'username' => $request->username,
				'first_name' => $request->first_name,
				'last_name' => $request->last_name,
				'emp_id' => $request->emp_id,
				'dob' => $date,
				'phone_no' => $request->phone_no,
				'ssn_no' => $request->ssn_no,
				'child_sup' => $request->child_sup,
				'health_insurance' => $request->health_insurance,
				'drivers_license' => $iname,
				'email' => $request->email,
				'color_field' => $request->color_field,
				'role' => $request->role,
				'dept' => $request->dept,
				'status'     =>  $request->status,
				'hourst_rate' => $request->hour_rate,
				'companies_id' => $company_nm
		);

		$user_update = User::whereId($request->hidden_id)->update($form_data);
		if(isset($request->companys_id)){
			$data_delte = UserManager::where("musers_id","=", $request->hidden_id)->delete();
			foreach($request->companys_id as $company){
				$user_comp = array('musers_id' => $request->hidden_id, 'users_id' => $company);
				UserManager::create($user_comp);
			}
		}	
		
		if(isset($request->users_id)){
			foreach($request->users_id as $users){
				$users_arrr[] = $users;
			}
		}
		if(isset($users_arrr)){
			$users_arrr = array_unique($users_arrr);
			$data_delte = UserSupervisorRel::where("supervisor_id", "=", $request->hidden_id)->delete();
			foreach($users_arrr as $users_arr){
				$form_data = array(
					'users_id' => $users_arr,
					'supervisor_id' => $request->hidden_id,
				);
				$UserSupervisorRel = UserSupervisorRel::create($form_data);
			}
		}	

		if(isset($request->cmusers_id)){
			foreach($request->cmusers_id as $users){
				$cmusers_arrr[] = $users;
			}
		}
		if(isset($cmusers_arrr)){
			$cmusers_arrr = array_unique($cmusers_arrr);
			$data_delte = UserCasemanagerRel::where("casemanager_id", "=", $request->hidden_id)->delete();
			foreach($cmusers_arrr as $users_arr){
				$form_data = array(
					'casemanager_id'=> $request->hidden_id,
					'users_id' => $users_arr,
				);
				$UserCasemanagerRel = UserCasemanagerRel::create($form_data);
			}
		}	
		
		if($user_update){
			if($request->role == "user"){
				return redirect('/users')->with(['success' => 'User Updated Successfully!!']);
			}elseif($request->role == "casemanager"){
				return redirect('/casemanagers')->with(['success' => 'Manager Updated Successfully!!']);
			}elseif($request->role == "supervisor"){
				return redirect('/supervisors')->with(['success' => 'Supervisor Updated Successfully!!']);
			}
			
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
	
	public function user_search(Request $request)
    {

		$searchTerm = $request->srch_user;
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
					$name  = strtoupper($approved_by);
				   $words = explode(" ", $name);
				    $firtsName = reset($words); 
					// echo substr($firtsName,0,1);
					 $last_name = !empty($words[1]) ? $words[1] : '';
					 
					 $approved_by = substr($firtsName,0,1). ' ' . $last_name;
					$color_info =  $this->color_info($datas->id); 
					
					
					if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					}
					  echo "<td>".$count."</td>";
					  echo "<td>".$datas->emp_id."</td>";
					  echo "<td>".$datas->first_name."</br><span>".$datas->last_name."</span></td>";
					   echo "<td>".$approved_by."</td>";
					  /*
					
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
					  echo "<td>".$datas->email."</td>";*/
					  
					/* if(Auth::user()->id == 72 || Auth::user()->id == 50 || Auth::user()->id == 282 ){
						 }else{
					  	 echo "<td>".$datas->pass."</td>";
						 }
					 echo "<td>".$datas->dept."</td>";*/
				 $user_companies = $this->user_companies($datas->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
				  echo "<td>".$datas->hourst_rate."</td>";
			   echo "<td>";
			  if($datas->last_login_at != null) { echo date('h:i a', strtotime($datas->last_login_at)) ; }
			  echo "</td>";
				//  echo "<td>";
				  //if($datas->last_login_at != null) { echo date('M d, Y', strtotime($datas->last_login_at));  }
				 // echo "</td>";
				//	  echo "<td>".date('M d, Y', strtotime($datas->created_at))."</td>";
					  
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
	
	
	public function user_searchs(Request $request)
    {
		
		$searchTerm = $request->srch_users;
		$data = User::where('role', 'user')
						->where('name', 'LIKE', "%{$searchTerm}%") 
						->orWhere('email', 'LIKE', "%{$searchTerm}%")
						->orWhere('emp_id', '=', $searchTerm)
						->orderBy('name', 'ASC')
						->get();
								//dd($data);die();
			return view('admin.top_search',compact('data'));
    }
	
	
	
	
	

	public function nuser_search(Request $request)
    {
		$searchTerm = $request->srch_user;
		$data = User::where('role', 'user')
						->where('name', 'LIKE', "%{$searchTerm}%") 
						->orWhere('email', 'LIKE', "%{$searchTerm}%")
						->orWhere('emp_id', '=', $searchTerm)
						->orderBy('name', 'ASC')
						->get();
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
					 
					
				 $user_companies = $this->user_companies($datas->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
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

	public function app_status(Request $request)
    {
		$searchTerm = $request->aap_status;
		$data = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('approve', $searchTerm)
								->distinct()->get(['users_id']);
		$user_time = array();
		if(isset($data)){
			foreach($data as $datas){
				$user_time[] = $datas->users_id;
			}
		}
		
		$users_data = User::with('companies')->whereIn('id', $user_time)->where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$ucount = 1;
			if(isset($users_data)){
			$count = 1; 
			  if($users_data->count() != 0){
				foreach ($users_data as $datas){
					$approved_by = $this->approved_by($datas->id); 
					$name  = strtoupper($approved_by);
				   $words = explode(" ", $name);
				    $firtsName = reset($words); 
					// echo substr($firtsName,0,1);
					 $last_name = !empty($words[1]) ? $words[1] : '';
					 
					 $approved_by = substr($firtsName,0,1). ' ' . $last_name;
					$color_info = $this->color_info($datas->id); 
					
					if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					}
					  echo "<td>".$count."</td>";
					  echo "<td>".$datas->emp_id."</td>";
					echo "<td>".$datas->first_name."</br><span>".$datas->last_name."</span></td>";   
					 echo "<td>".$approved_by."</td>";
					 
					  
				 $user_companies = $this->user_companies($datas->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
				  echo "<td>".$datas->hourst_rate."</td>";
			  echo "<td>";
			  if($datas->last_login_at != null) { echo date('h:i a', strtotime($datas->last_login_at)) ; }
			  echo "</td>";
				 
					
					   
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
	
   	public function napp_status(Request $request)
    {
		$searchTerm = $request->aap_status;
		$data = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('approve', $searchTerm)
								->distinct()->get(['users_id']);
		$user_time = array();
		if(isset($data)){
			foreach($data as $datas){
				$user_time[] = $datas->users_id;
			}
		}
		$users_data = User::with('companies')->whereIn('id', $user_time)->where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$ucount = 1;
			if(isset($users_data)){
			$count = 1; 
			  if($users_data->count() != 0){
				foreach ($users_data as $datas){
					$approved_by = $this->approved_by($datas->id); 
					$color_info = $this->color_info($datas->id); 
					
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
					 
					 
					  	
				 $user_companies = $this->user_companies($datas->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
				  
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

	public function user_color(Request $request)
    {
		
		$search_by_super = $request->search_by_super;
		$UserSupervisorRel = UserSupervisorRel::where("supervisor_id", $search_by_super)->get();
		$user_array = array();
		if(isset($UserSupervisorRel)){
			foreach($UserSupervisorRel as $UserSupervisorRel){
				$user_array[] = $UserSupervisorRel->users_id;
			}
			
		}
		$users_data = User::with('companies')->whereIn('id', $user_array)->where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$ucount = 1;
			if(isset($users_data)){
			$count = 1; 
			  if($users_data->count() != 0){
				foreach ($users_data as $datas){
					$approved_by = $this->approved_by($datas->id); 
					$name  = strtoupper($approved_by);
				   $words = explode(" ", $name);
				    $firtsName = reset($words); 
					// echo substr($firtsName,0,1);
					 $last_name = !empty($words[1]) ? $words[1] : '';
					 
					 $approved_by = substr($firtsName,0,1). ' ' . $last_name;
					$color_info = $this->color_info($datas->id); 
					if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					}
					  echo "<td>".$count."</td>";
					  echo "<td>".$datas->emp_id."</td>";
					  echo "<td>".$datas->first_name."</br><span>".$datas->last_name."</span></td>"; 
					     echo "<td>".$approved_by."</td>";
					
				 $user_companies = $this->user_companies($datas->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
				  echo "<td>".$datas->hourst_rate."</td>";
			  
				
					  echo "<td>";
			  if($datas->last_login_at != null) { echo date('h:i a', strtotime($datas->last_login_at)) ; }
			  echo "</td>";
					  
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
	
	
	
	public function user_status(Request $request)
    {
		$user_status = $request->user_status;
		
		$users_data = User::with('companies')->where('status', '=', $user_status)->where('role', '=', "user")->orderBy('created_at', 'DESC')->get();
		$ucount = 1;
			if(isset($users_data)){
			$count = 1; 
			  if($users_data->count() != 0){
				foreach ($users_data as $datas){
					$approved_by = $this->approved_by($datas->id); 
					$name  = strtoupper($approved_by);
				   $words = explode(" ", $name);
				    $firtsName = reset($words); 
					// echo substr($firtsName,0,1);
					 $last_name = !empty($words[1]) ? $words[1] : '';
					 
					 $approved_by = substr($firtsName,0,1). ' ' . $last_name;
					$color_info = $this->color_info($datas->id); 
					if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					}
					  echo "<td>".$count."</td>";
					  echo "<td>".$datas->emp_id."</td>";
					 
					   
					  
					 // echo "<td>";
					 // if($datas->status == 1 ){ echo "<h5 style='color:green'>Active</h5>"; }
					  //else{ echo "<h5 style='color:red'>Inactive</h5>"; }
					  //echo "</td>";
					  echo "<td>".$datas->first_name."</br><span>".$datas->last_name."</span></td>";
					  echo "<td>".$approved_by."</td>";
					 
				 $user_companies = $this->user_companies($datas->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
				  echo "<td>".$datas->hourst_rate."</td>";
			 
				  echo "<td>";
			  if($datas->last_login_at != null) { echo date('h:i a', strtotime($datas->last_login_at)) ; }
			  echo "</td>";
					
					  
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
	
	public function exort_user()
    {
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}

		$from_date    = explode('-', $frm_date);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $t_date);
		$to_date = implode("_", $to_date);
		$paydate = date("d M", strtotime('+5 days', strtotime($t_date)));
		
		$users = User::with('companies')->where('role', '=', "user")->orderBy('name', 'ASC')->get();
		$user_arr = array();
		$user_count = 1;
		if(isset($users)){
			 // $time_sheet[] = array(

						// '#' => "",
						// 'Emp ID' => "",
						// 'Last Name'   => "Payperiod",
						// 'First Name'   => "",
						// 'Name'  => "",
						// 'Company'   => date("d", strtotime($frm_date)).'-'.date("d M", strtotime($t_date)),
						// 'Total Hours'   => "",
						// 'Hourley Rate'   => "",
						// 'Billed $18'   => "",
						// 'Total Pay'   => "",
						// 'Total Billed'   => "",
						// 'Profit'   => "",
						// 'approver_name' => "",
						// 'approver_color' => "",
						
					// );			  		
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
										
					$approved_by = $this->approved_by($userss->id);
					$color_info = $this->color_info($userss->id); 
				}else{
					$data = TimeSheet::with('companies')
										->with('houses')
										->with('users')
										->whereBetween('hours_day', array($from_date, $to_date))
										->where('users_id', $userss->id)
										->orderBy('hours_day', 'DESC')
										->get();
					$approved_by = $this->tapproved_by($userss->id,$from_date, $to_date);
					$color_info = $this->color_info($userss->id); 
				}
				
				if(!empty($approved_by)){
					$approver_name = $approved_by;
				}else{
					$approver_name = "";
				}
			if(!empty($color_info)){
					$color_info = $color_info;
				}else{
					$color_info = "";
				}
				
			$count = 1;
			$total_hours = 0;
			$approved_hours = 0;
			$denied_hours = 0;
			  if($data->count() != 0){
				foreach ($data as $datas){
					$total_hours = $total_hours + $datas->hours_wrk;
					if($datas->approve == "2"){ 
							$approved_hours					  = $approved_hours + $datas->hours_wrk;
					}elseif($datas->approve == "1"){
						$denied_hours					  = $denied_hours + $datas->hours_wrk;
					}
					
				}
			  }
			
			  $total_pay = $approved_hours * $userss->hourst_rate;
			  if($userss->hourst_rate > 0){
				  $billed_rate =$userss->hourst_rate + number_format("6",2);
			  }else{
				  $billed_rate = 0;
			  }
			  $user_companies = $this->exp_user_companies($userss->id);
			  $total_billed = $approved_hours * $billed_rate;
			  $profit = $total_billed-$total_pay;
			  $time_sheet[] = array(

						'#' => $user_count,
						'Emp ID' => $userss->emp_id,
						'Last Name'   => $userss->last_name,
						'First Name'   => $userss->first_name,
						'Name'  => $userss->name,
						'Company'   => $user_companies,
						'Total Hours'   => $approved_hours,
						'Hourley Rate'   => '$'.$userss->hourst_rate,
						'Billed $18'   => '$'.$billed_rate,
						'Total Pay'   => $total_pay,
						'Total Billed'   => $total_billed,
						'Profit'   => $profit,
						'approver_name' => $approver_name,
						'approver_color' => $color_info,
						
						
					);			  	
			$user_count++;					
			}
		}
		
		Excel::create('Time Sheet', function($excel) use ($time_sheet){
			$excel->setTitle('Time Sheet');
			$excel->sheet('Time Sheet', function($sheet) use ($time_sheet){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
				
				$sheet->row(1, array('#','Emp ID', 'Last Name','First Name','Name', 'Company','Total Hours', 'Hourley Rate','Billed $18', 'Total Pay', 'Total Billed', 'Profit'));
				$sheet->cells('A1:L1', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#BDB76B');
								$cells->setAlignment('center');
							});
				$i = 2;

				foreach ($time_sheet as $cleans) {

					$sheet->row($i, array($cleans['#'], $cleans['Emp ID'], $cleans['Last Name'],$cleans['First Name'], $cleans['Name'], $cleans['Company'], $cleans['Total Hours'],$cleans['Hourley Rate'], $cleans['Billed $18'], $cleans['Total Pay'], $cleans['Total Billed'], $cleans['Profit']));
				
					if($cleans['approver_color'] != ""){
						$sheet->cell('M'.$i, function($cell) {
							$cell->setValue('');
						});
						$sheet->cell('N'.$i, function($cell) {
							$cell->setValue('');
						});
						$bgcolor = $cleans['approver_color'];
						$sheet->cells('A'.$i.':L'.$i, function ($cells) use ($bgcolor) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground($bgcolor);
							$cells->setAlignment('center');
						});
					}else{
						$sheet->cell('M'.$i, function($cell) {
							$cell->setValue('');
						});
						$sheet->cell('N'.$i, function($cell) {
							$cell->setValue('');
						});
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
	
	public function post_paydata(Request $request){
        // print_r($request->all()); die();
		$bet_dates = explode('-',$request->search_by_pay);
		//print_r($bet_dates); die();
		$search_by_compa = $request->search_by_compa;
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
		$xto_date = implode('-',$xto_date);
		$xfrom_date = explode('_',$from_date);
		$xfrom_date = implode('-',$xfrom_date);
		$paydate = date('Y-m-d', strtotime($xto_date. ' + 5 days'));
		
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
										
					$approved_by = $this->approved_by($userss->id);
					$color_info = $this->color_info($userss->id); 
				}else{
					$data = TimeSheet::with('companies')
										->with('houses')
										->with('users')
										->whereBetween('hours_day', array($from_date, $to_date))
										->where('users_id', $userss->id)
										->orderBy('hours_day', 'DESC')
										->get();
					$approved_by = $this->tapproved_by($userss->id,$from_date, $to_date);
					$color_info = $this->color_info($userss->id); 
				}
				
				if(!empty($approved_by)){
					$approver_name = $approved_by;
				}else{
					$approver_name = "";
				}
				if(!empty($color_info)){
					$color_info = $color_info;
				}else{
					$color_info = "";
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
			
			 if(!empty($user_companies)){
				
					$user_company_name = $user_companies;
				}else{
					
					$user_company_name = "";
				}
			 // $user_company_name = $user_companies;
			  
			  
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
			  $total_pay = $approved_hours * $userss->hourst_rate;
			  
			  if($userss->hourst_rate > 0){
				  $billed_rate =$userss->hourst_rate + number_format("6",2);
			  }else{
				  $billed_rate = 0;
			  }
			  if($billed_rate > 0){
				 // $holiday_hourley =$billed_rate * number_format("1.5",2);
				 $holiday_hourley = $userss->hourst_rate * number_format("1.5",2);
			  }else{
				  $holiday_hourley = 0;
			  }
			  
			  $user_companies = $this->exp_user_companies($userss->id);
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
								 
			//echo $reg_hours;
				 //echo "<pre>";
				// print_r($supervisor_arr);
				// die;
			  
			  $time_sheet[] = array(

						'#' => $user_count,
						'SSN' => $userss->emp_id,
						'Last Name'   => $userss->last_name,
						'First Name'   => $userss->first_name,
						'Company'   => $user_companies,
						'Reg Hours' => $reg_hours,
						'Holiday Hours'   => $holiday_tm,
						'Total Hours'   => $approved_hours,
						'Hourley Rate'   => '$'.$userss->hourst_rate,
						'Holiday Rate'   => '$'.$holiday_hourley,
						'Total Pay'   => $total_pay,
						'Billed Rate'   => '$'.$billed_rate,
						'Rate'          => '$'.$rate,
						'Total Billed'   => $total_billed,
						'approver_name' => $approver_name,
						'approver_color' => $color_info,
						'Holiday Pay' => $htotal_pays,
						'Total Holiday' => $Total_with_holiday,
						
						
					);			  	
				$user_count++;					
			}
		}
		
		$stitle = $user_company_name." Timesheet details for ".$paydate;
		//$stitle1 = "Employee Details";
		Excel::create('Time Sheet', function($excel) use ($time_sheet,$stitle,$paydate,$xfrom_date,$xto_date){
			$excel->setTitle('Time Sheet');
			$excel->sheet('Time Sheet', function($sheet) use ($time_sheet,$stitle,$paydate,$xfrom_date,$xto_date){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
				
				$sheet->row(1, array('','', '','','',$stitle,'', '','', '', '','', '', '',''));
				$sheet->row(2, array('','', '','','','', '','','Employee Details', '','','Client Billing Details','','',''));
				$sheet->row(3, array('#','SSN', 'Last Name','First Name', 'Company','Reg Hours','Holiday Hours','Total Hours', 'Hourley Rate','Holiday Rate', 'Total Pay($)', 'Billed Rate','Rate','Total Billed($)', 'Approved By'));
				$sheet->cell('P1', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('P2', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('P3', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('R1', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('R2', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('R3', function($cell) {
							$cell->setValue('');
						});
				$sheet->cells('A1:O1', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(22);
								$cells->setAlignment('center');
							});
				$sheet->Cells('I2:K2', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#FCD5B4');
								$cells->setAlignment('center');
							});
				$sheet->Cells('L2:N2', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('##D8D8D8');
								$cells->setAlignment('center');
							});			
				$sheet->cells('A3:O3', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#BDB76B');
								$cells->setAlignment('center');
							});			
				$i = 4;$j = 0;$k = 0;$l = 0;$m = 0;$n = 0;$o = 0;$p = 0; $q = 0; $r = 0;$s = 0;$t = 0;$u = 0;$v = 0;$w = 0;$x = 0; $y = 0; $z = 0;$emp = 0;$emp_wt_hrs = 0;
				$total_hours = 0;
				$total_Pay = 0;
				$total_billed = 0;
				$htotal_hours = 0;
				$htotal_Pay = 0;
				$htotal_pays = 0;
				$whtotal_billed = 0;
				
				foreach ($time_sheet as $cleans) {

					$sheet->row($i, array($cleans['#'], $cleans['SSN'], $cleans['Last Name'],$cleans['First Name'], $cleans['Company'], $cleans['Reg Hours'],  $cleans['Holiday Hours'], $cleans['Total Hours'],$cleans['Hourley Rate'],$cleans['Holiday Rate'], $cleans['Total Pay'], $cleans['Billed Rate'], $cleans['Rate'], $cleans['Total Billed'], $cleans['approver_name']));
				
					if($cleans['approver_color'] != ""){

						$sheet->cell('P'.$i, function($cell) {
							$cell->setValue('');
						});
						$sheet->cell('R'.$i, function($cell) {
							$cell->setValue('');
						});
						$bgcolor = $cleans['approver_color'];
						$sheet->cells('A'.$i.':O'.$i, function ($cells) use ($bgcolor) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground($bgcolor);
							$cells->setAlignment('center');
						});
					}else{

						$sheet->cell('P'.$i, function($cell) {
							$cell->setValue('');
						});
						$sheet->cell('R'.$i, function($cell) {
							$cell->setValue('');
						});
						$sheet->cells('A'.$i.':O'.$i, function ($cells) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground('#ffffff');
							$cells->setAlignment('center');
						});
					}
					if($cleans['approver_name'] == "Vladimir Ndebugre"){
						$j++;
					}elseif($cleans['approver_name'] == "Holly Wolfe"){
						$k++;
					}elseif($cleans['approver_name'] == "Regina Quartey"){
						$l++;
					}elseif($cleans['approver_name'] == "Long Caitlin"){
						$m++;
					}elseif($cleans['approver_name'] == "Emmanuel ndyia"){
						$o++;
					}elseif($cleans['approver_name'] == "John Seshie"){
						$p++;
					}elseif($cleans['approver_name'] == "Onbridges"){
						$q++;
					}elseif($cleans['approver_name'] == "Owura Kusi"){
						$r++;
					}elseif($cleans['approver_name'] == "Kasim Sulemana"){
						$s++;
					}elseif($cleans['approver_name'] == "William Kesson"){
						$t++;
					}
					if($cleans['Total Hours'] > 0){
						$emp_wt_hrs++;
					}else{
						$emp++;
					}
					$total_hours += (float)$cleans['Total Hours'];
					$total_Pay += (float)$cleans['Total Pay'];
					$htotal_hours += (float)$cleans['Holiday Hours'];
					$htotal_pays  += (float)$cleans['Holiday Pay'];
					$whtotal_billed += (float)$cleans['Total Billed'];
					$total_billed += (float)$cleans['Total Holiday'];
					$i++;
				}
				$supervisor_arr = array();
				
				if($j > 0){
					$supervisor_arr[] = array('supervisor' => 'Vladimir', 'count' => $j);
				}
				if($k > 0){
					$supervisor_arr[] = array('supervisor' => 'Holly', 'count' => $k);
				}
				if($l > 0){
					$supervisor_arr[] = array('supervisor' => 'Regina' , 'count'=> $l);
				}
				if($m > 0){
					$supervisor_arr[] = array('supervisor' => 'Long' , 'count'=> $m);
				}
				if($o > 0){
					$supervisor_arr[] = array('supervisor' => 'Emmanuel' , 'count'=> $o);
				}
				if($p > 0){
					$supervisor_arr[] = array('supervisor' => 'John' , 'count'=> $p);
				}
				if($q > 0){
					$supervisor_arr[] = array('supervisor' => 'Onbridges' , 'count'=> $q);
				}
				if($r > 0){
					$supervisor_arr[] = array('supervisor' => 'Owura' , 'count'=> $r);
				}
				if($s > 0){
					$supervisor_arr[] = array('supervisor' => 'Kasim' , 'count'=> $s);
				}
				if($t > 0){
					$supervisor_arr[] = array('supervisor' => 'William' , 'count'=> $t);
				}
				// echo $s;
				// echo "<pre>";
				// print_r($supervisor_arr);
				// die;
			//sheet->row($i, array('','', '','','Sum', '', '$'.$htotal_hours,',$total_hours,'','',$'.$total_Pay,'','','$'.$total_billed));
				$sheet->row($i, array('','', '','','Sum', '','',$total_hours,'','', '$'.$total_Pay, '','', '$'.$total_billed));
				$sheet->cells('E'.$i.':O'.$i, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setFontWeight('bold');
								$cells->setBackground('#fcd5b4');
								$cells->setAlignment('center');
							});
				$row1 = $i+1;	
				$sheet->row($row1, array('','Summary', '','','','', '', '','', '', ''));
				$sheet->cells('A'.$row1.':D'.$row1, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setFontWeight('bold');
								$cells->setBackground('#ffffff');
								$cells->setAlignment('center');
								
							});
				
				$row2 = $i+2;
				$sheet->row($row2, array('# emp', '',$emp_wt_hrs,'','', '', '','', '', ''));
				$sheet->cells('A'.$row2.':D'.$row2, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row3 = $i+3;
				$sheet->row($row3, array('# Emp with 0hrs', '',$emp,'','', '', '','', '', ''));
				$sheet->cells('A'.$row3.':D'.$row3, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});			
				$row4 = $i+4;
				$sheet->row($row4, array('Holiday Hours', '',$htotal_hours,'','', '', '','', '', ''));
				$sheet->cells('A'.$row4.':D'.$row4, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});	
                $row5 = $i+5;
				$sheet->row($row5, array('Holiday Pay', '','$'.$htotal_pays,'','', '', '','', '', ''));
				$sheet->cells('A'.$row5.':D'.$row5, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});	
                $row6= $i+6;
				$sheet->row($row6, array('Holiday Approved', '','$'.$htotal_Pay,'','', '', '','', '', ''));
				$sheet->cells('A'.$row6.':D'.$row6, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
                $row7 = $i+7;
				$sheet->row($row7, array('Payperiod',date("d-M-y", strtotime($xfrom_date)), date("d-M-y", strtotime($xto_date)),date("d-M-y", strtotime($paydate)),'','', '', '','', '', ''));
				$sheet->cells('A'.$row7.':D'.$row7, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#ffffff');
								$cells->setAlignment('center');
							});
				$kkk = $row7+1;
				if(!empty($supervisor_arr) && isset($supervisor_arr)){
					foreach($supervisor_arr as $supervisor_ar){
						$sheet->row($kkk, array($supervisor_ar['supervisor'],'Approved', $supervisor_ar['count'],'','','', '', '','', '', ''));
						$sheet->cells('A'.$kkk.':D'.$kkk, function ($cells) {
									$cells->setFontColor('#000000');
									$cells->setFontFamily('Calibri');
									$cells->setFontSize(14);
									$cells->setBackground('#dbeef3');
									$cells->setAlignment('center');
								});
						$kkk++;
					}
					
				} 
                $row11 = $kkk;
				$sheet->row($row11, array('Total Billed', '','$'.$total_billed,'','', '', '','', '', ''));
				$sheet->cells('A'.$row11.':D'.$row11, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							}); 
                $row12 = $kkk+1;
				$sheet->row($row12, array('Total Holiday', '','$'.$whtotal_billed,'','', '', '','', '', ''));
				$sheet->cells('A'.$row12.':D'.$row12, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});	
                 $row13 = $kkk+2;
				$sheet->row($row13, array('Total hrs', '',$total_hours,'','', '', '','', '', ''));
				$sheet->cells('A'.$row13.':D'.$row13, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row14 = $kkk+3;
				$sheet->row($row14, array('Total Pay', '','$'.$total_Pay,'','', '', '','', '', ''));
				$sheet->cells('A'.$row14.':D'.$row14, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});														
                
			}); 
		})->download('xlsx');
			
	
		
	}
	
	public function usearch_payperiod(Request $request)
    {

		
      //print_r($request->all());
	  //die();
	   
	   $bet_dates = explode('-',$request->search_by_payu);
		if(isset($bet_dates)){
			$from_dates    = $bet_dates[0];
			$to_dates    = $bet_dates[1];
		}
		else{
				$from_date  = "";
				$to_date = "";
			}
	  //print_r($bet_dates);
	  //die();
	 $xto_date = explode('_',$to_dates);
	$to_date = implode('-',$xto_date);
	$xfrom_date = explode('_',$from_dates);
	$from_date = implode('-',$xfrom_date);
	$search_by_comp = $request->search_by_compp;
		

		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			$user_companies = UserManager::where('users_id', '=', $search_by_comp)->get();
		
			
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$users_arrr[] = $user_company->musers_id;
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
								->where('hours_day', $from_dates)
								->whereIn('users_id', $user_arr)
								->distinct()->get(['users_id']);
		}else{
			$data = TimeSheet::with('companies')
								->whereBetween('hours_day', array($from_dates, $to_dates))
								->whereIn('users_id', $user_arr)
								->distinct()->get(['users_id']);
		}
		//dd($data);
		$total_hours = 0;
			$approved_hours = 0;
			$denied_hours = 0;
		$user_time = array();
		if(isset($data)){
			foreach($data as $datas){
				$user_time[] = $datas->users_id;
				$total_hours = $total_hours + $datas->hours_wrk;
					if($datas->approve == "2"){ 
							$approved_hours					  = $approved_hours + $datas->hours_wrk;
					}elseif($datas->approve == "1"){
						$denied_hours					  = $denied_hours + $datas->hours_wrk;
					}
			}
		}
		$users_data = User::with('companies')->whereIn('id', $user_time)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
		$ucount = 1;
		//dd($users_data);
		if(isset($users_data)){
			foreach($users_data as $user_data){
				$approved_by = $this->tapproved_by($user_data->id, $from_dates, $to_dates); 
					$color_info = $this->color_info($user_data->id); 
				
				
				
				if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					} 
				  echo "<td>".$ucount."</td>";
				  echo "<td>".$user_data->name."</td>";
				 
				  echo "<td>";
						echo '<a  href="'.url('/').'/users/'.$user_data->id.'/edit" title="Edit"><i class="fa fa-pencil"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/').'/user/changepassword/'.$user_data->id.'" title="Change Password"><i class="fa fa-unlock"></i></a>';
						echo '<a style="margin-left: 5px;cursor:pointer" data-baseURL="'.url('/').'" data-ID="'.$user_data->id.'" class="delete_user" title="Delete"><i class="fa fa-trash-o"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/user/timesheets').'/'.$user_data->id.'/'.$from_date.'/'.$to_date.'/'.$search_by_comp.'" title="Time Sheets"><i class="fa fa-book"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/user/driver/license').'/'.$user_data->id.'" title="Driver License"><i class="fa fa-drivers-license"></i></a>';
					   echo "</td>";
					    echo "<td>".$user_data->emp_id."</td>";
					   
					  echo "<td>";
					  if($user_data->status == 1 ){ echo "<h5 style='color:green'>Active</h5>"; }
					  else{ echo "<h5 style='color:red'>Inactive</h5>"; }
					  echo "</td>";
				  
 						echo "<td>".$user_data->hourst_rate."</td>";
				  echo "<td>";
				  if($user_data->last_login_at != null){ echo date('h:i a', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				   echo "<td>";
				  if($user_data->last_login_at != null){ echo date('M d, Y', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				  echo "<td>".date('M d, Y(D)', strtotime($user_data->created_at))."</td>";

					echo "<td>";
	$total_hours = $this->ttotal_time($user_data->id, $from_dates, $to_dates);
						if($total_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_hours."</p>";
						}elseif($total_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_hours."</p>";
						}
				  echo "</td>";
				   echo "<td>";
				   $approved_hours = $this->tapproved_time($user_data->id, $from_dates, $to_dates);
						if($approved_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_hours."</p>";
						}elseif($approved_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_hours."</p>";
						}
				  echo "</td>";
				   echo "<td>";
				   $denied_hours = $this->tdenied_time($user_data->id, $from_dates, $to_dates);
						if($denied_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_hours."</p>";
						}elseif($denied_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_hours."</p>";
						}
				  echo "</td>";
				  echo "<td>".$approved_by."</td>";
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
					 echo "<td>".$user_data->email."</td>";
					 if(Auth::user()->id == 72 || Auth::user()->id == 50 || Auth::user()->id == 282 ){
						 }else{
					  	 echo "<td>".$user_data->pass."</td>";
						 }
						 $user_companies = $this->user_companies($user_data->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
				 echo "<td>".$user_data->dept."</td>";
				 echo "</tr>";
				$ucount++;
			}
		}
		
	}
	public function search_payperiod(Request $request)
    {
		
      //print_r($request->all());
	  //die();
	   
	  
		$from_date    = explode('-', $request->frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $request->todate);
		$to_date = implode("_", $to_date);
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
		//dd($data);
		$total_hours = 0;
			$approved_hours = 0;
			$denied_hours = 0;
		$user_time = array();
		if(isset($data)){
			foreach($data as $datas){
				$user_time[] = $datas->users_id;
				$total_hours = $total_hours + $datas->hours_wrk;
					if($datas->approve == "2"){ 
							$approved_hours					  = $approved_hours + $datas->hours_wrk;
					}elseif($datas->approve == "1"){
						$denied_hours					  = $denied_hours + $datas->hours_wrk;
					}
			}
		}
		$users_data = User::with('companies')->whereIn('id', $user_time)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
		$ucount = 1;
		//dd($users_data);
		if(isset($users_data)){
			foreach($users_data as $user_data){
				$approved_by = $this->tapproved_by($user_data->id, $from_date, $to_date); 
					$color_info = $this->color_info($user_data->id); 
				
				
				
				if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					} 
				  echo "<td>".$ucount."</td>";
				  echo "<td>".$user_data->emp_id."</td>";
				  echo "<td>";
						echo '<a  href="'.url('/').'/users/'.$user_data->id.'/edit" title="Edit"><i class="fa fa-pencil"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/').'/user/changepassword/'.$user_data->id.'" title="Change Password"><i class="fa fa-unlock"></i></a>';
						echo '<a style="margin-left: 5px;cursor:pointer" data-baseURL="'.url('/').'" data-ID="'.$user_data->id.'" class="delete_user" title="Delete"><i class="fa fa-trash-o"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/user/timesheets').'/'.$user_data->id.'/'.$from_date.'/'.$to_date.'/'.$search_by_comp.'" title="Time Sheets"><i class="fa fa-book"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/user/driver/license').'/'.$user_data->id.'" title="Driver License"><i class="fa fa-drivers-license"></i></a>';
					   echo "</td>";
					   echo "<td>".$approved_by."</td>";
					  echo "<td>";
					  if($user_data->status == 1 ){ echo "<h5 style='color:green'>Active</h5>"; }
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
					  echo "<td>".$user_data->email."</td>";
				   echo "<td>".$user_data->name."</td>";
				   if(Auth::user()->id == 72 || Auth::user()->id == 50 || Auth::user()->id == 282 ){
						 }else{
					  	 echo "<td>".$user_data->pass."</td>";
						 }
				 echo "<td>".$user_data->dept."</td>";
				  $user_companies = $this->user_companies($user_data->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
 echo "<td>".$user_data->hourst_rate."</td>";
				  echo "<td>";
				  if($user_data->last_login_at != null){ echo date('h:i a', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				   echo "<td>";
				  if($user_data->last_login_at != null){ echo date('M d, Y', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				  echo "<td>".date('M d, Y(D)', strtotime($user_data->created_at))."</td>";

					echo "<td>";
	$total_hours = $this->ttotal_time($user_data->id, $from_date, $to_date);
						if($total_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_hours."</p>";
						}elseif($total_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_hours."</p>";
						}
				  echo "</td>";
				   echo "<td>";
				   $approved_hours = $this->tapproved_time($user_data->id, $from_date, $to_date);
						if($approved_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_hours."</p>";
						}elseif($approved_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_hours."</p>";
						}
				  echo "</td>";
				   echo "<td>";
				   $denied_hours = $this->tdenied_time($user_data->id, $from_date, $to_date);
						if($denied_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_hours."</p>";
						}elseif($denied_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_hours."</p>";
						}
				  echo "</td>";
				 echo "</tr>";
				$ucount++;
			}
		}
		
	}
	public function finace_search_payperiod(Request $request)
    {
		
      //print_r($request->all());
	  //die();
	   
	  
		$from_date    = explode('-', $request->frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $request->todate);
		$to_date = implode("_", $to_date);
		$search_by_comp = $request->search_by_comp;

		$TotalWorkHrs = 0;
		$TotalWorkPay = 0;
		$Total_billed =0;
		$Total_htotal_pays =0;
		$Total_holiday_hours =0;
		 $Total_htotal_pay= 0;
		$Total_with_holidays = 0;
		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			$user_companies = UserManager::where('users_id', '=', $search_by_comp)->get();
		
			
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$users_arrr[] = $user_company->musers_id;
				}
			}
			
		}
		$users = User::with('companies')->whereIn('id', $users_arrr)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
		$user_arr = array();
		$ucount = 1;
		$tablehtml = '';
		$ReturnArray = array();
		
		$ApprovedUserWithZerohrs = 0;
		$ApprovedUserWithOutZerohrs = 0;
		
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
										
					$approved_by = $this->approved_by($userss->id);
					$color_info = $this->color_info($userss->id); 
				}else{
					$data = TimeSheet::with('companies')
										->with('houses')
										->with('users')
										->whereBetween('hours_day', array($from_date, $to_date))
										->where('users_id', $userss->id)
										->orderBy('hours_day', 'DESC')
										->get();
					$approved_by = $this->tapproved_by($userss->id,$from_date, $to_date);
					$color_info = $this->color_info($userss->id); 
				}
				//dd($from_date);
				if(!empty($approved_by)){
					$approver_name = $approved_by;
				}else{
					$approver_name = "";
				}
				if(!empty($color_info)){
					$color_info = $color_info;
				}else{
					$color_info = "";
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
						//dd($datas);
						//$reg_hours = $datas->hours_wrk;
						$total_hours = $total_hours + $datas->hours_wrk;
						if($datas->approve == "2"){ 
								$approved_hours					  = $approved_hours + $datas->hours_wrk;
						}elseif($datas->approve == "1"){
							$denied_hours					  = $denied_hours + $datas->hours_wrk;
						}
						
					}
				  }
			
			 if(!empty($user_companies)){
				
					$user_company_name = $user_companies;
				}else{
					
					$user_company_name = "";
				}
			 // $user_company_name = $user_companies;
			  
			  
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
				$Total_holiday_hours = floatval($Total_holiday_hours) + floatval($holiday_tm);	
				if(isset($holiday_tm) && $holiday_tm > 0){
					$approved_hours = $approved_hours - $holiday_tm;
					$holiday_time = $holiday_tm;
				}else{
					$approved_hours = $approved_hours;
					$holiday_time = 0;
				}
			  $total_pay = $approved_hours * $userss->hourst_rate;
			  $TotalWorkPay = floatval($TotalWorkPay) + floatval($total_pay);
			  if($userss->hourst_rate > 0){
				  $billed_rate =$userss->hourst_rate + number_format("6",2);
			  }else{
				  $billed_rate = 0;
			  }
			  if($billed_rate > 0){
				 // $holiday_hourley =$billed_rate * number_format("1.5",2);
				 $holiday_hourley = $userss->hourst_rate * number_format("1.5",2);
			  }else{
				  $holiday_hourley = 0;
			  }
			  
			  $user_companies = $this->exp_user_companies($userss->id);
			  $total_billed = $approved_hours * $billed_rate;
			  $Total_billed= floatval($Total_billed) + floatval($total_billed);
			  $profit = $total_billed-$total_pay;
		      $htotal_pay = $holiday_time * $holiday_hourley;
			   $Total_htotal_pay= floatval($Total_htotal_pay) + floatval($htotal_pay);	
			  $Total_with_holiday = $htotal_pay + $total_billed;
			  $Total_with_holidays = floatval($Total_with_holidays) + floatval($Total_with_holiday);
			  $rate = $holiday_hourley + number_format("6",2);
			 // dd($total_billed);
			 // $rate = 0;
		     $htotal_pays=$holidy_hours * $holiday_hourley;
			 $Total_htotal_pays= floatval($Total_htotal_pays) + floatval($htotal_pays);	
			  $usersd = TimeSheet::where('users_id','=', $userss->id)->first();
			  
			  $holidy_hours = TimeSheet::where('users_id','=', $userss->id)
				   				   ->where('approve', '=', 2)
								   ->where('hours_day', $from_date)
								   ->whereIn('users_id', $user_arr)
								   ->sum('hours_wrk');
								
				$reg_hours = $approved_hours - $holiday_tm;				  
								 
			//echo $reg_hours;
				 //echo "<pre>";
				// print_r($supervisor_arr);
				// die;
				
				$approved_by = $this->tapproved_by($userss->id, $from_date, $to_date);
				$name  = strtoupper($approved_by);
				$words = explode(" ", $name);
				$firtsName = reset($words); 
					// echo substr($firtsName,0,1);
			    $last_name = !empty($words[1]) ? $words[1] : '';
					 
			    $approved_by = substr($firtsName,0,1). ' ' . $last_name;
				if($color_info != "") { 
					$tablehtml .= "<tr style='background:".$color_info."'>";
					}else{
						$tablehtml .= "<tr>";
					} 
				  $tablehtml .= "<td>".$ucount."</td>";
				  $tablehtml .= "<td>".$userss->ssn_no."</td>";
				   $tablehtml .= "<td>".$userss->first_name."</br><span>".$userss->last_name."</span></td>";
					   $tablehtml .= "<td>".$approved_by."</td>";
					 
				  $user_companies = $this->user_companies($userss->id);
				 $tablehtml .= "<td><ul class='comp_list'>".$user_companies."</ul></td>";
				// $tablehtml .= "<td>".$userss->dept."</td>";
                 $tablehtml .= "<td>".$userss->hourst_rate."</td>";
				  
				//   $tablehtml .= "<td>";
				  //if($userss->last_login_at != null){ $tablehtml .= date('M d, Y', strtotime($userss->last_login_at)); }
				  
				  //$tablehtml .= "</td>";
				  $tablehtml .= "<td>";
				  if($userss->last_login_at != null){ $tablehtml .= date('h:i a', strtotime($userss->last_login_at)); }
				  
				  $tablehtml .= "</td>";

					$tablehtml .= "<td>";
	$total_hours = $this->ttotal_time($userss->id, $from_date, $to_date);
	$TotalWorkHrs = floatval($TotalWorkHrs) + floatval($total_hours);
						if($total_hours <=  79){
							$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_hours."</p>";
						}elseif($total_hours == 80){
								$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_hours."</p>";
						}else{
						   $tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_hours."</p>";
						}
				  $tablehtml .= "</td>";
				   $tablehtml .= "<td>";
				   $approved_hours = $this->tapproved_time($userss->id, $from_date, $to_date);
				   if($approved_hours > 0){
						$ApprovedUserWithOutZerohrs++;
				   }else{
						$ApprovedUserWithZerohrs++;
				   }
						if($approved_hours <=  79){
							$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_hours."</p>";
						}elseif($approved_hours == 80){
								$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_hours."</p>";
						}else{
						   $tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_hours."</p>";
						}
				  $tablehtml .= "</td>";
				   $tablehtml .= "<td>";
				   $denied_hours = $this->tdenied_time($userss->id, $from_date, $to_date);
						if($denied_hours <=  79){
							$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_hours."</p>";
						}elseif($denied_hours == 80){
								$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_hours."</p>";
						}else{
						   $tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_hours."</p>";
						}
				  $tablehtml .= "</td>";
				 $tablehtml .= "</tr>";
			 $ucount++;
			}
		}
		
		$SummaryHtml = '';
		$SummaryHtml .= '<h5>Summary '.$from_date.' - '.$to_date.'</h5>
					<span class="tl-data">
					<label># emp</label>	<p style="display: inline-block" ;="">'.$ApprovedUserWithOutZerohrs.' </p></span>
					<span class="tl-data"><label>Emp with 0hrs	</label>	<p style="display: inline-block" ;="">'.$ApprovedUserWithZerohrs.' </p></span>
					<span class="tl-data"><label>Holiday Hours</label>	<p style="display: inline-block" ;="">'.$Total_holiday_hours.' </p></span>
					<span class="tl-data"><label>Holiday Pay</label>	<p style="display: inline-block" ;="">'.$Total_htotal_pay.' </p></span>
					
					<span class="tl-data"><label>Total Billed</label>	<p style="display: inline-block" ;="">	$'.$Total_billed.' </p></span>
					<span class="tl-data"><label>Total Holiday</label>	<p style="display: inline-block" ;="">	$'.$Total_with_holidays.'</p></span>
					<span class="tl-data"><label>Total hrs</label>	<p style="display: inline-block" ;="">'.$TotalWorkHrs.' </p></span>
					<span class="tl-data"><label>Total Pay</label>	<p style="display: inline-block" ;="">'.$TotalWorkPay.' </p></span>';
					
		$ReturnArray["tabledata"] = $tablehtml;
		$ReturnArray["empsummary"] = $SummaryHtml;
		echo json_encode($ReturnArray);
					
			
	}
	
	public function finace_payperiod(Request $request)
    {
		
    
	   
	   $bet_dates = explode('-',$request->search_by_pays);
		if(isset($bet_dates)){
			$from_dates    = $bet_dates[0];
			$to_dates    = $bet_dates[1];
		}
		else{
				$from_date  = "";
				$to_date = "";
			}
	  
	 $xto_date = explode('_',$to_dates);
	$to_date = implode('-',$xto_date);
	$xfrom_date = explode('_',$from_dates);
	$from_date = implode('-',$xfrom_date);
	$search_by_comp = $request->search_by_compf;
//dd($search_by_comp);
		$TotalWorkHrs = 0;
		$TotalWorkPay = 0;
		$Total_billed =0;
		$Total_htotal_pays =0;
		$Total_holiday_hours =0;
		 $Total_htotal_pay= 0;
		$Total_with_holidays = 0;
		$holiday_tm = 0;
		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			$user_companies = UserManager::where('users_id', '=', $search_by_comp)->get();
		//dd($user_companies);
			
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$users_arrr[] = $user_company->musers_id;
				}
			}
			
		}
		if(isset($users_arrr) && $users_arrr != 0){
			$user_t = TimeSheet::whereIn('users_id', $users_arrr)->orderBy('hours_wrk', 'ASC')->get();
		//dd($user_companies);
			
			if(isset($user_t)){
				foreach($user_t as $user_ts){
					$users_arrr[] = $user_ts->users_id;
				}
			}
			
		}

		$users = User::with('companies')->whereIn('id', $users_arrr)->where('role', '=', "user")->get();
		$user_arr = array();
		$ucount = 1;
		$tablehtml = '';
		$ReturnArray = array();
		
		$ApprovedUserWithZerohrs = 0;
		$ApprovedUserWithOutZerohrs = 0;
		
		if(isset($users)){
			 
			foreach($users as $userss){
				$user_arr[] = $userss->id;
				if($from_date == $to_date){
					$data = TimeSheet::with('companies')
										->with('houses')
										->with('users')
										->where('hours_day', $from_dates)
										->where('users_id', $userss->id)
										->orderBy('hours_day', 'DESC')
										->get();
										
					$approved_by = $this->approved_by($userss->id);
					$color_info = $this->color_info($userss->id); 
				}else{
					$data = TimeSheet::with('companies')
										->with('houses')
										->with('users')
										->whereBetween('hours_day', array($from_dates, $to_dates))
										->where('users_id', $userss->id)
										->orderBy('hours_day', 'DESC')
										->get();
					$approved_by = $this->tapproved_by($userss->id,$from_date, $to_date);
					$color_info = $this->color_info($userss->id); 
				}
				//dd($from_dates);
				if(!empty($approved_by)){
					$approver_name = $approved_by;
				}else{
					$approver_name = "";
				}
				if(!empty($color_info)){
					$color_info = $color_info;
				}else{
					$color_info = "";
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
						//dd($datas);
						//$reg_hours = $datas->hours_wrk;
						$total_hours = $total_hours + $datas->hours_wrk;
						if($datas->approve == "2"){ 
								$approved_hours					  = $approved_hours + $datas->hours_wrk;
						}elseif($datas->approve == "1"){
							$denied_hours					  = $denied_hours + $datas->hours_wrk;
						}
						
					}
				  }
			
			 if(!empty($user_companies)){
				
					$user_company_name = $user_companies;
				}else{
					
					$user_company_name = "";
				}
			 // $user_company_name = $user_companies;
			  
			  
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
					$Total_holiday_hours = floatval($Total_holiday_hours) + floatval($holiday_tm);
				if(isset($holiday_tm) && $holiday_tm > 0){
					$approved_hours = $approved_hours - $holiday_tm;
					$holiday_time = $holiday_tm;
				}else{
					$approved_hours = $approved_hours;
					$holiday_time = 0;
				}
			  $total_pay = $approved_hours * $userss->hourst_rate;
			  $TotalWorkPay = floatval($TotalWorkPay) + floatval($total_pay);
			  if($userss->hourst_rate > 0){
				  $billed_rate =$userss->hourst_rate + number_format("6",2);
			  }else{
				  $billed_rate = 0;
			  }
			  if($billed_rate > 0){
				 // $holiday_hourley =$billed_rate * number_format("1.5",2);
				 $holiday_hourley = $userss->hourst_rate * number_format("1.5",2);
			  }else{
				  $holiday_hourley = 0;
			  }
			  
			  $user_companies = $this->exp_user_companies($userss->id);
			  $total_billed = $approved_hours * $billed_rate;
			  $Total_billed= floatval($Total_billed) + floatval($total_billed);
			  $profit = $total_billed-$total_pay;
		      $htotal_pay = $holiday_time * $holiday_hourley;
			   $Total_htotal_pay= floatval($Total_htotal_pay) + floatval($htotal_pay);	
			  $Total_with_holiday = $htotal_pay + $total_billed;
			  $Total_with_holidays = floatval($Total_with_holidays) + floatval($Total_with_holiday);
			  $rate = $holiday_hourley + number_format("6",2);
			 // dd($total_billed);
			 // $rate = 0;
		     $htotal_pays=$holidy_hours * $holiday_hourley;
			 $Total_htotal_pays= floatval($Total_htotal_pays) + floatval($htotal_pays);	
			  $usersd = TimeSheet::where('users_id','=', $userss->id)->first();
			  
			  $holidy_hours = TimeSheet::where('users_id','=', $userss->id)
				   				   ->where('approve', '=', 2)
								   ->where('hours_day', $from_dates)
								   ->whereIn('users_id', $user_arr)
								   ->sum('hours_wrk');
								
				$reg_hours = $approved_hours - $holiday_tm;				  
								 
			//echo $reg_hours;
				 //echo "<pre>";
				// print_r($supervisor_arr);
				// die;
				
				$approved_by = $this->tapproved_by($userss->id, $from_dates, $to_dates);
				 $name  = strtoupper($approved_by);
				   $words = explode(" ", $name);
				    $firtsName = reset($words); 
					// echo substr($firtsName,0,1);
					 $last_name = !empty($words[1]) ? $words[1] : '';
					 
					 $approved_by = substr($firtsName,0,1). ' ' . $last_name;
				$total_hours = $this->ttotal_time($userss->id, $from_dates, $to_dates);
				 $denied_hours = $this->tdenied_time($userss->id, $from_dates, $to_dates);
				  $approved_hours = $this->tapproved_time($userss->id, $from_dates, $to_dates);
				if($total_hours > 0 ){
				if($color_info != "") { 
					$tablehtml .= "<tr style='background:".$color_info."'>";
					}else{
						$tablehtml .= "<tr>";
					} 
				  $tablehtml .= "<td>".$ucount."</td>";
				  $tablehtml .= "<td>".$userss->emp_id."</td>";
				  $tablehtml .= "<td>".$userss->first_name."</br><span>".$userss->last_name."</span></td>";
					   $tablehtml .= "<td>".$approved_by."</td>";
					 
				  $user_companies = $this->user_companies($userss->id);
				 $tablehtml .= "<td><ul class='comp_list'>".$user_companies."</ul></td>";
			//	 $tablehtml .= "<td>".$userss->dept."</td>";
                 $tablehtml .= "<td>".$userss->hourst_rate."</td>";
				  
				 //  $tablehtml .= "<td>";
				//  if($userss->last_login_at != null){ $tablehtml .= date('M d, Y', strtotime($userss->last_login_at)); }
				  
				//  $tablehtml .= "</td>";
				  $tablehtml .= "<td>";
				  if($userss->last_login_at != null){ $tablehtml .= date('h:i a', strtotime($userss->last_login_at)); }
				  
				  $tablehtml .= "</td>";

					$tablehtml .= "<td>";
	
	//dd($total_hours);
	
						if($total_hours <=  79){
							$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_hours."</p>";
						}elseif($total_hours == 80){
								$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_hours."</p>";
						}else{
						   $tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_hours."</p>";
						}
				  $tablehtml .= "</td>";
				   $tablehtml .= "<td>";
				  
				   $TotalWorkHrs = floatval($TotalWorkHrs) + floatval($approved_hours);
				   if($approved_hours > 0){
						$ApprovedUserWithOutZerohrs++;
				   }else{
						$ApprovedUserWithZerohrs++;
				   }
						if($approved_hours <=  79){
							$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_hours."</p>";
						}elseif($approved_hours == 80){
								$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_hours."</p>";
						}else{
						   $tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_hours."</p>";
						}
				  $tablehtml .= "</td>";
				   $tablehtml .= "<td>";
				  
						if($denied_hours <=  79){
							$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_hours."</p>";
						}elseif($denied_hours == 80){
								$tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_hours."</p>";
						}else{
						   $tablehtml .= "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_hours."</p>";
						}
				  $tablehtml .= "</td>";
				 $tablehtml .= "</tr>";
				}
			 $ucount++;
			}
		}
		
		$SummaryHtml = '';
		$SummaryHtml .= '<h5>Summary '.$from_date.' - '.$to_date.'</h5>
					<span class="tl-data">
					<label># emp</label>	<p style="display: inline-block" ;="">'.$ApprovedUserWithOutZerohrs.' </p></span>
					<span class="tl-data"><label>Emp with 0hrs	</label>	<p style="display: inline-block" ;="">'.$ApprovedUserWithZerohrs.' </p></span>
					<span class="tl-data"><label>Holiday Hours</label>	<p style="display: inline-block" ;="">'.$Total_holiday_hours.' </p></span>
					<span class="tl-data"><label>Holiday Pay</label>	<p style="display: inline-block" ;="">'.$Total_htotal_pay.' </p></span>
					
					<span class="tl-data"><label>Total Billed</label>	<p style="display: inline-block" ;="">	$'.$Total_billed.' </p></span>
					<span class="tl-data"><label>Total Holiday</label>	<p style="display: inline-block" ;="">	$'.$Total_with_holidays.'</p></span>
					<span class="tl-data"><label>Total hrs</label>	<p style="display: inline-block" ;="">'.$TotalWorkHrs.' </p></span>
					<span class="tl-data"><label>Total Pay</label>	<p style="display: inline-block" ;="">'.$TotalWorkPay.' </p></span>';
					
		$ReturnArray["tabledata"] = $tablehtml;
		$ReturnArray["empsummary"] = $SummaryHtml;
		echo json_encode($ReturnArray);
					
			
	}
	
	
	
	public function nsearch_payperiod(Request $request)
    {

		$from_date    = explode('-', $request->frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $request->todate);
		$to_date = implode("_", $to_date);
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
		$total_hours = 0;
			$approved_hours = 0;
			$denied_hours = 0;
		$user_time = array();
		if(isset($data)){
			foreach($data as $datas){
				$user_time[] = $datas->users_id;
				$total_hours = $total_hours + $datas->hours_wrk;
					if($datas->approve == "2"){ 
							$approved_hours					  = $approved_hours + $datas->hours_wrk;
					}elseif($datas->approve == "1"){
						$denied_hours					  = $denied_hours + $datas->hours_wrk;
					}
			}
		}
		$users_data = User::with('companies')->whereIn('id', $user_time)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
		$ucount = 1;
		
		if(isset($users_data)){
			foreach($users_data as $user_data){
				$approved_by = $this->tapproved_by($user_data->id, $from_date, $to_date); 
					$color_info = $this->color_info($user_data->id); 
				
				
				
				if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					} 
				  echo "<td>".$ucount."</td>";
				  echo "<td>".$user_data->emp_id."</td>";
				  echo "<td>";
						echo '<a  href="'.url('/').'/users/'.$user_data->id.'/edit" title="Edit"><i class="fa fa-pencil"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/').'/user/changepassword/'.$user_data->id.'" title="Change Password"><i class="fa fa-unlock"></i></a>';
						echo '<a style="margin-left: 5px;cursor:pointer" data-baseURL="'.url('/').'" data-ID="'.$user_data->id.'" class="delete_user" title="Delete"><i class="fa fa-trash-o"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/user/timesheets').'/'.$user_data->id.'/'.$from_date.'/'.$to_date.'/'.$search_by_comp.'" title="Time Sheets"><i class="fa fa-book"></i></a>';
						echo '<a  style="margin-left: 5px;"  href="'.url('/user/driver/license').'/'.$user_data->id.'" title="Driver License"><i class="fa fa-drivers-license"></i></a>';
					   echo "</td>";
					   echo "<td>".$approved_by."</td>";
					
				  $user_companies = $this->user_companies($user_data->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";

				  echo "<td>";
				  if($user_data->last_login_at != null){ echo date('h:i a', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				   echo "<td>";
				  if($user_data->last_login_at != null){ echo date('M d, Y', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				  echo "<td>".date('M d, Y(D)', strtotime($user_data->created_at))."</td>";

					echo "<td>";
	$total_hours = $this->ttotal_time($user_data->id, $from_date, $to_date);
						if($total_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_hours."</p>";
						}elseif($total_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_hours."</p>";
						}
				  echo "</td>";
				   echo "<td>";
				   $approved_hours = $this->tapproved_time($user_data->id, $from_date, $to_date);
						if($approved_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_hours."</p>";
						}elseif($approved_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_hours."</p>";
						}
				  echo "</td>";
				   echo "<td>";
				   $denied_hours = $this->tdenied_time($user_data->id, $from_date, $to_date);
						if($denied_hours <=  79){
							echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_hours."</p>";
						}elseif($denied_hours == 80){
								echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_hours."</p>";
						}else{
						   echo "<p style='text-align: center;font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_hours."</p>";
						}
						 /*
				  echo "</td>";
				  $vacc_report = $this->vacc_report($user_data->id); 
										if(isset($vacc_report) && !empty($vacc_report)){
												echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'>".$vacc_report[0]."</td>";
												echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'>".date('M d', strtotime($vacc_report[1]))."-".date('M d, Y', strtotime($vacc_report[2]))."</td>";
													if($vacc_report[3]==0){echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'><p style='padding: 10px;
    text-align: left;
    color: #000;
    background: yellow;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Pending</p></td>";}elseif($vacc_report[3]==1){echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'> <p style='padding: 10px;
    text-align: left;
    color: #fff;
    background: green;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Approved</p></td>";}elseif($vacc_report[3]==2){echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'><p style='padding: 10px;
    text-align: left;
    color: #fff;
    background: red;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Decline</p></td>";}else{echo "<td style='background: blue;color: #fff;font-size: 20px;font-weight: bold;'><p style='padding: 10px;
    text-align: left;
    color: #000;
    background: yellow;
    font-size: 16px;
    font-weight: bold;
    border-radius: 10px;'>Pending</p></td>";}
												

										}*/
				 echo "</tr>";
				$ucount++;
			}
		}
		
	}
	
	public function allexport_data($frmdate,$todate,$search_by_comp)
    {	
		
        $from_date    = explode('-', $frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $todate);
		$to_date = implode("_", $to_date);
		$paydate = date("M d", strtotime('+5 days', strtotime($todate)));
		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			$user_companies = UserManager::where('users_id', '=',$search_by_comp)->get();
			
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
		
		$users = User::with('companies')->whereIn('id', $users_arrr)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
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
										
					$approved_by = $this->approved_by($userss->id);
					$color_info = $this->color_info($userss->id); 
				}else{
					$data = TimeSheet::with('companies')
										->with('houses')
										->with('users')
										->whereBetween('hours_day', array($from_date, $to_date))
										->where('users_id', $userss->id)
										->orderBy('hours_day', 'DESC')
										->get();
					$approved_by = $this->tapproved_by($userss->id,$from_date, $to_date);
					$color_info = $this->color_info($userss->id); 
				}
				
				if(!empty($approved_by)){
					$approver_name = $approved_by;
				}else{
					$approver_name = "";
				}
				if(!empty($color_info)){
					$color_info = $color_info;
				}else{
					$color_info = "";
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
		
			// print_r($approved_hours);

			 if(!empty($user_companies)){
				
					$user_company_name = $user_companies;
				}else{
					
					$user_company_name = "";
				}
			 // $user_company_name = $user_companies;
			  
			  
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
					$reg_hours = $approved_hours - $holiday_tm;
					$holiday_time = $holiday_tm;
				}else{
					$reg_hours = $approved_hours;
					$holiday_time = 0;
				}
			  $total_pay = $reg_hours * $userss->hourst_rate;
			  
			  if($userss->hourst_rate > 0){
				  $billed_rate =$userss->hourst_rate + number_format("6",2);
			  }else{
				  $billed_rate = 0;
			  }
			  if($billed_rate > 0){
				 // $holiday_hourley =$billed_rate * number_format("1.5",2);
				 $holiday_hourley = $userss->hourst_rate * number_format("1.5",2);
			  }else{
				  $holiday_hourley = 0;
			  }
			  
			  $user_companies = $this->exp_user_companies($userss->id);
			  $total_billed = $reg_hours * $billed_rate;
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
								
						  
							  
				$reg_pay = $reg_hours * $userss->hourst_rate;		
				$t_pay = $reg_pay + $htotal_pay;					  	
				$breg_pay = $reg_hours * $billed_rate;
				$bhh_pay = $holiday_tm * $rate;		
				$bt_pay = $breg_pay + $bhh_pay;	

                 			
			//echo $ht_pay;
				 //echo "<pre>";
				// print_r($supervisor_arr);
				// die;
			  
			  $time_sheet[] = array(

						'#' => $user_count,
						'SSN' => $userss->emp_id,
						'Last Name'   => $userss->last_name,
						'First Name'   => $userss->first_name,
						'Company'   => $user_companies,
						'Reg Hours' => $reg_hours,
						'Holiday Hours'   => $holiday_tm,
						'Total Hours'   => $approved_hours,
						'Hourley Rate'   => '$'.$userss->hourst_rate,
						'Holiday Rate'   => '$'.$holiday_hourley,
						'Total Pay'   => $t_pay,
						'Billed Rate'   => '$'.$billed_rate,
						'Rate'          => '$'.$rate,
						'Total Billed'   => $bt_pay,
						'approver_name' => $approver_name,
						'approver_color' => $color_info,
						'Holiday Pay' => $htotal_pay,
						'Total Holiday' => $Total_with_holiday,
						
						
					);			  	
				$user_count++;					
			}
		}
		// die();
		//$user_company_name = "";
		//$time_sheet = "";
		$stitle = $user_company_name." Timesheet details for ".$paydate;
		//$stitle1 = "Employee Details";
		Excel::create('Time Sheet', function($excel) use ($time_sheet,$stitle,$paydate,$frmdate,$todate){
			$excel->setTitle('Time Sheet');
			$excel->sheet('Time Sheet', function($sheet) use ($time_sheet,$stitle,$paydate,$frmdate,$todate){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
				
				$sheet->row(1, array('','', '','','',$stitle,'', '','', '', '','', '', '',''));
				$sheet->row(2, array('','', '','','','', '','','Employee Details', '','','Client Billing Details','','',''));
				$sheet->row(3, array('#','SSN', 'Last Name','First Name', 'Company','Reg Hours','Holiday Hours','Total Hours', 'Hourley Rate','Holiday Rate', 'Total Pay($)', 'Billed Rate','Rate','Total Billed($)', 'Approved By'));
				$sheet->cell('P1', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('P2', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('P3', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('Q1', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('Q2', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('Q3', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('R1', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('R2', function($cell) {
							$cell->setValue('');
						});
				$sheet->cell('R3', function($cell) {
							$cell->setValue('');
						});
				$sheet->cells('A1:O1', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(22);
								$cells->setAlignment('center');
							});
				$sheet->Cells('I2:K2', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#FCD5B4');
								$cells->setAlignment('center');
							});
				$sheet->Cells('L2:N2', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('##D8D8D8');
								$cells->setAlignment('center');
							});			
				$sheet->cells('A3:O3', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#BDB76B');
								$cells->setAlignment('center');
							});			
				$i = 4;$j = 0;$k = 0;$l = 0;$m = 0;$n = 0;$o = 0;$p = 0; $q = 0; $r = 0;$s = 0;$t = 0;$u = 0;$v = 0;$w = 0;$x = 0; $y = 0; $z = 0;$emp = 0;$emp_wt_hrs = 0;
				$total_hours = 0;
				$t_Pay = 0;
				$total_billed = 0;
				$htotal_hours = 0;
				$htotal_pay = 0;
				$htotal_pays = 0;
				$whtotal_billed = 0;
				$bt_pay = 0;
				
				foreach ($time_sheet as $cleans) {

					$sheet->row($i, array($cleans['#'], $cleans['SSN'], $cleans['Last Name'],$cleans['First Name'], $cleans['Company'], $cleans['Reg Hours'],  $cleans['Holiday Hours'], $cleans['Total Hours'],$cleans['Hourley Rate'],$cleans['Holiday Rate'], $cleans['Total Pay'], $cleans['Billed Rate'], $cleans['Rate'], $cleans['Total Billed'], $cleans['approver_name']));
				
					if($cleans['approver_color'] != ""){

						$sheet->cell('P'.$i, function($cell) {
							$cell->setValue('');
						});
						$sheet->cell('R'.$i, function($cell) {
							$cell->setValue('');
						});
						$bgcolor = $cleans['approver_color'];
						$sheet->cells('A'.$i.':O'.$i, function ($cells) use ($bgcolor) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground($bgcolor);
							$cells->setAlignment('center');
						});
					}else{

						$sheet->cell('P'.$i, function($cell) {
							$cell->setValue('');
						});
						$sheet->cell('R'.$i, function($cell) {
							$cell->setValue('');
						});
						$sheet->cells('A'.$i.':O'.$i, function ($cells) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground('#ffffff');
							$cells->setAlignment('center');
						});
					}
					if($cleans['approver_name'] == "Vladimir Ndebugre"){
						$j++;
					}elseif($cleans['approver_name'] == "Holly Wolfe"){
						$k++;
					}elseif($cleans['approver_name'] == "Regina Quartey"){
						$l++;
					}elseif($cleans['approver_name'] == "Long Caitlin"){
						$m++;
					}elseif($cleans['approver_name'] == "Emmanuel ndyia"){
						$o++;
					}elseif($cleans['approver_name'] == "John Seshie"){
						$p++;
					}elseif($cleans['approver_name'] == "Onbridges"){
						$q++;
					}elseif($cleans['approver_name'] == "Owura Kusi"){
						$r++;
					}elseif($cleans['approver_name'] == "Kasim Sulemana"){
						$s++;
					}elseif($cleans['approver_name'] == "William Kesson"){
						$t++;
					}
					if($cleans['Total Hours'] > 0){
						$emp_wt_hrs++;
					}else{
						$emp++;
					}
					$total_hours += (float)$cleans['Total Hours'];
					$t_Pay += (float)$cleans['Total Pay'];
					$htotal_hours += (float)$cleans['Holiday Hours'];
					$htotal_pay  += (float)$cleans['Holiday Pay'];
					$bt_pay += (float)$cleans['Total Billed'];
					$total_billed += (float)$cleans['Total Holiday'];
					$i++;
				}
				$supervisor_arr = array();
				
				if($j > 0){
					$supervisor_arr[] = array('supervisor' => 'Vladimir', 'count' => $j);
				}
				if($k > 0){
					$supervisor_arr[] = array('supervisor' => 'Holly', 'count' => $k);
				}
				if($l > 0){
					$supervisor_arr[] = array('supervisor' => 'Regina' , 'count'=> $l);
				}
				if($m > 0){
					$supervisor_arr[] = array('supervisor' => 'Long' , 'count'=> $m);
				}
				if($o > 0){
					$supervisor_arr[] = array('supervisor' => 'Emmanuel' , 'count'=> $o);
				}
				if($p > 0){
					$supervisor_arr[] = array('supervisor' => 'John' , 'count'=> $p);
				}
				if($q > 0){
					$supervisor_arr[] = array('supervisor' => 'Onbridges' , 'count'=> $q);
				}
				if($r > 0){
					$supervisor_arr[] = array('supervisor' => 'Owura' , 'count'=> $r);
				}
				if($s > 0){
					$supervisor_arr[] = array('supervisor' => 'Kasim' , 'count'=> $s);
				}
				if($t > 0){
					$supervisor_arr[] = array('supervisor' => 'William' , 'count'=> $t);
				}
				// echo $s;
				// echo "<pre>";
				// print_r($supervisor_arr);
				// die;
			//sheet->row($i, array('','', '','','Sum', '', '$'.$htotal_hours,',$total_hours,'','',$'.$total_Pay,'','','$'.$total_billed));
				$sheet->row($i, array('','', '','','Sum', '','',$total_hours,'','', '$'.$t_Pay, '','', '$'.$bt_pay));
				$sheet->cells('E'.$i.':O'.$i, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setFontWeight('bold');
								$cells->setBackground('#fcd5b4');
								$cells->setAlignment('center');
							});
				$row1 = $i+1;	
				$sheet->row($row1, array('','Summary', '','','','', '', '','', '', ''));
				$sheet->cells('A'.$row1.':D'.$row1, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setFontWeight('bold');
								$cells->setBackground('#ffffff');
								$cells->setAlignment('center');
								
							});
				
				$row2 = $i+2;
				$sheet->row($row2, array('# emp', '',$emp_wt_hrs,'','', '', '','', '', ''));
				$sheet->cells('A'.$row2.':D'.$row2, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row3 = $i+3;
				$sheet->row($row3, array('# Emp with 0hrs', '',$emp,'','', '', '','', '', ''));
				$sheet->cells('A'.$row3.':D'.$row3, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});			
				$row4 = $i+4;
				$sheet->row($row4, array('Holiday Hours', '',$htotal_hours,'','', '', '','', '', ''));
				$sheet->cells('A'.$row4.':D'.$row4, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});	
                $row5 = $i+5;
				$sheet->row($row5, array('Holiday Pay', '','$'.$htotal_pay,'','', '', '','', '', ''));
				$sheet->cells('A'.$row5.':D'.$row5, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});	
                $row6= $i+6;
				$sheet->row($row6, array('Holiday Approved', '','$'.$htotal_pays,'','', '', '','', '', ''));
				$sheet->cells('A'.$row6.':D'.$row6, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							}); 
                $row7 = $i+7;
				$sheet->row($row7, array('payperiod',date("d-M-y", strtotime($frmdate)), date("d-M-y", strtotime($todate)),date("d-M-y", strtotime($paydate)),'','', '', '','', '', ''));
				$sheet->cells('A'.$row7.':D'.$row7, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#ffffff');
								$cells->setAlignment('center');
							});
				$kkk = $row7+1;
				if(!empty($supervisor_arr) && isset($supervisor_arr)){
					foreach($supervisor_arr as $supervisor_ar){
						$sheet->row($kkk, array($supervisor_ar['supervisor'],'Approved', $supervisor_ar['count'],'','','', '', '','', '', ''));
						$sheet->cells('A'.$kkk.':D'.$kkk, function ($cells) {
									$cells->setFontColor('#000000');
									$cells->setFontFamily('Calibri');
									$cells->setFontSize(14);
									$cells->setBackground('#dbeef3');
									$cells->setAlignment('center');
								});
						$kkk++;
					}
					
				} 
                $row11 = $kkk;
				$sheet->row($row11, array('Total Billed', '','$'.$bt_pay,'','', '', '','', '', ''));
				$sheet->cells('A'.$row11.':D'.$row11, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							}); 
                $row12 = $kkk+1;
				$sheet->row($row12, array('Total Holiday', '','$'.$htotal_pay,'','', '', '','', '', ''));
				$sheet->cells('A'.$row12.':D'.$row12, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});	
                 $row13 = $kkk+2;
				$sheet->row($row13, array('Total hrs', '',$total_hours,'','', '', '','', '', ''));
				$sheet->cells('A'.$row13.':D'.$row13, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row14 = $kkk+3;
				$sheet->row($row14, array('Total Pay', '','$'.$t_Pay,'','', '', '','', '', ''));
				$sheet->cells('A'.$row14.':D'.$row14, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});							
			
				
				
				
			}); 
		})->download('xlsx');
    }
	
	
	public function allexport_data_new($frmdate,$todate,$search_by_comp)
    {	
		$holidays = array( '11/25/2021',
							'12/25/2021',
							'01/01/2021',
							'05/31/2021',
							'07/04/2021',
							'09/06/2021',
							
							);
        $from_date    = explode('-', $frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $todate);
		$to_date = implode("_", $to_date);
		$paydate = date("M d", strtotime('+5 days', strtotime($todate)));
		$users_arrr = array();
		if(isset($search_by_comp) && $search_by_comp != 0){
			$user_companies = UserManager::where('users_id', '=',$search_by_comp)->get();
		
			
			if(isset($user_companies)){
				foreach($user_companies as $user_company){
					$users_arrr[] = $user_company->musers_id;
				}
			}
			
		}
		
		$users = User::with('companies')->whereIn('id', $users_arrr)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
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
										
					$approved_by = $this->approved_by($userss->id);
					$color_info = $this->color_info($userss->id); 
				}else{
					$data = TimeSheet::with('companies')
										->with('houses')
										->with('users')
										->whereBetween('hours_day', array($from_date, $to_date))
										->where('users_id', $userss->id)
										->orderBy('hours_day', 'DESC')
										->get();
					$approved_by = $this->tapproved_by($userss->id,$from_date, $to_date);
					$color_info = $this->color_info($userss->id); 
				}
				
				if(!empty($approved_by)){
					$approver_name = $approved_by;
				}else{
					$approver_name = "";
				}
				if(!empty($color_info)){
					$color_info = $color_info;
				}else{
					$color_info = "";
				}
				
				$count = 1;
				$total_hours = 0;
				$approved_hours = 0;
				$denied_hours = 0;
				  if($data->count() != 0){
					foreach ($data as $datas){
						$total_hours = $total_hours + $datas->hours_wrk;
						if($datas->approve == "2"){ 
								$approved_hours					  = $approved_hours + $datas->hours_wrk;
						}elseif($datas->approve == "1"){
							$denied_hours					  = $denied_hours + $datas->hours_wrk;
						}
						
					}
				  }
			
			 
			  $user_company_name = $user_companies;
			  
			  
			  // Holiday Hours
				$cxto_date = explode('_',$to_date);
				$cxto_date = implode('-',$cxto_date);
				$cxfrom_date = explode('_',$from_date);
				$cxfrom_date = implode('-',$cxfrom_date);
				$holiday_dt = "";
				$holiday_dt_arr = array();
				if(isset($holidays)){
					foreach($holidays as $holiday){
						$holiday = new DateTime($holiday);
						$cto_date = new DateTime($cxto_date);
						$cfrom_date  = new DateTime($cxfrom_date);
						if (
						  $holiday->format('y-m-d') >= $cfrom_date->format('y-m-d') && 
						  $holiday->format('y-m-d') <= $cto_date->format('y-m-d')){
						  $holiday_dt = $holiday->format('Y/m/d');
						  $holiday_dt_arr[] = $holiday->format('Y_m_d');
						}
					}
					
				}
				$holiday_tm = 0; 
				$holiday_count = 1;
				
				if(isset($holiday_dt_arr)){
						foreach($holiday_dt_arr as $holiday_dt_ar){
								$holiday_dt_ar = explode('/',$holiday_dt_ar);
								$holiday_dt_ar = implode('_',$holiday_dt_ar);
								$holiday_time  = TimeSheet::where('users_id','=', $userss->id)
										->where('approve', '=', 2)
										->where('hours_day','=', $holiday_dt_ar)
										->orderBy('created_at', 'DESC')
										->first();
										
										if(isset($holiday_time)){
											$holiday_tm = $holiday_time->hours_wrk + $holiday_tm;
										}
						}
					}
				if(isset($holiday_tm) && $holiday_tm > 0){
					$approved_hours = $approved_hours - $holiday_tm;
					$holiday_time = $holiday_tm;
				}else{
					$approved_hours = $approved_hours;
					$holiday_time = 0;
				}
			  $total_pay = $approved_hours * $userss->hourst_rate;
			  if($userss->hourst_rate > 0){
				  $billed_rate =$userss->hourst_rate + number_format("6",2);
			  }else{
				  $billed_rate = 0;
			  }
			  if($billed_rate > 0){
				  $holiday_hourley =$billed_rate + number_format("6",2);
			  }else{
				  $holiday_hourley = 0;
			  }
			  $user_companies = $this->exp_user_companies($userss->id);
			  $total_billed = $approved_hours * $billed_rate;
			  $profit = $total_billed-$total_pay;
			  $htotal_pay = $holiday_time * $holiday_hourley;
			  $Total_with_holiday = $htotal_pay + $total_billed;
			  
			  
			  $time_sheet[] = array(

						'#' => $user_count,
						'Emp ID' => $userss->emp_id,
						'Last Name'   => $userss->last_name,
						'First Name'   => $userss->first_name,
						'Company'   => $user_companies,
						'Total Hours'   => $approved_hours,
						'Hourley Rate'   => '$'.$userss->hourst_rate,
						'Holiday Hourley'   => '$'.$holiday_hourley,
						'Billed $18'   => '$'.$billed_rate,
						'Total Pay'   => $total_pay,
						'Total Billed'   => $total_billed,
						'approver_name' => $approver_name,
						'approver_color' => $color_info,
						'Holiday Hours'   => $holiday_time,
						'Holiday Pay' => $htotal_pay,
						'Total Holiday' => $Total_with_holiday,
						
						
					);			  	
				$user_count++;					
			}
		}
		
		$stitle = $user_company_name." Timesheet details for ".$paydate;
		Excel::create('Time Sheet', function($excel) use ($time_sheet,$stitle,$paydate,$frmdate,$todate){
			$excel->setTitle('Time Sheet');
			$excel->sheet('Time Sheet', function($sheet) use ($time_sheet,$stitle,$paydate,$frmdate,$todate){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
				
				$sheet->row(1, array('','', '','','',$stitle,'', '','', '', '','', '', '',''));
				$sheet->row(2, array('#','Emp ID', 'Last Name','First Name', 'Company','Total Hours', 'Hourley Rate','Holiday Hourley', 'Billed $18', 'Total Pay($)', 'Total Billed($)','Approved By','Holiday Hours','Holiday Pay','Total Holiday'));
				$sheet->cells('A1:O1', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(22);
								$cells->setAlignment('center');
							});
				$sheet->cells('A2:O2', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#BDB76B');
								$cells->setAlignment('center');
							});
				$i = 3;$j = 0;$k = 0;$l = 0;$m = 0;$n = 0;$o = 0;$p = 0; $q = 0; $r = 0;$s = 0;$t = 0;$u = 0;$v = 0;$w = 0;$x = 0; $y = 0; $z = 0;$emp = 0;$emp_wt_hrs = 0;
				$total_hours = 0;
				$total_Pay = 0;
				$total_billed = 0;
				$htotal_hours = 0;
				$htotal_Pay = 0;
				$whtotal_billed = 0;
				foreach ($time_sheet as $cleans) {

					$sheet->row($i, array($cleans['#'], $cleans['Emp ID'], $cleans['Last Name'],$cleans['First Name'], $cleans['Company'], $cleans['Total Hours'],$cleans['Hourley Rate'],$cleans['Holiday Hourley'], $cleans['Billed $18'], $cleans['Total Pay'], $cleans['Total Billed'], $cleans['approver_name'], $cleans['Holiday Hours'], $cleans['Holiday Pay'], $cleans['Total Holiday']));
				
					if($cleans['approver_color'] != ""){

						$sheet->cell('P'.$i, function($cell) {
							$cell->setValue('');
						});
						$bgcolor = $cleans['approver_color'];
						$sheet->cells('A'.$i.':O'.$i, function ($cells) use ($bgcolor) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground($bgcolor);
							$cells->setAlignment('center');
						});
					}else{

						$sheet->cell('P'.$i, function($cell) {
							$cell->setValue('');
						});
						$sheet->cells('A'.$i.':O'.$i, function ($cells) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground('#ffffff');
							$cells->setAlignment('center');
						});
					}
					if($cleans['approver_name'] == "Vladimir Ndebugre"){
						$j++;
					}elseif($cleans['approver_name'] == "Holly Wolfe"){
						$k++;
					}elseif($cleans['approver_name'] == "Regina Quartey"){
						$l++;
					}elseif($cleans['approver_name'] == "Long Caitlin"){
						$m++;
					}elseif($cleans['approver_name'] == "Emmanuel ndyia"){
						$o++;
					}elseif($cleans['approver_name'] == "John Seshie"){
						$p++;
					}elseif($cleans['approver_name'] == "Onbridges"){
						$q++;
					}elseif($cleans['approver_name'] == "Owura Kusi"){
						$r++;
					}elseif($cleans['approver_name'] == "Kasim Sulemana"){
						$s++;
					}elseif($cleans['approver_name'] == "William Kesson"){
						$t++;
					}
					if($cleans['Total Hours'] > 0){
						$emp_wt_hrs++;
					}else{
						$emp++;
					}
					$total_hours += (float)$cleans['Total Hours'];
					$total_Pay += (float)$cleans['Total Pay'];
					$htotal_hours += (float)$cleans['Holiday Hours'];
					$htotal_Pay += (float)$cleans['Holiday Pay'];
					$whtotal_billed += (float)$cleans['Total Billed'];
					$total_billed += (float)$cleans['Total Holiday'];
					$i++;
				}
				$supervisor_arr = array();
				
				if($j > 0){
					$supervisor_arr[] = array('supervisor' => 'Vladimir', 'count' => $j);
				}
				if($k > 0){
					$supervisor_arr[] = array('supervisor' => 'Holly', 'count' => $k);
				}
				if($l > 0){
					$supervisor_arr[] = array('supervisor' => 'Regina' , 'count'=> $l);
				}
				if($m > 0){
					$supervisor_arr[] = array('supervisor' => 'Long' , 'count'=> $m);
				}
				if($o > 0){
					$supervisor_arr[] = array('supervisor' => 'Emmanuel' , 'count'=> $o);
				}
				if($p > 0){
					$supervisor_arr[] = array('supervisor' => 'John' , 'count'=> $p);
				}
				if($q > 0){
					$supervisor_arr[] = array('supervisor' => 'Onbridges' , 'count'=> $q);
				}
				if($r > 0){
					$supervisor_arr[] = array('supervisor' => 'Owura' , 'count'=> $r);
				}
				if($s > 0){
					$supervisor_arr[] = array('supervisor' => 'Kasim' , 'count'=> $s);
				}
				if($t > 0){
					$supervisor_arr[] = array('supervisor' => 'William' , 'count'=> $t);
				}
				// echo $s;
				// echo "<pre>";
				// print_r($supervisor_arr);
				// die;
				$sheet->row($i, array('','', '','','Sum', $total_hours, '','','', '$'.$total_Pay, '$'.$whtotal_billed,'', '$'.$htotal_hours, '$'.$htotal_Pay, '$'.$total_billed));
				$sheet->cells('E'.$i.':O'.$i, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setFontWeight('bold');
								$cells->setBackground('#fcd5b4');
								$cells->setAlignment('center');
							});
				$row1 = $i+1;	
				$sheet->row($row1, array('','Summary', '','','','', '', '','', '', ''));
				$sheet->cells('A'.$row1.':D'.$row1, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setFontWeight('bold');
								$cells->setBackground('#ffffff');
								$cells->setAlignment('center');
								
							});
				
				$row2 = $i+2;
				$sheet->row($row2, array('payperiod',date("d-M-y", strtotime($frmdate)), date("d-M-y", strtotime($todate)),date("d-M-y", strtotime($paydate)),'','', '', '','', '', ''));
				$sheet->cells('A'.$row2.':D'.$row2, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#ffffff');
								$cells->setAlignment('center');
							});
				$kkk = $row2+1;
				if(!empty($supervisor_arr) && isset($supervisor_arr)){
					foreach($supervisor_arr as $supervisor_ar){
						$sheet->row($kkk, array($supervisor_ar['supervisor'],'Approved', $supervisor_ar['count'],'','','', '', '','', '', ''));
						$sheet->cells('A'.$kkk.':D'.$kkk, function ($cells) {
									$cells->setFontColor('#000000');
									$cells->setFontFamily('Calibri');
									$cells->setFontSize(14);
									$cells->setBackground('#dbeef3');
									$cells->setAlignment('center');
								});
						$kkk++;
					}
					
				}
				$row6 = $kkk;
				$sheet->row($row6, array('Total hrs', '',$total_hours,'','', '', '','', '', ''));
				$sheet->cells('A'.$row6.':D'.$row6, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row7 = $kkk+1;
				$sheet->row($row7, array('Total Pay', '','$'.$total_Pay,'','', '', '','', '', ''));
				$sheet->cells('A'.$row7.':D'.$row7, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row8 = $kkk+2;
				$sheet->row($row8, array('Total Billed', '','$'.$whtotal_billed,'','', '', '','', '', ''));
				$sheet->cells('A'.$row8.':D'.$row8, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row9 = $kkk+3;
				$sheet->row($row9, array('Holiday Hours', '',$htotal_hours,'','', '', '','', '', ''));
				$sheet->cells('A'.$row9.':D'.$row9, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row10 = $kkk+4;
				$sheet->row($row10, array('Holiday Pay', '','$'.$htotal_Pay,'','', '', '','', '', ''));
				$sheet->cells('A'.$row10.':D'.$row10, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row11 = $kkk+5;
				$sheet->row($row11, array('Total Holiday', '','$'.$total_billed,'','', '', '','', '', ''));
				$sheet->cells('A'.$row11.':D'.$row11, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row12 = $kkk+6;
				$sheet->row($row12, array('# emp', '',$emp_wt_hrs,'','', '', '','', '', ''));
				$sheet->cells('A'.$row12.':D'.$row12, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				$row13 = $kkk+7;
				$sheet->row($row13, array('# Emp with 0hrs', '',$emp,'','', '', '','', '', ''));
				$sheet->cells('A'.$row13.':D'.$row13, function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#dbeef3');
								$cells->setAlignment('center');
							});
				
				
				
			});
		})->download('xlsx');
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
								//->orderBy(sum('hours_wrk'), 'DESC')
								//->get();
								->sum('hours_wrk');
								
		}else{
			$total_time = TimeSheet::with('companies')
								->whereBetween('hours_day', array($from_date, $to_date))
								->where('users_id', '=', $id)
								//->orderBy(sum('hours_wrk'), 'DESC')
								//->get();
								->sum('hours_wrk');
		}
	//	print_r($total_time);
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
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		
		$users_arrr = array();
		$users = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->get();
		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}

		
		if($frm_date != "" && $t_date != ""){
			$total_time = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($frm_date, $t_date))
								->sum('hours_wrk');
		}else{
			$total_time = "";
		}

		return $total_time;
    }
	
	public static function approved_time($id)
    {
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		
		$users_arrr = array();
		$users = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->get();
		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}

		if($frm_date != "" && $t_date != ""){
			$approved_time = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($frm_date, $t_date))
								->where('approve', '=', 2)
								->sum('hours_wrk');
		}else{
			$approved_time = "";
		}
        // $approved_time = TimeSheet::where('users_id', '=', $id)->where('approve', '=', 2)->sum('hours_wrk');
		return $approved_time;
    }
	
	public static function denied_time($id)
    {
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		$users_arrr = array();
		$users = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->get();
		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}

		if($frm_date != "" && $t_date != ""){
			$denied_time = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($frm_date, $t_date))
								->where('approve', '=', 1)
								->sum('hours_wrk');
		}else{
			$denied_time = "";
		}
        // $denied_time = TimeSheet::where('users_id', '=', $id)->where('approve', '=', 1)->sum('hours_wrk');
		return $denied_time;
    }
	
	
	public static function supervisor_info($id)
    {
        $company_id = Company::where('company', 'LIKE', '%' . $id . '%')->first();
		$UserManager = UserManager::where('users_id', '=', $company_id->id)->first();
		$user = User::where('id' ,'=', $UserManager->musers_id)->first();
		$supervisor = $user->first_name;
		return $supervisor;
    }
	
	public function driver_license($id){
		$users = User::where('id', '=', $id)->first();
		$drivers_license = $users->drivers_license;
		return view('admin.users.driving_license',compact('drivers_license','id'));
	}
	
	
		
	public function dates(){
		$begin = new DateTime( "2020-06-22" );
		$end   = new DateTime( "2021-06-30" );
		
		for($i = $begin; $i <= $end; $i->modify('+1 day')){ 
			$pay_be = $i;
			$pay_begin = $pay_be->format("Y-m-d");
			$pay_end   = $pay_be->add(new DateInterval('P13D'));
			for($j = $pay_be; $j <= $pay_end; $j->modify('+1 day')){ 
					$date = $j;
					$weekendDay = false;
					$day = $date->format("D");
					if($day == 'Sat' || $day == 'Sun'){
						$weekendDay = true;
					}
					if($weekendDay){
						echo $date->format("Y-m-d") . ' falls on the weekend.<br>';
					} else{
						echo $date->format("Y-m-d") . ' falls on a weekday.<br>';
					}
			}
			echo "<br>";
			$pay_date   = $pay_end->add(new DateInterval('P5D'));
			$i->modify("-5 day");
			
		}
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
	
	
	public static function approved_by($id)
    {
		
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}

		if($frm_date != "" && $t_date != ""){
			 $frm_date    = explode('-', $frm_date);
						$frm_date = implode("_", $frm_date);
						$t_date    = explode('-', $t_date);
					   $t_date = implode("_", $t_date);
			$approved_by = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($frm_date, $t_date))
								->where('approve', '=', 2)
								->get();
								
		}
		$approved_users = array();
		
		if(isset($approved_by)){
			foreach($approved_by as $approved_bys){
				$approved_users[] = $approved_bys->approved_by;
			}
			
		}
		
		$app_users = User::with('companies')->whereIn("id", $approved_users)->orderBy('name', 'ASC')->get();
		$user_name = "";
		// $user_detail = array();
		$count = 1;
		if(isset($app_users)){
			foreach($app_users as $app_user){
				if($count > 1){
					$user_name .= ", ".$app_user->name;
				}else{
					$user_name .= $app_user->name;
				}
				// $user_detail[] = $user_name;
				// $user_detail[] = $app_user->color_field;
				$count++;
			}
			
		}
		return $user_name;
	}
	
	
	public static function tapproved_by($id, $from_date, $to_date)
    {

		if($from_date != "" && $to_date != ""){
			$approved_by = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($from_date, $to_date))
								->where('approve', '=', 2)
								->get();
		}
		$approved_users = array();
		if(isset($approved_by)){
			foreach($approved_by as $approved_bys){
				$approved_users[] = $approved_bys->approved_by;
			}
			
		}
		
		$app_users = User::with('companies')->whereIn("id", $approved_users)->orderBy('name', 'ASC')->get();
		$user_name = "";
		// $user_detail = array();
		$count = 1;
		if(isset($app_users)){
			foreach($app_users as $app_user){
				if($count > 1){
					$user_name .= ", ".$app_user->name;
				}else{
					$user_name .= $app_user->name;
				}
				// $user_detail[] = $user_name;
				// $user_detail[] = $app_user->color_field;
				$count++;
			}
			
		}
        // $approved_time = TimeSheet::where('users_id', '=', $id)->where('approve', '=', 2)->sum('hours_wrk');
		return $user_name;
		
    }
	
	public static function super_info($id)
    {
		
		$company = UserManager::where('musers_id', $id)->first();
		$users = User::where("role", "=", "supervisor")->get();
		
		$supervisor_id = array();
		
		if(isset($users)){
			foreach($users as $user){
				$superviso = UserManager::where('users_id', $company->users_id)->where('musers_id', $user->id)->first();
				$supervisor_id[] = $superviso->musers_id;
			}
		}
			// $bg_color = $data->color_field;

		return $supervisor_id;
		
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
	public static function vacc_report($id){
		$data_user_vacc = UserVaccatioStatusn::where('user_id','=', $id)->orderBy('created_at', 'DESC')->first();
	//	dd($data_user_vacc);die();
		$arr_val = array();
		if(isset($data_user_vacc)){
			$vacc_frm    = explode('_', $data_user_vacc->vacc_start);
			$vacc_frm = implode("-", $vacc_frm);
			$vacc_to    = explode('_', $data_user_vacc->vacc_end);
			$vacc_to = implode("-", $vacc_to);
			$date1 = new DateTime(date('m/d/y', strtotime($vacc_frm)));
			$date2 = new DateTime(date('m/d/y', strtotime($vacc_to)));
			$vacc_frm1 = $vacc_frm;
			$vacc_to1    = $vacc_to;
			$diff = $date2->diff($date1);

			$days = $diff->days;
			$hours = $diff->h;
			$hours = $hours + ($diff->days*24);
			$hours = floatval(8*$days);
			$arr_val[] = $hours;
			$arr_val[] = $vacc_frm1;
			$arr_val[] = $vacc_to1;
			$arr_val[] = $data_user_vacc->vacc_status;
		}
		return $arr_val;

	}
}
