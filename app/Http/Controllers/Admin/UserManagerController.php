<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\UserManager;

class UserManagerController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {
		$data = UserManager::with('users')->where("musers_id", "=", $user)->orderBy('created_at', 'DESC')->get();
		return view('admin.musers.muser_view',compact('data'));
    }
	
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$user = User::orderBy('created_at', 'DESC')->get();
        return view('admin.musers.muser_add', compact('user'));
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
			'user_id'    =>  'required',
		];
		$customMessages = [
			'user_id'    =>  'Please Select User',
		];
		$this->validate($request, $rules, $customMessages);
				
		$form_data = array(
				'companies_id' => $request->company_id,
				'house_add' => $request->house_add,
		);
		
		$house_store = House::create($form_data);
			
		if($house_store){
			return redirect('/houses')->with(['success' => 'House Created Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while creating House!!']);
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
    public function update(Request $request)
    {

		$rules = [
			'house_add'    =>  'required',
		];
		$customMessages = [
			'house_add'    =>  'Please Add the house address',
		];
		$this->validate($request, $rules, $customMessages);
				
		$form_data = array(
				'companies_id' => $request->company_id,
				'house_add' => $request->house_add,
		);
		$house_update = House::whereId($request->hidden_id)->update($form_data);

		if($house_update){
			return redirect('/houses')->with(['success' => 'House Updated Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while updating House!!']);
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
        $data = House::findOrFail($id);
        $data->delete();
    }
}
