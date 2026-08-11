<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\UserVaccation;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Models\TimeSheet;
use Excel;
use App\Models\User;
use App\Models\AdminMeta;
use App\Models\Company;
use App\Models\Department;
use App\Models\UserManager;
use DateTime;
use App\Models\Payperiods;
use App\Models\UserSupervisorRel;
use App\Models\UserCasemanagerRel;

class VaccationController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
	 
    public function index() {
		$data = UserVaccation::orderBy('created_at', 'DESC')->paginate(10);
		return view('admin.vaccation.vacc_view',compact('data'));
    }
	
	/**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function create() {
    	$users = User::where("role", "=", "user")->orderBy('created_at', 'DESC')->get();
        return view('admin.vaccation.vacc_add',compact('users'));
    }
	/**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function store(Request $request) {
		//Validate input fields
		$request->validate([
			'user_id' => 'required',
			'vacc_sl' => 'required',
		]);
		// $rules = [
		// 	'vacc_sl'     =>  'required',
		// ];
		// $customMessages = [
		// 	'vacc_sl'     =>  'required',
		// ];
		// $this->validate($request, $rules, $customMessages);
				
		$vacc_frm    = explode('-', $request->vacc_frm);
		$vacc_frm = implode("_", $vacc_frm);
		$vacc_to    = explode('-', $request->vacc_to);
		$vacc_to = implode("_", $vacc_to);
		
		$form_data = array(
			'user_id' => $request->user_id,
			'vacc_sl' => $request->vacc_sl,
			'vacc_vc' => $request->vacc_vc,
			'vacc_be' => $request->vacc_be,
			'vacc_jd' => $request->vacc_jd,
			'vacc_frm' => $vacc_frm,
			'vacc_to' => $vacc_to,
		);
		$user_store = UserVaccation::create($form_data);
		if($user_store){
			return redirect('/vaccations')->with(['success' => 'Vaccation created successfully.']);
		}else{
			return redirect()->back()->with(['succeerrorss' => 'Error while creating Vaccation!!']);
		}	
    }
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id) {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id) {
    	$users = User::where("role", "=", "user")->orderBy('created_at', 'DESC')->get();
		$data = UserVaccation::where('id', '=', $id)->get();
		return view('admin.vaccation.vacc_edit',compact('data','users'));
    }
	
	/**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function update(Request $request)  {
		//Validate input fields
		$request->validate([
			'user_id' => 'required',
			'vacc_sl' => 'required',
		]);
		// $rules = [
		// 	'vacc_sl'     =>  'required',
		// ];
		// $customMessages = [
		// 	'vacc_sl'     =>  'required',
		// ];
		// $this->validate($request, $rules, $customMessages);
		$vacc_frm    = explode('-', $request->vacc_frm);
		$vacc_frm = implode("_", $vacc_frm);
		$vacc_to    = explode('-', $request->vacc_to);
		$vacc_to = implode("_", $vacc_to);
		$form_data = array(
				'user_id' => $request->user_id,
				'vacc_sl' => $request->vacc_sl,
				'vacc_vc' => $request->vacc_vc,
				'vacc_be' => $request->vacc_be,
				'vacc_jd' => $request->vacc_jd,
				'vacc_frm' => $vacc_frm,
				'vacc_to' => $vacc_to,
		);
		$user_update = UserVaccation::whereId($request->hidden_id)->update($form_data);

		if($user_update) {
			return redirect('/vaccations')->with(['success' => 'Vaccation updated successfully.']);
		}else {
			return redirect()->back()->with(['success' => 'Error while updating Company!!']);
		}
    }
	
	/**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

	//Function for delete vocation
    public function destroy(Request $request) {
		//Get ajax request for vocation id
		$vocation_id = $request->vocation_id;
		//Delete vocation
		$is_delete_vocation = UserVaccation::where('id', $vocation_id)->delete();
		//Check if vocation record deleted or not
		if($is_delete_vocation){
			echo '<p style="color:green;">Vocation record deleted successfully.</p>';
			// Corrected JavaScript code
			echo '<script>setTimeout(function(){ window.location.href = ""; }, 3000);</script>';
		} else {
			echo '<p style="color:green;">Oops something wrong.</p>';
		}
    }
	
    public static function user($id) {
    	$user = User::where('id', '=', $id)->first();
    	$user = $user->name;
    	return $user;
    }
}
