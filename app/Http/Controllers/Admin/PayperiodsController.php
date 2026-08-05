<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\models\Company;
use App\models\Payperiods;
use Carbon\Carbon;

class PayperiodsController extends Controller
{
	/*
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

	//Function for index
    public function index() {
		//Get payperiods
		$data = Payperiods::with('companies')->orderBy('created_at', 'DESC')->paginate(10);
		return view('admin.payperiods.payperiod_view',compact('data'));
    }
	
	/**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

	//Function for create payperiod
    public function create() {
		//Get companies
		$companies = Company::orderBy('display_order', 'ASC')->get();
        return view('admin.payperiods.payperiod_add', compact('companies'));
    }
	
	/**
	* Store a newly created resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/

	//Function for store payperiod
    public function store(Request $request) {
		//validate input fileds
		$request->validate([
			'company_id' => 'required',
		]);
		//Get payperiod
		$test = Payperiods::latest('id')->first();
		$pay_dates = explode('-',$test->payperiod_value);
		if(isset($pay_dates)){
			$from_date    = $pay_dates[0];
			$to_date    = $pay_dates[1];
		}
		$xto_date = explode('_',$to_date);
		$xto_date = implode('-',$xto_date);
		$xfrom_date = explode('_',$from_date);
		$xfrom_date = implode('-',$xfrom_date);
		$xpto_date = date('Y_m_d', strtotime($xto_date. ' + 14 days'));
		$xpfrom_date = date('Y_m_d', strtotime($xfrom_date. ' + 14 days'));
		$pay_value= $xpfrom_date.'-'.$xpto_date;
		
	    $todata= strtotime($xto_date. ' + 14 days');
         $topay_data= date('d-M-y', $todata);
        $frmdata= strtotime($xfrom_date. ' + 14 days');
		$tofrm_data= date('d-M', $frmdata);
		$pay_title= $tofrm_data.' '.'to'.' '.$topay_data;
        $companies = $test->companies_id;
		
		//dd($comp);
		//dd($companies);
		
		/*$rules = [
			'payperiod_title'    =>  'required',
		];
		$customMessages = [
			'payperiod_title'    =>  'Please Add the Payperiod Title',
		];
		$this->validate($request, $rules, $customMessages);
		*/	
			$form_data = array(
				'companies_id' => $request->company_id,
				//'companies_id' => '7',
				'payperiod' => $pay_title,
				'payperiod_value' => $pay_value,
		);
		//Create payperiod
		$paypeirod_store = Payperiods::create($form_data);
		//Check if payperiod created or not
		if($paypeirod_store){
			return redirect('/payperiods')->with(['success' => 'Payperiod created successfully.']);
		}else{
			return redirect()->back()->with(['error' => 'Error while creating Payperiod!!']);
		}
    }
	
	//Function for astore
	public function astore(Request $request) {
		//Get payperiod
		$test = Payperiods::latest('id')->first();
		$pay_dates = explode('-',$test->payperiod_value);
		if(isset($pay_dates)){
			$from_date    = $pay_dates[0];
			$to_date    = $pay_dates[1];
		}
		$xto_date = explode('_',$to_date);
		$xto_date = implode('-',$xto_date);
		$xfrom_date = explode('_',$from_date);
		$xfrom_date = implode('-',$xfrom_date);
		$xpto_date = date('Y_m_d', strtotime($xto_date. ' + 14 days'));
		$xpfrom_date = date('Y_m_d', strtotime($xfrom_date. ' + 14 days'));
		$pay_value= $xpfrom_date.'-'.$xpto_date;
		
	    $todata= strtotime($xto_date. ' + 14 days');
         $topay_data= date('d-M-y', $todata);
        $frmdata= strtotime($xfrom_date. ' + 14 days');
		$tofrm_data= date('d-M', $frmdata);
		$pay_title= $tofrm_data.' '.'to'.' '.$topay_data;
        $companies = $test->companies_id;
		$comp = $companies +1;
		//dd($comp);
		//dd($companies);
		/*$rules = [
			'payperiod_title'    =>  'required',
		];
		$customMessages = [
			'payperiod_title'    =>  'Please Add the Payperiod Title',
		];
		$this->validate($request, $rules, $customMessages);
		*/	
        if($companies == 15){
			$form_data = array(
				//'companies_id' => $request->company_id,
				'companies_id' => '15',
				'payperiod' => $pay_title,
				'payperiod_value' => $pay_value,
		);
		}else{
			$form_data = array(
				//'companies_id' => $request->company_id,
				'companies_id' => '15',
				'payperiod' => $pay_title,
				'payperiod_value' => $pay_value,
		);
		}
		//Create payperiod
		$paypeirod_store = Payperiods::create($form_data);
		//Check if payperiod created or not	
		if($paypeirod_store){
			return redirect('/payperiods')->with(['success' => 'Payperiod created successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while creating Payperiod!!']);
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

	//Function for edit payperiod
    public function edit($id) {
		//Get companies
		$companies = Company::orderBy('display_order', 'ASC')->get();
		//Get payperiods
		$data = Payperiods::where('id', '=', $id)->get();
		return view('admin.payperiods.payperiod_edit',compact('data','companies'));
    }
	
	/**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

	//Function for update payperiods
    public function update(Request $request) {
		//validate input fileds
		$request->validate([
			'payperiod_title' => 'required',
		]);
		// $rules = [
		// 	'payperiod_title'    =>  'required',
		// ];
		// $customMessages = [
		// 	'payperiod_title'    =>  'Please Add the Payperiod Title',
		// ];
		// $this->validate($request, $rules, $customMessages);
		$form_data = array(
			'companies_id' => $request->company_id,
			'payperiod' => $request->payperiod_title,
			'payperiod_value' => $request->payperiod_value,
		);
		//Update payperiod
		$paypeirod_store = Payperiods::whereId($request->hidden_id)->update($form_data);
        //Check if payperiod updated or not
		if($paypeirod_store){
			return redirect('/payperiods')->with(['success' => 'Payperiod updated successfully.']);
		}else{
			return redirect()->back()->with(['error' => 'Error while updating Payperiod!!']);
		}
    }
	
	/**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

	//Function for delete payperiod
    public function destroy(Request $request) {
		//Get ajax request for payperiod id
		$payperiod_id = $request->payperiod_id;
		//Delete payperiods
		$is_delete_payperiod = Payperiods::where('id', $payperiod_id)->delete();
		//Check if payperiod record deleted or not
		if($is_delete_payperiod){
			echo '<p style="color:green;">Payperiod record deleted successfully.</p>';
			// Corrected JavaScript code
			echo '<script>setTimeout(function(){ window.location.href = ""; }, 3000);</script>';
		} else {
			echo '<p style="color:green;">Oops something wrong.</p>';
		}
    }
}
