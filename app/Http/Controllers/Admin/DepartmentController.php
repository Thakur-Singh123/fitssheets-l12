<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;

class DepartmentController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  
  //Fuction for all department
  public function index() {
    $data = Department::orderBy('created_at', 'DESC')->paginate(10);
    return view('admin.department.dept_view',compact('data'));
  }

  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */

  //Function for create department
  public function create() {
    return view('admin.department.dept_add');
  }

  /**
  * Store a newly created resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */

  //Function for store department
  public function store(Request $request) {
    //Validate input fields
    $request->validate([
      'dept_add' => 'required',
    ]);
    // $rules = [
    //   'dept_add'    =>  'required',
    // ];
    // $customMessages = [
    //   'dept_add'    =>  'Please Add the house address',
    // ];
    // $this->validate($request, $rules, $customMessages);
    //Create
    $form_data = array(
        'department' => $request->dept_add,
    );
    $department_store = Department::create($form_data);
    //Check if department exits or not
    if($department_store) {
      return redirect('/department')->with(['success' => 'Department created successfully.']);
    } else {
      return redirect()->back()->with(['success' => 'Error while creating Department!!']);
    }
  }

  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show($id){
    //
  }

  /**
  * Show the form for editing the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function edit($id) {
    $data = Department::where('id', '=', $id)->get();
    return view('admin.department.dept_edit',compact('data'));
  }

  /**
  * Update the specified resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */

  //Function for update department
  public function update(Request $request) {
    //Validate input fields
    $request->validate([
      'dept_add' => 'required',
    ]);
    $rules = [
      'dept_add'    =>  'required',
    ];
    //$customMessages = [
    //'dept_add' => 'Please Add the house address',
    //];
    // $this->validate($request, $rules, $customMessages);
    $form_data = array(
      'department' => $request->dept_add,
    );
    //Update
    $dept_update = Department::whereId($request->hidden_id)->update($form_data);
    //Check if department updated or not
    if($dept_update){
      return redirect('/department')->with(['success' => 'Department updated successfully.']);
    }else{
      return redirect()->back()->with(['success' => 'Error while updating Department!!']);
    }
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  
  //Function for delete department
  public function destroy(Request $request) {
		//Get ajax request for department id
		$department_id = $request->department_id;
		//Delete department
		$is_delete_department = Department::where('id', $department_id)->delete();
		//Check if department record deleted or not
		if($is_delete_department){
			echo '<p style="color:green;">Department record deleted successfully.</p>';
			// Corrected JavaScript code
			echo '<script>setTimeout(function(){ window.location.href = ""; }, 3000);</script>';
		} else {
			echo '<p style="color:green;">Oops something wrong.</p>';
		}
  }
	
  //Function for departments
	public static function departments() {
		$data = Department::orderBy('department', 'ASC')->get();
		return $data;
	}
}
