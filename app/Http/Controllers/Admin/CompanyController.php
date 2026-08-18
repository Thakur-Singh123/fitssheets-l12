<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class CompanyController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index() {
    //Get compnay
    $data = Company::orderBy('created_at', 'DESC')->paginate(10);
    return view('admin.company.company_view',compact('data'));
  }
	
	/**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function create() {
    return view('admin.company.company_add');
  }
	
  /**
  * Store a newly created resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */

  public function store(Request $request) {
    //Validate input filed
    $request->validate([
      'name' => 'required',
      'company' => 'required',
    ]);
    //$rules = [
    //'name'     =>  'required|string|max:255',
    //'company'    =>  'required',
    //];
    //$customMessages = [
    //'name'     =>  'Please add company name',
    //   'company'    =>  'Please add company',
    // ];
    // $this->validate($request, $rules, $customMessages);
    $form_data = array(
        'name' => $request->name,
        'company' => $request->company,
    );
    //Create company
    $user_store = Company::create($form_data);
    //Check if company created or not
    if($user_store) {
      return redirect('/companies')->with(['success' => 'Company created successfully.']);
    } else {
      return redirect()->back()->with(['success' => 'Error while creating company!!']);
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
    //Get company
    $data = Company::where('id', '=', $id)->get();
    return view('admin.company.company_edit',compact('data'));
  }

  /**
  * Update the specified resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function update(Request $request) {
    //Validate input field
    $request->validate([
      'name' => 'required',
      'company' => 'required',
    ]);
    //$rules = [
    //'name' => 'required|string|max:255',
    //'company' => 'required',
    //];
    //$customMessages = [
    //'name'=> 'Please add company name',
    //'company' =>'Please add company',
    //];
    //$this->validate($request, $rules, $customMessages);
    $form_data = array(
        'name' => $request->name,
        'company' => $request->company,
    );
    $user_update = Company::whereId($request->hidden_id)->update($form_data);

    if($user_update){
      return redirect('/companies')->with(['success' => 'Company updated successfully.']);
    }else{
      return redirect()->back()->with(['success' => 'Error while updating Company!!']);
    }
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */

  //Function for delete company
  public function destroy(Request $request) {
		//Get ajax request for company id
		$company_id = $request->company_id;
		//Delete company
		$is_delete_company = Company::where('id', $company_id)->delete();
		//Check if company record deleted or not
		if($is_delete_company){
			echo '<p style="color:green;">Company record deleted successfully.</p>';
			// Corrected JavaScript code
			echo '<script>setTimeout(function(){ window.location.href = ""; }, 3000);</script>';
		} else {
			echo '<p style="color:green;">Oops something wrong.</p>';
		}
  }
	
	public static function companies() {
		$data = Company::orderBy('company', 'ASC')->get();
		return $data;
	}	
}
