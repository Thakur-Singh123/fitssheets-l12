<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\EmpNotfication;
use App\EmpNotificationRel;
use App\User;
use App\AdminMeta;
use App\Company;
use App\Department;
use App\UserManager;
use DB;

class EmpNotificationController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {
		$data = EmpNotfication::orderBy('created_at', 'DESC')->get();
		return view('admin.notitfication.not_view',compact('data'));
    }
	
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$companies = Company::orderBy('created_at', 'DESC')->get();
		$user = User::where("role", "=", "user")->orderBy('created_at', 'DESC')->get();
        return view('admin.notitfication.not_add',compact('user','companies'));
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
			'not_title'    =>  'required',
			'not_text'    =>  'required',
		];
		$customMessages = [
			'not_title'    =>  'Please Add the Notification Title',
			'not_text'    =>  'Please Add the Notification Text',
		];
		$this->validate($request, $rules, $customMessages);
				
		$form_data = array(
				'not_title' => $request->not_title,
				'not_text' => $request->not_text,
		);
		
		$EmpNotfication = EmpNotfication::create($form_data);
		$last_emp_id = DB::getPdo()->lastInsertId();
		$users_arrr = array();
		if(isset($request->company_id)){
			foreach($request->company_id as $company_ids){
				$user_companies = UserManager::where('users_id', '=', $company_ids)->get();
				if(isset($user_companies)){
					foreach($user_companies as $user_company){
						$users_arrr[] = $user_company->musers_id;
					}
				}
			}
		}
			
		if(isset($request->users_id)){
			foreach($request->users_id as $users){
				$users_arrr[] = $users;
			}
		}
		
		
		
		if(isset($users_arrr)){
			$users_arrr = array_unique($users_arrr);
			
			// echo "<pre>";
			// print_r($users_arrr);
			// die;
			foreach($users_arrr as $users_arr){
				$form_data = array(
					'users_id' => $users_arr,
					'emp_notfications_id' => $last_emp_id,
				);
				$EmpNotificationRel = EmpNotificationRel::create($form_data);
			}
		}
		
			
		if($EmpNotfication){
			return redirect('/notifications')->with(['success' => 'Notification Created Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while creating Notification!!']);
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
		$companies = Company::orderBy('created_at', 'DESC')->get();
		$user = User::where("role", "=", "user")->orderBy('created_at', 'DESC')->get();
		$data = EmpNotfication::where('id', '=', $id)->get();
		$EmpNotfication_id = EmpNotificationRel::where('emp_notfications_id', '=', $id)->get();
		return view('admin.notitfication.not_edit',compact('data','user','EmpNotfication_id','companies'));
		
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
			'not_title'    =>  'required',
			'not_text'    =>  'required',
		];
		$customMessages = [
			'not_title'    =>  'Please Add the Notification Title',
			'not_text'    =>  'Please Add the Notification Text',
		];
		$this->validate($request, $rules, $customMessages);
				
		$form_data = array(
				'not_title' => $request->not_title,
				'not_text' => $request->not_text,
		);
		
		
		$EmpNotfication = EmpNotfication::whereId($request->hidden_id)->update($form_data);
		
		$users_arrr = array();
		if(isset($request->company_id)){
			foreach($request->company_id as $company_ids){
				$user_companies = UserManager::where('users_id', '=', $company_ids)->get();
				if(isset($user_companies)){
					foreach($user_companies as $user_company){
						$users_arrr[] = $user_company->musers_id;
					}
				}
			}
		}
			
		if(isset($request->users_id)){
			foreach($request->users_id as $users){
				$users_arrr[] = $users;
			}
		}
		if(isset($users_arrr)){
			$users_arrr = array_unique($users_arrr);
			$data_delte = EmpNotificationRel::where("emp_notfications_id", "=", $request->hidden_id)->delete();
			foreach($users_arrr as $users_arr){
				$form_data = array(
					'users_id' => $users_arr,
					'emp_notfications_id' => $request->hidden_id,
				);
				$EmpNotificationRel = EmpNotificationRel::create($form_data);
			}
		}	
		if($EmpNotfication){
			return redirect('/notifications')->with(['success' => 'Notification Updated Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while updating Notification!!']);
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
        $data = EmpNotfication::findOrFail($id);
        $data->delete();
		$data_delte = EmpNotificationRel::where("emp_notfications_id", "=", $id)->delete();
    }

}
