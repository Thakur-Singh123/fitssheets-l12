<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;

class CaseManagerInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {
		$data = User::where("role", "=", "casemanager")->orderBy('name', 'ASC')->get();
		return view('admin.cmusers.cmuser_view',compact('data'));
    }
	
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.user_add');
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
			'name'     =>  'required|string|max:255',
			'email'    =>  'required|string|email|max:255|unique:users',
			'role' 	   =>  'required',
			'dept'     =>  'required',
			'password' =>  'required|string|min:8|same:confirmed',
		];
		$customMessages = [
			'name'     =>  'Please add user name',
			'email'    =>  'Please add user email',
			'role' 	   =>  'Please add user role',
			'dept'     =>  'Please add user department',
			'password' =>  'Add password or same as password entered before',
		];
		$this->validate($request, $rules, $customMessages);
				
		$form_data = array(
				'name' => $request->name,
				'email' => $request->email,
				'role' => $request->role,
				'dept' => $request->dept,
				'status'     =>  '0',
				'password' => Hash::make($request->password),
		);
		
		$user_store = User::create($form_data);
			
		if($user_store){
			return redirect('/casemanagers')->with(['success' => 'Manager Created Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while creating User!!']);
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
		$data = User::where('id', '=', $id)->get();
		return view('admin.users.user_edit',compact('data'));
		
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
			'name'     =>  'required',
			'email'    =>  'required',
			'role' 	   =>  'required',
			'dept'     =>  'required',
			'password' =>  'required',
		];
		$customMessages = [
			'name'     =>  'Please add user name',
			'email'    =>  'Please add user email',
			'role' 	   =>  'Please add user role',
			'dept'     =>  'Please add user department',
			'password' =>  'Please add user password',
		];
		$this->validate($request, $rules, $customMessages);

		$form_data = array(
				'name' => $request->name,
				'email' => $request->email,
				'role' => $request->role,
				'dept' => $request->dept,
				'status'     =>  '0',
				'password' => Hash::make($request->password),
		);
		$user_update = User::whereId($request->hidden_id)->update($form_data);

		if($user_update){
			return redirect('/casemanagers')->with(['success' => 'User Updated Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while updating User!!']);
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
        $data = User::findOrFail($id);
        $data->delete();
    }
}
