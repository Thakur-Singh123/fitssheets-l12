<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Excel;
use App\TimeSheet;
use App\ListProblem;
use App\User;
use App\House;
use App\Company;
use DateTime;
use Carbon\Carbon;


class ListsProblemController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {
		$user = Auth::user()->id;
		$datas = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $user)->orderBy('hours_day', 'DESC')->paginate(15);
		
		$data = ListProblem::orderBy('created_at', 'DESC')->get();
		$list_date = array();
		$list_data = array();
		$issue_arr = array();
		if(isset($data)){
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
		if(isset($list_date) && isset($list_data)){
			$list_date = array_unique($list_date);
			foreach($list_date as $list_dates){
				$arr = array();
				foreach($list_data as $list_datas){
					if($list_dates == $list_datas['created_at']){
						$arr[] = $list_datas;
					}

				}
				$issue_arr[] = array(
					$list_dates => $arr,
				);
			}
		}
		// dd($issue_arr);
		return view('admin.list.ls_view',compact('issue_arr'));
    }

    public function create()
    {
		$user = Auth::user()->id;
		$Company = Auth::user()->companies_id;
		$name = Auth::user()->name;
		$ssn = Auth::user()->ssn_no;
		 
		 $companies = Company::orderBy('display_order', 'ASC')->get();
	//	$companies = Company::orderBy('created_at', 'DESC')->get();
		if($Company == "Quantumleap, Inc"){
			$houses = House::where('companies_id', '=', $Company)->orderBy('created_at', 'DESC')->get();
		}else{
			$Com = array();
			$Com[] = "Quantumleap, Inc";
			$houses = House::whereNotIn('companies_id', $Com)->orderBy('created_at', 'DESC')->get();
		}
		//die();
        return view('admin.list.ls_add', compact('companies','user','houses','name','ssn'));
    }
	
	 /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		 $user_id = Auth::user()->id;
		 $name = Auth::user()->name;
		 $ssn = Auth::user()->ssn_no;
		// $hours = $request->hours_day;
		
		$rules = [
			'company_id'     =>  'required',
			'issue'    =>  'required',
			
		];
	
	
		$customMessages = [
			'company_id'     =>  'Please select company',
			'issue'     =>  'Please add issue',
			
		];
		$this->validate($request, $rules, $customMessages);
			

        

		$user = User::where('id', '=', $user_id)->first();

		
 

		
		    $form_data = array(
				'companies_id' => $request->company_id,
				'ssn' => $ssn,
				'user_id' => $user_id,
				'name'  => $name,
				'issue'  => $request->issue,
				'resolution_remarks'  => $request->resolution_remarks,
			
		);
		
		//dd($form_data);
		$ls_store = ListProblem::create($form_data);
			
		if($ls_store){
			return redirect('/lists-issue')->with(['success' => 'Notes Added Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while Adding Issue!!']);
		}
		
    }
	
	 public function edit($id)
    {
		$user = Auth::user()->id;
		$Company = Auth::user()->companies_id;
		$name = Auth::user()->name;
		$ssn = Auth::user()->ssn_no;
		//$companies = Company::orderBy('created_at', 'DESC')->get();
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
    public function update(Request $request)
    {
		$user = Auth::user()->id;
        $name = Auth::user()->name;
		$ssn = Auth::user()->ssn_no;
		$rules = [
			
			'issue'    =>  'required',
			
		];
	
	
		$customMessages = [
			
			'issue'     =>  'Please add issue',
			
		];
		//$this->validate($request, $rules, $customMessages);
		$this->validate($request, $rules, $customMessages);
	
		$form_data = array(
				
				'issue'  => $request->issue,
				'resolution_remarks'  => $request->resolution_remarks,
		);
		//dd($form_data);
		$ls_update = ListProblem::whereId($request->hidden_id)->update($form_data);

		if($ls_update){
			return redirect('/lists-issue')->with(['success' => 'Notes Updated Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while updating Issue!!']);
		}
    }

   public function destroy($id)
   {
        $data = ListProblem::findOrFail($id);
        $data->delete();
   }
	
   public function approve($id)
   {
		$data = ListProblem::findOrFail($id);
		$data->status = 1; //Approved
		$data->save();
		return redirect('lists-issue')->with(['success' => 'List point checked!!']);

   }
   public function decline($id)
   {
	   $data = ListProblem::findOrFail($id);
	   $data->status = 0; //Declined
	   $data->save();
		return redirect('lists-issue')->with(['success' => 'List point unchecked!!']); //Redirect user somewhere
   }
   public static function company($id)
   {
	   $data = Company::findOrFail($id);
	   $company = "";
	   if(isset($data)){
	   	$company = $data->company;
	   }
	   return $company;
   }
	
	
}
