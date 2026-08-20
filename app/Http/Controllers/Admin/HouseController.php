<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\House;
use App\Models\Company;

class HouseController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */

  public function index() {
    $data = House::orderBy('created_at', 'DESC')->paginate(10);
    return view('admin.house.house_view',compact('data'));
  }

  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */

  public function create() {
    //Get companies
    $companies = Company::orderBy('display_order', 'ASC')->get();
    return view('admin.house.house_add', compact('companies'));
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
      'house_add' => 'required',
      'company_id' => 'required',
    ]);
    //$rules = [
    //'house_add'    =>  'required',
    //];
    //$customMessages = [
    //'house_add'    =>  'Please Add the house address',
    //];
    //$this->validate($request, $rules, $customMessages);
    //Create house
    $form_data = array(
      'companies_id' => $request->company_id,
      'house_add' => $request->house_add,
    );
    //Store
    $house_store = House::create($form_data);
    //Check if house created or not
    if($house_store) {
      return redirect('/houses')->with(['success' => 'House created successfully.']);
    } else {
      return redirect()->back()->with(['success' => 'Error while creating House!!']);
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
    $companies = Company::orderBy('created_at', 'DESC')->get();
    $data = House::where('id', '=', $id)->get();
    return view('admin.house.house_edit',compact('data','companies'));
  }

  /**
  * Update the specified resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  
  public function update(Request $request) {
    //Validate input fields
    $request->validate([
      'house_add' => 'required',
      'company_id' => 'required',
    ]);
    //$rules = [
    //'house_add'=>'required',
    //];
    //$customMessages = [
    //'house_add'=>'Please Add the house address',
    //];
    //$this->validate($request, $rules, $customMessages);
    //Array
    $form_data = array(
      'companies_id' => $request->company_id,
      'house_add' => $request->house_add,
    );
    //Update 
    $house_update = House::whereId($request->hidden_id)->update($form_data);
    //Check if house updated or not
    if($house_update) {
      return redirect('/houses')->with(['success' => 'House updated successfully.']);
    } else {
      return redirect()->back()->with(['success' => 'Error while updating House!!']);
    }
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  
  //Function for delete house
  public function destroy(Request $request) {
		//Get ajax request for house id
		$house_id = $request->house_id;
		//Delete house
		$is_delete_house = House::where('id', $house_id)->delete();
		//Check if house_ record deleted or not
		if($is_delete_house){
			echo '<p style="color:green;">House record deleted successfully.</p>';
			// Corrected JavaScript code
			echo '<script>setTimeout(function(){ window.location.href = ""; }, 3000);</script>';
		} else {
			echo '<p style="color:green;">Oops something wrong.</p>';
		}
  }
}
