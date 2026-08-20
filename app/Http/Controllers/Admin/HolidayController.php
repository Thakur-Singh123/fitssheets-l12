<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\Holiday;

class HolidayController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  
  public function index() {
    $data = Holiday::orderBy('created_at', 'DESC')->paginate(10);
    return view('admin.holiday.holiday_view',compact('data'));
  }
/**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create() {
      return view('admin.holiday.holiday_add');
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
      'date' => 'required',
      'description' => 'required',
    ]);
    //$rules = [
    //'date' =>  'required|string|max:255',
    //'description' =>  'required',
    //];
    //$customMessages = [
    //'date'=> 'Please add date',
    //'description'=> 'Please add description',
    //];
    // $this->validate($request, $rules, $customMessages);
    $form_data = array(
      'date' => $request->date,
      'description' => $request->description,
    );
    //Create holidya
    $user_store = Holiday::create($form_data);
    //Check if holiday create or not
    if($user_store) {
      return redirect('/holidays')->with(['success' => 'Holiday created successfully.']);
    } else {
      return redirect()->back()->with(['error' => 'Error while creating Holiday!!']);
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
    $data = Holiday::where('id', '=', $id)->get();
    return view('admin.holiday.holiday_edit',compact('data'));
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
      'date' => 'required',
      'description' => 'required',
    ]);
    //$rules = [
    //'date' => 'required|string|max:255',
    //'description' => 'required',
    //];
    //$customMessages = [
    //'date' => 'Please add date',
    //'description' => 'Please add description',
    //];
    //$this->validate($request, $rules, $customMessages);
    $form_data = array(
      'date' => $request->date,
      'description' => $request->description,
    );
    //Update
    $user_update = Holiday::whereId($request->hidden_id)->update($form_data);
    //Check if holiday updated or not 
    if($user_update) {
      return redirect('/holidays')->with(['success' => 'Holiday updated successfully.']);
    } else {
      return redirect()->back()->with(['error' => 'Error while updating Holiday!!']);
    }
  }

  /**
  * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

  //Function for delete holiday
  public function destroy(Request $request) {
    //Get ajax request for holiday id
    $holiday_id = $request->holiday_id;
    //Delete holiday
    $is_delete_holiday = Holiday::where('id', $holiday_id)->delete();
    //Check if payperiod record deleted or not
    if($is_delete_holiday){
      echo '<p style="color:green;">Holiday record deleted successfully.</p>';
      // Corrected JavaScript code
      echo '<script>setTimeout(function(){ window.location.href = ""; }, 3000);</script>';
    } else {
      echo '<p style="color:green;">Oops something wrong.</p>';
    }
  }
}
