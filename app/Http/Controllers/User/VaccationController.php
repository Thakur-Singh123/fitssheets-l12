<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Excel;
use App\Models\TimeSheet;
use App\Models\User;
use App\Models\House;
use App\Models\Company;
use DateTime;
use Carbon\Carbon;
use App\Models\UserVaccatioStatusn;
use App\Models\UserVaccation;

class VaccationController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	
	//Function for index
    public function index() {
		//Get user id
		$user = Auth::user()->id;
		//Get user vaccation status
		$data = UserVaccatioStatusn::where('user_id', '=', $user)->paginate(10);
		return view('user.vaccation.vc_view',compact('data'));
    }
	
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	//Function for create vocation
    public function create() {
		// $user = Auth::user()->id;
		// $Company = Auth::user()->companies_id;
		
		// $companies = Company::orderBy('created_at', 'DESC')->get();
		// if($Company == "Quantumleap, Inc"){
		// 	$houses = House::where('companies_id', '=', $Company)->orderBy('created_at', 'DESC')->get();
		// }else{
		// 	$Com = array();
		// 	$Com[] = "Quantumleap, Inc";
		// 	$houses = House::whereNotIn('companies_id', $Com)->orderBy('created_at', 'DESC')->get();
		// }
        // return view('user.vaccation.vc_add', compact('companies','user','houses'));
        return view('user.vaccation.vc_add');
    }
	
	 /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

	//Function for submit vocation
    public function store(Request $request) {
		//Validate input field
		$request->validate([
			'vacc_frm' => 'required',
		]);
		//Get auth use
		$user = Auth::user()->id;
		
		$vacc_frm    = explode('-', $request->vacc_frm);
		$vacc_frm = implode("_", $vacc_frm);
		$vacc_to    = explode('-', $request->vacc_to);
		$vacc_to = implode("_", $vacc_to);
		$report_by    = explode('-', $request->report_by);
		$report_by = implode("_", $report_by);

		//Get user vaccation
		$data_vacc = UserVaccation::where('user_id','=', $user)->orderBy('created_at', 'DESC')->first();

		$date1 = new DateTime(date('m/d/y', strtotime($request->vacc_frm)));
		$date2 = new DateTime(date('m/d/y', strtotime($request->vacc_to)));

		$diff = $date2->diff($date1);

		$days = $diff->days;
		$hours = $diff->h;
		$hours = $hours + ($diff->days*24);
		$hours = floatval(8*$days);
		// echo "Hello";
		// print_r($data_vacc);
    	// die;
		// if(isset($data_vacc)){

			// $used_hours = $data_vacc->vacc_vc;
			// $avail_hours = $data_vacc->vacc_sl;
			// $hours_requested = $hours;
			// $check_hours = $avail_hours - $hours_requested;
			// if($check_hours >= 0){

				$form_data = array(
					'user_id' => $user,
					'vacc_start' => $vacc_frm,
					'vacc_end' => $vacc_to,
					'vacc_comments' => $request->comments,
					'vacc_top'  => $request->time_policy,
					'vacc_rbu'  => $report_by,
					'vacc_status' => 0,
				);
				//store vaccation
				$ts_store = UserVaccatioStatusn::create($form_data);
			    //Check if voccation created or not
				if($ts_store) {
					return redirect('/enter-vaccation')->with(['success' => 'Vaccation Added successfully.']);
				} else {
					return redirect()->back()->with(['success' => 'Error while Adding Hours!!']);
				}
				// $used_hours = $used_hours + $hours_requested;
				// $avail_hours = $avail_hours - $hours_requested;
				// $form_data = array(
				// 'vacc_sl' => $avail_hours,
				// 'vacc_vc' => $used_hours,
				// );
				// $user_update = UserVaccation::where('user_id','=', $user)->update($form_data);
			// }else{
			// 	return redirect()->back()->with(['success' => 'Vaccation hours are not left or you have used your all hours.']);
			// }	
		// }else{
		// 		return redirect()->back()->with(['success' => 'Vaccation hours are not assign to you, please contact admin']);
		// }
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
	//Function for edit voccation
    public function edit($id) {
		// $user = Auth::user()->id;
		// $Company = Auth::user()->companies_id;
		// $companies = Company::orderBy('created_at', 'DESC')->get();
		// if($Company == "Quantumleap, Inc"){
		// 	$houses = House::where('companies_id', '=', $Company)->orderBy('created_at', 'DESC')->get();
		// }else{
		// 	$Com = array();
		// 	$Com[] = "Quantumleap, Inc";
		// 	$houses = House::whereNotIn('companies_id', $Com)->orderBy('created_at', 'DESC')->get();
		// }
		$data = UserVaccatioStatusn::where('id', '=', $id)->get();
		return view('user.vaccation.vc_edit',compact('data'));
    }
	
	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	//Function for update voccation
    public function update(Request $request) {
		//Validate input field
		$request->validate([
			'vacc_frm' => 'required',
		]);
		//Get auth id
		$user = Auth::user()->id; 
		
		$vacc_frm    = explode('-', $request->vacc_frm);
		$vacc_frm = implode("_", $vacc_frm);
		$vacc_to    = explode('-', $request->vacc_to);
		$vacc_to = implode("_", $vacc_to);
		$report_by    = explode('-', $request->report_by);
		$report_by = implode("_", $report_by);
        //Update vaccation
		$data_vacc = UserVaccation::where('user_id','=', $user)->orderBy('created_at', 'DESC')->first();

		$date1 = new DateTime(date('m/d/y', strtotime($request->vacc_frm)));
		$date2 = new DateTime(date('m/d/y', strtotime($request->vacc_to)));

		$diff = $date2->diff($date1);

		$days = $diff->days;
		$hours = $diff->h;
		$hours = $hours + ($diff->days*24);
		$hours = floatval(8*$days);

		// if(isset($data_vacc)){
			// $used_hours = $data_vacc->vacc_vc;
			// $avail_hours = $data_vacc->vacc_sl;
			// $hours_requested = $hours;
			// $check_hours = $avail_hours - $hours_requested;
			// if($check_hours >= 0){

				$form_data = array(
						'user_id' => $user,
						'vacc_start' => $vacc_frm,
						'vacc_end' => $vacc_to,
						'vacc_comments' => $request->comments,
						'vacc_top'  => $request->time_policy,
						'vacc_rbu'  => $report_by,
				);
				//Update
				$ts_update = UserVaccatioStatusn::whereId($request->hidden_id)->update($form_data);
                //Check if vocation updated or not
				if($ts_update){
					return redirect('/enter-vaccation')->with(['success' => 'Vaccation updated successfully.']);
				}else{
					return redirect()->back()->with(['success' => 'Error while updating Hours!!']);
				}
				// $used_hours = $used_hours + $hours_requested;
				// $avail_hours = $avail_hours - $hours_requested;
				// $form_data = array(
				// 'vacc_sl' => $avail_hours,
				// 'vacc_vc' => $used_hours,
				// );
				// $user_update = UserVaccation::where('user_id','=', $user)->update($form_data);
			// }else{
			// 	return redirect()->back()->with(['success' => 'Vaccation hours are not left or you have used your all hours.']);
			// }	
		// }else{
		// 		return redirect()->back()->with(['success' => 'Vaccation hours are not assign to you, please contact admin']);
		// }
    }
	

	 /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	//Function for delete voccation
    public function destroy($id) {
		//Delete vaccation
        $data = UserVaccatioStatusn::findOrFail($id);
        $data->delete();
    }	
}
