<?php

namespace App\Http\Controllers\CaseManager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\TimeSheet;
use App\User;
use DateTime;
use App\Company;
use App\UserManager;
use App\UserCasemanagerRel;
use App\Payperiods;

class UsermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {
		$user = Auth::user()->id;
		$data = UserCasemanagerRel::where('casemanager_id', '=', $user)->get();
		$user_id = array();
		
		if(isset($data)){
			foreach($data as $datas){
				$users = User::where('id', '=', $datas->users_id)->first();
				$user_id[] = $users->id;
			}
		}
		// echo "<pre>";
		// print_r($user_id);
		// die;
		$company_id = array();
		if(isset($user_id)){
			foreach($user_id as $user_ids){
				$company = UserManager::where('musers_id', '=', $user_ids)->first();
				if(isset($company)){
					$company_id[] = $company->users_id;
				}
				
			}
		}

		
		$payperiods = Payperiods::whereIn('companies_id', $company_id)->get();
		$TodayDate = new DateTime();		
		if(isset($payperiods)){
			foreach($payperiods as $payperiod){
				$bet_dates = explode('-',$payperiod->payperiod_value);
				if(isset($bet_dates)){
					$from_date    = $bet_dates[0];
					$to_date    = $bet_dates[1];
				}
				$xto_date = explode('_',$to_date);
				$xto_date = implode('-',$xto_date);
				$xfrom_date = explode('_',$from_date);
				$xfrom_date = implode('-',$xfrom_date);
				
				$xto_date = new DateTime($xto_date);
				$xfrom_date  = new DateTime($xfrom_date);
				
				 if (
				  $TodayDate->format('y-m-d') >= $xfrom_date->format('y-m-d') && 
				  $TodayDate->format('y-m-d') <= $xto_date->format('y-m-d')){
				  $frm_date  = $xfrom_date->format('Y-m-d');
				  $t_date = $xto_date->format('Y-m-d');
				  $sfrm_date  = $xfrom_date->format('Y_m_d');
				  $st_date = $xto_date->format('Y_m_d');
				}
			}
			
		}

		$arr_dates = array();
		if(!empty($frm_date) && !empty($t_date)){
			$arr_dates[] = array('frm_date' => $frm_date, 't_date' => $t_date);
		
		}
		else{
			$arr_dates[] = array('frm_date' => "", 't_date' => "");
			
		}
		if(!empty($sfrm_date) && !empty($st_date)){
			$arr_dates[] = array('sfrm_date' => $sfrm_date, 'st_date' => $st_date);
			
		}
		else{
			$arr_dates[] = array('sfrm_date' => "", 'st_date' => "");
			
		}
		
		$time_sheet = TimeSheet::whereBetween('hours_day', array($arr_dates[1]['sfrm_date'], $arr_dates[1]['st_date']))->whereIn('users_id', $user_id)->orderBy('created_at', 'DESC')->get();
		$tuser_id = array();
		// echo "<pre>";
		// print_r($time_sheet);
		// die;
		if(isset($time_sheet)){
			foreach($time_sheet as $datas){
				$users = User::where('id', '=', $datas->users_id)->first();
				$tuser_id[] = $users->id;
			}
		}
		$data = User::whereIn('id', $user_id)->where('role', '=', "user")->orderBy('name', 'ASC')->paginate(15);
		return view('casemanager.users.user_view',compact('data', 'arr_dates'));
    }
	
	
	 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function timesheets($id)
    {

		$company = UserManager::where('musers_id', '=', $id)->first();
		if(isset($company)){
			$payperiods = Payperiods::where('companies_id', $company->users_id)->get();
		}else{
			$payperiods = array();
		}
		
		$TodayDate = new DateTime();		
		if(isset($payperiods)){
			foreach($payperiods as $payperiod){
				$bet_dates = explode('-',$payperiod->payperiod_value);
				if(isset($bet_dates)){
					$from_date    = $bet_dates[0];
					$to_date    = $bet_dates[1];
				}
				$xto_date = explode('_',$to_date);
				$xto_date = implode('-',$xto_date);
				$xfrom_date = explode('_',$from_date);
				$xfrom_date = implode('-',$xfrom_date);
				
				$xto_date = new DateTime($xto_date);
				$xfrom_date  = new DateTime($xfrom_date);
				
				 if (
				  $TodayDate->format('y-m-d') >= $xfrom_date->format('y-m-d') && 
				  $TodayDate->format('y-m-d') <= $xto_date->format('y-m-d')){
				  $frm_date  = $xfrom_date->format('Y-m-d');
				  $t_date = $xto_date->format('Y-m-d');
				  $sfrm_date  = $xfrom_date->format('Y_m_d');
				  $st_date = $xto_date->format('Y_m_d');
				}
			}
			
		}

		$arr_dates = array();
		if(!empty($frm_date) && !empty($t_date)){
			$arr_dates[] = array('frm_date' => $frm_date, 't_date' => $t_date);
		
		}
		else{
			$arr_dates[] = array('frm_date' => "", 't_date' => "");
			
		}
		if(!empty($sfrm_date) && !empty($st_date)){
			$arr_dates[] = array('sfrm_date' => $sfrm_date, 'st_date' => $st_date);
			
		}
		else{
			$arr_dates[] = array('sfrm_date' => "", 'st_date' => "");
			
		}
		
		$data = TimeSheet::whereBetween('hours_day', array($arr_dates[1]['sfrm_date'], $arr_dates[1]['st_date']))->where('users_id', $id)->orderBy('created_at', 'DESC')->get();
		// $tuser_id = array();
		// echo "<pre>";
		// print_r($time_sheet);
		// die;
		// if(isset($time_sheet)){
		// 	foreach($time_sheet as $datas){
		// 		$users = User::where('id', '=', $datas->users_id)->first();
		// 		$tuser_id[] = $users->id;
		// 	}
		// }

		$user = User::where("role", "=", "user")->where('id', '=', $id)->orderBy('created_at', 'DESC')->first();
		$name = $user->name;
		// $data = TimeSheet::with('companies')->where('users_id', '=', $id)->orderBy('created_at', 'DESC')->get();
		return view('casemanager.timesheet.ts_view',compact('data', 'id','name','arr_dates'));
    }
	
	public function timesheets_wt_dates($id,$frm_dt,$to_dt)
    {
		
		$f_d = $frm_dt;
		$t_d = $to_dt;
		
		$company = UserManager::where('musers_id', '=', $id)->first();
		if(isset($company)){
			$payperiods = Payperiods::where('companies_id', $company->users_id)->get();
		}else{
			$payperiods = array();
		}
		
		$TodayDate = new DateTime();		
		if(isset($payperiods)){
			foreach($payperiods as $payperiod){
				$bet_dates = explode('-',$payperiod->payperiod_value);
				if(isset($bet_dates)){
					$from_date    = $bet_dates[0];
					$to_date    = $bet_dates[1];
				}
				$xto_date = explode('_',$to_date);
				$xto_date = implode('-',$xto_date);
				$xfrom_date = explode('_',$from_date);
				$xfrom_date = implode('-',$xfrom_date);
				
				$xto_date = new DateTime($xto_date);
				$xfrom_date  = new DateTime($xfrom_date);
				
				 if (
				  $TodayDate->format('y-m-d') >= $xfrom_date->format('y-m-d') && 
				  $TodayDate->format('y-m-d') <= $xto_date->format('y-m-d')){
				  $frm_date  = $xfrom_date->format('Y-m-d');
				  $t_date = $xto_date->format('Y-m-d');
				  $sfrm_date  = $xfrom_date->format('Y_m_d');
				  $st_date = $xto_date->format('Y_m_d');
				}
			}
			
		}

		$arr_dates = array();
		if(!empty($frm_date) && !empty($t_date)){
			$arr_dates[] = array('frm_date' => $frm_date, 't_date' => $t_date);
		
		}
		else{
			$arr_dates[] = array('frm_date' => "", 't_date' => "");
			
		}
		if(!empty($sfrm_date) && !empty($st_date)){
			$arr_dates[] = array('sfrm_date' => $sfrm_date, 'st_date' => $st_date);
			
		}
		else{
			$arr_dates[] = array('sfrm_date' => "", 'st_date' => "");
			
		}
		
		
		if(!empty($f_d) && !empty($t_d)){
			$data = TimeSheet::whereBetween('hours_day', array($f_d, $t_d))->where('users_id', $id)->orderBy('created_at', 'DESC')->get();
		
		}else{
			$data = TimeSheet::whereBetween('hours_day', array($arr_dates[1]['sfrm_date'], $arr_dates[1]['st_date']))->where('users_id', $id)->orderBy('created_at', 'DESC')->get();
		
		}
		// $tuser_id = array();
		// echo "<pre>";
		// print_r($time_sheet);
		// die;
		// if(isset($time_sheet)){
		// 	foreach($time_sheet as $datas){
		// 		$users = User::where('id', '=', $datas->users_id)->first();
		// 		$tuser_id[] = $users->id;
		// 	}
		// }

		$user = User::where("role", "=", "user")->where('id', '=', $id)->orderBy('created_at', 'DESC')->first();
		$name = $user->name;
		// $data = TimeSheet::with('companies')->where('users_id', '=', $id)->orderBy('created_at', 'DESC')->get();
		return view('casemanager.timesheet.ts_view',compact('data', 'id','name','arr_dates','f_d', 't_d'));
    }
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('casemanager.users.user_add');
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
			return redirect('/cmusers')->with(['success' => 'User Created Successfully!!']);
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
			return redirect('/users')->with(['success' => 'User Updated Successfully!!']);
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
	
	public function user_msearch(Request $request)
    {
		$searchTerm = $request->srch_user;
		$data = User::where('role', 'user')
						->where('name', 'LIKE', "%{$searchTerm}%") 
						->orWhere('email', 'LIKE', "%{$searchTerm}%")
						->orderBy('name', 'ASC')
						->get();
			if(isset($data)){
$count = 1; 
			  if($data->count() != 0){
				foreach ($data as $datas){
					echo "<tr>";
					  echo "<td>".$count."</td>";
					  echo "<td>".$datas->email."</td>";
					  echo "<td>".$datas->name."</td>";
					 echo "<td>".$datas->dept."</td>";
					  echo "<td>".date('M d, Y', strtotime($datas->created_at))."</td>";
					   echo "<td>";
					  if($datas->status == 1 ){ echo "Active"; }
					  else{ echo "InActive"; }
					  echo "</td>";
					  echo "<td>";
						echo '<a  style="margin-left: 5px;"  href="'.url('/user/timesheets').'/'.$datas->id.'" title="Time Sheets"><i class="fa fa-book"></i></a>';
					   echo "</td>";
					 echo "</tr>";
				$count++; 
				}
				
			  }else{
					echo "<p>Sorry No Data!!</p>";
			  }
				
				
			}
    }
}
