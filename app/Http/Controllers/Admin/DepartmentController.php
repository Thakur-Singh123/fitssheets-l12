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
	 
    public function index()
    {
		$data = Department::orderBy('created_at', 'DESC')->get();
		return view('admin.department.dept_view',compact('data'));
    }
	
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.department.dept_add');
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
			'dept_add'    =>  'required',
		];
		$customMessages = [
			'dept_add'    =>  'Please Add the house address',
		];
		$this->validate($request, $rules, $customMessages);
				
		$form_data = array(
				'department' => $request->dept_add,
		);
		
		$department_store = Department::create($form_data);
			
		if($department_store){
			return redirect('/department')->with(['success' => 'Department Created Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while creating Department!!']);
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
    public function update(Request $request)
    {

		$rules = [
			'dept_add'    =>  'required',
		];
		$customMessages = [
			'dept_add'    =>  'Please Add the house address',
		];
		$this->validate($request, $rules, $customMessages);
				
		$form_data = array(
				'department' => $request->dept_add,
		);
		
		$dept_update = Department::whereId($request->hidden_id)->update($form_data);

		if($dept_update){
			return redirect('/department')->with(['success' => 'Department Updated Successfully!!']);
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
    public function destroy($id)
    {
        $data = Department::findOrFail($id);
        $data->delete();
    }
	
	 public static function departments() {
			$data = Department::orderBy('department', 'ASC')->get();
		return $data;
	}
}
