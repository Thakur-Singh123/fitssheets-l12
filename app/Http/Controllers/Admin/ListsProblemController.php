<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Excel;
use App\Models\TimeSheet;
use App\Models\ListProblem;
use App\Models\User;
use App\Models\House;
use App\Models\Company;
use DateTime;
use Carbon\Carbon;

class ListsProblemController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

	//Function for all problem list
    public function index() {
		//Get auth detail
		$user = Auth::user()->id;
		//Get timesheet
		$datas = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $user)->orderBy('hours_day', 'DESC')->paginate(15);
		//Get lists
		$data = ListProblem::orderBy('created_at', 'DESC')->paginate(10);
		$list_date = array();
		$list_data = array();
		$issue_arr = array();
		if(isset($data)) {
			foreach ($data as $key => $value) {
				$list_date[] = $value->created_at->format('y_m_d');
				$list_data[] = array(
					'id' => $value->id,
					'name' => $value->name,
					'ssn'  => $value->ssn,
					'company' => $value->companies_id,
					'issue' => $value->issue,
					'status'  => $value->status,
					'resolution' => $value->resolution_remarks,
					'created_at' => $value->created_at->format('y_m_d'),
				);
			}
		}
		if(isset($list_date) && isset($list_data)) {
			$list_date = array_unique($list_date);
			foreach($list_date as $list_dates){
				$arr = array();
				foreach($list_data as $list_datas){
					if($list_dates == $list_datas['created_at']) {
						$arr[] = $list_datas;
					}
				}
				$issue_arr[] = array(
					$list_dates => $arr,
				);
			}
		}
		return view('admin.list.ls_view',compact('issue_arr','data'));
    }

	//Functio for create isssu
    public function create() {
		//Get auth 
		$user = Auth::user()->id;
		$Company = Auth::user()->companies_id;
		$name = Auth::user()->name;
		$ssn = Auth::user()->ssn_no;
		//Get companies
		$companies = Company::orderBy('display_order', 'ASC')->get();
		if($Company == "Quantumleap, Inc") {
			$houses = House::where('companies_id', '=', $Company)->orderBy('created_at', 'DESC')->get();
		} else {
			$Com = array();
			$Com[] = "Quantumleap, Inc";
			$houses = House::whereNotIn('companies_id', $Com)->orderBy('created_at', 'DESC')->get();
		}
        return view('admin.list.ls_add', compact('companies','user','houses','name','ssn'));
    }

	/**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function store(Request $request) {
		//Get auth id
		$user_id = Auth::user()->id;
		$name = Auth::user()->name;
		$ssn = Auth::user()->ssn_no;
		//Validate input fields
		$request->validate([
			'company_id' => 'required',
			'issue' => 'required',
		]);
		// //$hours = $request->hours_day;
		// $rules = [
		// 	'company_id' =>'required',
		// 	'issue' =>'required',
		// ];
		// $customMessages = [
		// 	'company_id' =>'Please select company',
		// 	'issue' =>'Please add issue',
			
		// ];
		// $this->validate($request, $rules, $customMessages);
		//Get user
		$user = User::where('id', '=', $user_id)->first();
		//Create 
		$form_data = array(
			'companies_id' => $request->company_id,
			'ssn' => $ssn,
			'user_id' => $user_id,
			'name'  => $name,
			'issue'  => $request->issue,
			'resolution_remarks'  => $request->resolution_remarks,
		);
		//Store
		$ls_store = ListProblem::create($form_data);
	    //Check if issu craeted or not 
		if($ls_store){
			return redirect('/lists-issue')->with(['success' => 'Issue created successfully.']);
		}else{
			return redirect()->back()->with(['success' => 'Error while Adding Issue!']);
		}
		
    }
	
	//Function for edit issue
	public function edit($id) {
		//Get auth
		$user = Auth::user()->id;
		$Company = Auth::user()->companies_id;
		$name = Auth::user()->name;
		$ssn = Auth::user()->ssn_no;
		//Get company
		$companies = Company::orderBy('display_order', 'ASC')->get();
		$data = ListProblem::where('id', '=', $id)->get();
		return view('admin.list.ls_edit',compact('data','companies','user','ssn','name'));
    }
	
	/**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

	//Function for update problem
    public function update(Request $request) {
		//Get auth
		$user = Auth::user()->id;
        $name = Auth::user()->name;
		$ssn = Auth::user()->ssn_no;
		$request->validate([
			'issue' => 'required',
		]);
		// $rules = [
		// 	'issue' =>'required',
		// ];
		// $customMessages = [
		// 	'issue' =>'Please add issue',
		// ];
		// //$this->validate($request, $rules, $customMessages);
		// $this->validate($request, $rules, $customMessages);
	    //form data
		$form_data = array(
			'issue'  => $request->issue,
			'resolution_remarks'  => $request->resolution_remarks,
		);
		//Update data
		$ls_update = ListProblem::whereId($request->hidden_id)->update($form_data);
        //Check if issue updated or not
		if($ls_update) {
			return redirect('/lists-issue')->with(['success' => 'Issue updated successfully.']);
		} else {
			return redirect()->back()->with(['success' => 'Error while updating Issue!']);
		}
    }
	
	//Function for approved
	public function approve($id) {
		//Get problem
		$data = ListProblem::findOrFail($id);
		$data->status = 1;
		$data->save();
		return redirect('lists-issue')->with(['success' => 'Issue approve successfully.']);
	}

	//Function for decline
	public function decline($id) {
		$data = ListProblem::findOrFail($id);
		$data->status = 0;
		$data->save();
		return redirect('lists-issue')->with(['success' => 'Issue decline successfully.']);
	}

	// //Function for delete
	// public function destroy($id) {
	// 	$data = ListProblem::findOrFail($id);
	// 	$data->delete();
	// }

	//Function for delete issue
    public function destroy(Request $request) {
		//Get ajax request for issue id
		$issue_id = $request->issue_id;
		//Delete issue
		$is_delete_issue= ListProblem::where('id', $issue_id)->delete();
		//Check if issue record deleted or not
		if($is_delete_issue){
			echo '<p style="color:green;">Issue record deleted successfully.</p>';
			// Corrected JavaScript code
			echo '<script>setTimeout(function(){ window.location.href = ""; }, 3000);</script>';
		} else {
			echo '<p style="color:green;">Oops something wrong.</p>';
		}
    }

	//Function for company
	public static function company($id)	{
		//Get company
		$data = Company::findOrFail($id);
		$company = "";
		if(isset($data)){
			$company = $data->company;
		}
		return $company;
	}
}
