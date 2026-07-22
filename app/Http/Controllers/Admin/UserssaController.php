<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\TimeSheet;
use Excel;
use App\User;
use App\Company;
use App\UserManager;
use DateTime;
use App\Payperiods;
use App\UserSupervisorRel;

class UserssaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index($id,$frm_dt,$to_dt)
    {
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		$TodayDate = new DateTime();
        $origin = new DateTime('2020-12-21');
        $interval = $origin->diff($TodayDate);
        $date_diff =  $interval->format('%a');
        if($date_diff == 0){
            	$frm_date = "2020_12_21";
		        $t_date = "2021_01_03";
        }
      
		if(!empty($frm_dt) && !empty($to_dt)){
			$frm_date = $frm_dt;
			$t_date = $to_dt;
		}
		$user = $id;
		$companies = UserManager::where('musers_id', '=', $user)->get();
		$company_id = array();
		$users = User::where('id', '=', $user)->first();
		$user_f_name = $users->first_name;
		if(isset($companies)){
			foreach($companies as $company){
				$company_id[] = $company->users_id;
			}
		}
		$users_id = UserManager::whereIn('users_id', $company_id)->get();
		$user_idss = array();
		if(isset($users_id)){
			foreach($users_id as $users_ids){
				$user_idss[] = $users_ids->musers_id;
			}
		}
		$companiess = Company::orderBy('company', 'ASC')->get();
		$data = User::with('companies')->whereIn('id', $user_idss)->where('role', '=', "user")->orderBy('name', 'ASC')->paginate(15);
		return view('admin.sausers.user_view',compact('data','user','user_f_name', 'companiess','frm_date','t_date' ));
    }
	
	
	 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function timesheets($sv,$id,$frm_dt,$to_dt)
    {
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		$TodayDate = new DateTime();
        $origin = new DateTime('2020-12-21');
        $interval = $origin->diff($TodayDate);
        $date_diff =  $interval->format('%a');
        if($date_diff == 0){
            	$frm_date = "2020_12_21";
		        $t_date = "2021_01_03";
		         $sfrm_date  = "2020_12_21";
				  $st_date = "2021_01_03";
        }
       
		if(!empty($sfrm_date) && !empty($st_date)){
			$sfrm_date  = $sfrm_date;
			$st_date = $st_date;
		}
		else{
			$sfrm_date  = "";
			$st_date = "";
		}
		
		if(!empty($frm_dt) && !empty($to_dt)){
			$frm_date = $frm_dt;
			$t_date = $to_dt;
			$sfrm_date = explode('-',$frm_dt);
			$sfrm_date = implode('_',$sfrm_date);
			$st_date = explode('-',$to_dt);
			$st_date = implode('_',$st_date);
		}
		$user = User::where("role", "=", "user")->where('id', '=', $id)->orderBy('created_at', 'DESC')->first();
		$name = $user->name;
		$data = TimeSheet::with('companies')->whereBetween('hours_day', array($sfrm_date, $st_date))->where('users_id', '=', $id)->orderBy('created_at', 'DESC')->get();
		return view('admin.satimesheet.ts_view',compact('data','sv', 'id','name','frm_date','t_date'));
    }
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('manager.users.user_add');
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
			return redirect('/users')->with(['success' => 'User Created Successfully!!']);
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
	
	public function user_ssearch(Request $request)
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
				foreach ($data as $user_data){
					$color_info =  $this->color_info($user_data->id); 
					
					
					if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					}
				  echo "<td>".$count."</td>";
				   echo "<td>".$user_data->emp_id."</td>";
				   echo "<td>";
					 echo '<a  style="margin-left: 5px;"  href="'.url('/user/suser/timesheets').'/'.$user_data->id.'" title="Time Sheets"><i class="fa fa-book"></i></a>';
				  echo "</td>";
				   echo "<td>".$user_data->last_name." ".$user_data->first_name."</td>";
				 echo "<td>".$user_data->dept."</td>";
				 $user_companies = $this->user_companies($user_data->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
				  echo "<td>";
				  if($user_data->last_login_at != null){ echo date('h:i a', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				   echo "<td>";
				  if($user_data->last_login_at != null){ echo date('M d, Y', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				  echo "<td>".date('M d, Y(D)', strtotime($user_data->created_at))."</td>";
				  echo "<td>";
				   if($user_data->status == 1){ echo "<h5 style='color:green'>Active</h5>"; }else{ echo "<h5 style='color:red'>Inactive</h5>"; } 
				   echo "</td>";
				 				    				  echo "<td>";
					$total_time = $this->total_time($user_data->id);
						if($total_time <=  79){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_time."</p>";
						}elseif($total_time == 80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_time."</p>";
						}
				  echo "</td>";
				   echo "<td>";
					$approved_time = $this->approved_time($user_data->id);
						if($approved_time <=  79){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_time."</p>";
						}elseif($approved_time == 80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_time."</p>";
						}
				  echo "</td>";
				   echo "<td>";
					$denied_time = $this->denied_time($user_data->id);
						if($denied_time <=  79){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_time."</p>";
						}elseif($denied_time == 80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_time."</p>";
						}
				  echo "</td>";
				 echo "</tr>";
				$count++; 
				}
				
			  }else{
					echo "<p>Sorry No Data!!</p>";
			  }
				
				
			}
    }
	
	public function export_comp_data($comp_id)
    {

		$users_id = UserManager::where('users_id', $comp_id)->get();
		$user_idss = array();
		if(isset($users_id)){
			foreach($users_id as $users_ids){
				$user_idss[] = $users_ids->musers_id;
			}
		}
		
		$users = User::with('companies')->whereIn('id', $user_idss)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
		$user_arr = array();
		$user_count = 1;
		if(isset($users)){

			foreach($users as $userss){
				$user_arr[] = $userss->id;
				$data = TimeSheet::with('companies')
									->with('houses')
									->with('users')
									->where('users_id', $userss->id)
									->orderBy('hours_day', 'DESC')
									->get();
		
			$count = 1;
			$total_hours = 0;
			$approved_hours = 0;
			$denied_hours = 0;
			  if($data->count() != 0){
				foreach ($data as $datas){
					if($datas->approved_by != 0){
						$user_id = User::where('id', '=', $datas->approved_by )->first();
						$approved_by = $user_id->name;
					}else{
						$approved_by = "";
					}
					
					 $hours_day    = explode('_', $datas->hours_day);
					 $hours_day = implode("/", $hours_day); 
					 $hours_day = date("M d, Y(D)", strtotime($hours_day));		
					$total_hours					  = $total_hours + $datas->hours_wrk;
					 if($datas->vacation_status == "0"){ 
						$vacation_status = "No"; 
					 }elseif($datas->vacation_status == "1"){
						 $vacation_status = "Yes";
						}else{
					$vacation_status = "";
				}
						$approve = "";
					if($datas->approve == "2"){ 
							$approve = "Yes";
							$approved_hours					  = $approved_hours + $datas->hours_wrk;
					}elseif($datas->approve == "1"){
						$denied_hours					  = $denied_hours + $datas->hours_wrk;
						$approve = "No";
					}else{
						$approve = "Pending"; 
					}
					$color_info = $this->color_info($datas->users->id); 
					$time_sheet[] = array(

						'#' => $count,
						'Emp ID' =>  $datas->users->emp_id,
						'Last Name'   => $datas->users->last_name,
						'First Name'   => $datas->users->first_name,
						'Name'  => $datas->users->name,
						'Department'   => $datas->users->dept,
						'House'  => $datas->houses->house_add,
						'Time In'   => $datas->time_in,
						'Time Out'   => $datas->time_out,
						'Hours Worked'  => $datas->hours_wrk,
						'Day'    => $hours_day,
						'Vacation'   => $vacation_status,
						'Approved'    => $approve,
						'Approved By' => $approved_by,
						'approver_color' => $color_info,
						
					);
					$count++;
				}
			  }
			  if($total_hours != 0){
				  $time_sheet[] = array(

						'#' => "",
						'Emp ID' =>  "",
						'Last Name'   => "",
						'First Name'   => "",
						'Name'  => "",
						'Department'   => "",
						'House'  => "",
						'Time In'   => "",
						'Time Out'   => "Total Hours",
						'Hours Worked'  => $total_hours,
						'Day'    => "",
						'Vacation'   => "",
						'Approved'    => "",
						'Approved By' => "",
						'approver_color' => "",
						
					);
				  $time_sheet[] = array(

						'#' => "",
						'Emp ID' =>  "",
						'Last Name'   => "",
						'First Name'   =>"",
						'Name'  => "",
						'Department'   => "",
						'House'  => "",
						'Time In'   => "",
						'Time Out'   => "Approval",
						'Hours Worked'  => "Approved",
						'Day'    => "Denied",
						'Vacation'   => "",
						'Approved'    => "",
						'Approved By' => "",
						'approver_color' => "",
						
					);
					$time_sheet[] = array(

						'#' => "",
						'Emp ID' =>  "",
						'Last Name'   => "",
						'First Name'   => "",
						'Name'  => "",
						'Department'   => "",
						'House'  => "",
						'Time In'   => "",
						'Time Out'   => "Hours",
						'Hours Worked'  => $approved_hours,
						'Day'    => $denied_hours,
						'Vacation'   => "",
						'Approved'    => "",
						'Approved By' => "",
						'approver_color' => "",
						
					);
					
					$time_sheet[] = array(

						'#' => "",
						'Emp ID' =>  "",
						'Last Name'   => "",
						'First Name'   => "",
						'Name'  => "",
						'Department'   => "",
						'House'  => "",
						'Time In'   => "",
						'Time Out'   => "",
						'Hours Worked'  => "",
						'Day'    => "",
						'Vacation'   => "",
						'Approved'    => "",
						'Approved By' => "",
						'approver_color' => "",
						
					);	 
			  }
			  		
			}
		}
		Excel::create('Time Sheet', function($excel) use ($time_sheet){
			$excel->setTitle('Time Sheet');
			$excel->sheet('Time Sheet', function($sheet) use ($time_sheet){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
				$sheet->row(1, array('#','Emp ID', 'Last Name','First Name','Name', 'Department','House', 'Time In','Time Out', 'Hours Worked', 'Day', 'Vacation', 'Approved', 'Approved By'));
				$sheet->cells('A1:L1', function ($cells) {
								$cells->setFontColor('#000000');
								$cells->setFontFamily('Calibri');
								$cells->setFontSize(14);
								$cells->setBackground('#BDB76B');
								$cells->setAlignment('center');
							});
				$i = 2;

				foreach ($time_sheet as $cleans) {

					$sheet->row($i, array($cleans['#'], $cleans['Emp ID'], $cleans['Last Name'],$cleans['First Name'], $cleans['Name'], $cleans['Department'], $cleans['House'],$cleans['Time In'], $cleans['Time Out'], $cleans['Hours Worked'], $cleans['Day'], $cleans['Vacation'], $cleans['Approved'], $cleans['Approved By']));
				
					if($cleans['approver_color'] != ""){
						$sheet->cell('O'.$i, function($cell) {
							$cell->setValue('');
						});
						$bgcolor = $cleans['approver_color'];
						$sheet->cells('A'.$i.':L'.$i, function ($cells) use ($bgcolor) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground($bgcolor);
							$cells->setAlignment('center');
						});
					}else{
						$sheet->cell('O'.$i, function($cell) {
							$cell->setValue('');
						});
						$sheet->cells('A'.$i.':L'.$i, function ($cells) {
							$cells->setFontColor('#000000');
							$cells->setFontFamily('Calibri');
							$cells->setFontSize(14);
							$cells->setBackground('#ffffff');
							$cells->setAlignment('center');
						});
					}
					$i++;
				}

			});
		})->download('xlsx');
	}
	
	public static function total_time($id,$frm_date,$t_date)
    {
        $payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		$TodayDate = new DateTime();
		$origin = new DateTime('2020-12-21');
        $interval = $origin->diff($TodayDate);
        $date_diff =  $interval->format('%a');
        if($date_diff == 0){
            	$frm_date = "2020_12_21";
		        $t_date = "2021_01_03";
		         $from_dat  = "2020_12_21";
				  $to_dat = "2021_01_03";
        }
        
		$users_arrr = array();
		$users = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->get();
		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}
		
		if(!empty($frm_date) && !empty($t_date)){
			
				$from_dat = explode('-',$frm_date);
				$from_dat = implode('_',$from_dat);
				$to_dat = explode('-',$t_date);
				$to_dat = implode('_',$to_dat);
		}
		if($from_dat != "" && $to_dat != ""){
			$total_time = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($from_dat, $to_dat))
								->sum('hours_wrk');
		}else{
			$total_time = "";
		}
		return $total_time;
    }
	
	public static function approved_time($id,$frm_date,$t_date)
    {
        $payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		$TodayDate = new DateTime();
		$origin = new DateTime('2020-12-21');
        $interval = $origin->diff($TodayDate);
        $date_diff =  $interval->format('%a');
        if($date_diff == 0){
            	$frm_date = "2020_12_21";
		        $t_date = "2021_01_03";
		         $from_dat  = "2020_12_21";
				  $to_dat = "2021_01_03";
        }
        
		$users_arrr = array();
		$users = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->get();
		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}
		if(!empty($frm_date) && !empty($t_date)){
			$from_dat = explode('-',$frm_date);
				$from_dat = implode('_',$from_dat);
				$to_dat = explode('-',$t_date);
				$to_dat = implode('_',$to_dat);
		}
		
		if($from_dat != "" && $to_dat != ""){
			$approved_time = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($from_dat, $to_dat))
								->where('approve', '=', 2)
								->sum('hours_wrk');
		}else{
			$approved_time = "";
		}
		return $approved_time;
    }
	
	public static function denied_time($id,$frm_date,$t_date)
    {
        $payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		$TodayDate = new DateTime();
		$origin = new DateTime('2020-12-21');
        $interval = $origin->diff($TodayDate);
        $date_diff =  $interval->format('%a');
        if($date_diff == 0){
            	$frm_date = "2020_12_21";
		        $t_date = "2021_01_03";
		         $from_dat  = "2020_12_21";
				  $to_dat = "2021_01_03";
        }
        
		$users_arrr = array();
		$users = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->get();
		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}
		
		if(!empty($frm_date) && !empty($t_date)){
				$from_dat = explode('-',$frm_date);
				$from_dat = implode('_',$from_dat);
				$to_dat = explode('-',$t_date);
				$to_dat = implode('_',$to_dat);
		}
		if($from_dat != "" && $to_dat != ""){
			$denied_time = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($frm_date, $t_date))
								->where('approve', '=', 1)
								->sum('hours_wrk');
		}else{
			$denied_time = "";
		}
		return $denied_time;
    }
	
	public function search_suser_by_comp(Request $request)
	{
		$user_companies = UserManager::where('users_id', '=', $request->comp_id)->get();
		
		$users = array();
		if(isset($user_companies)){
			foreach($user_companies as $user_company){
				$users[] = $user_company->musers_id;
			}
		}
		
		$data = User::where('role', 'user')
						->whereIn('id', $users) 
						->orderBy('name', 'ASC')
						->get();
			if(isset($data)){
$count = 1; 
			  if($data->count() != 0){
				foreach ($data as $user_data){
				$color_info =  $this->color_info($user_data->id); 
					
					
					if($color_info != "") { 
					echo "<tr style='background:".$color_info."'>";
					}else{
						echo "<tr>";
					}
				  echo "<td>".$count."</td>";
				   echo "<td>".$user_data->emp_id."</td>";
				   echo "<td>";
					 echo '<a  style="margin-left: 5px;"  href="'.url('/user/suser/timesheets').'/'.$user_data->id.'" title="Time Sheets"><i class="fa fa-book"></i></a>';
				  echo "</td>";
				    echo "<td>".$user_data->last_name." ".$user_data->first_name."</td>";
				 echo "<td>".$user_data->dept."</td>";
				 $user_companies = $this->user_companies($user_data->id);
				 echo "<td><ul class='comp_list'>".$user_companies."</ul></td>";
				  echo "<td>";
				  if($user_data->last_login_at != null){ echo date('h:i a', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				   echo "<td>";
				  if($user_data->last_login_at != null){ echo date('M d, Y', strtotime($user_data->last_login_at)); }
				  
				  echo "</td>";
				  echo "<td>".date('M d, Y(D)', strtotime($user_data->created_at))."</td>";
				  echo "<td>";
				   if($user_data->status == 1){ echo "<h5 style='color:green'>Active</h5>"; }else{ echo "<h5 style='color:red'>Inactive</h5>"; } 
				   echo "</td>";
				 				    				  echo "<td>";
					$total_time = $this->total_time($user_data->id);
						if($total_time <=  79){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$total_time."</p>";
						}elseif($total_time == 80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$total_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$total_time."</p>";
						}
				  echo "</td>";
				   echo "<td>";
					$approved_time = $this->approved_time($user_data->id);
						if($approved_time <=  79){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$approved_time."</p>";
						}elseif($approved_time == 80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$approved_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$approved_time."</p>";
						}
				  echo "</td>";
				   echo "<td>";
					$denied_time = $this->denied_time($user_data->id);
						if($denied_time <=  79){
							echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#000;background:yellow' >".$denied_time."</p>";
						}elseif($denied_time == 80){
								echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:green' >".$denied_time."</p>";
						}else{
						   echo "<p style='font-size: 15px;font-weight: 700;padding: 5px;border-radius: 20px;color:#fff;background:purple' >".$denied_time."</p>";
						}
				  echo "</td>";
				 echo "</tr>";
				$count++; 
				}
				
			  }else{
					echo "<p>Sorry No Data!!</p>";
			  }
				
				
			}
	
	}
	
	public static function user_companies($id)
    {
		$user_companies = UserManager::where('musers_id', '=', $id)->get();
		$com_out = "";
		if(isset($user_companies)){
			foreach($user_companies as $user_company){
				$company = Company::where('id', '=', $user_company->users_id)->first();
				
				$com_out .= '<li>'.$company->company.'</li>';
			}
		}
		
		return $com_out;
	}
	
	public static function color_info($id)
    {
		$UserSupervisorRel = UserSupervisorRel::where('users_id', $id)->first();
		if(isset($UserSupervisorRel)){
			$color_info = User::where("id", $UserSupervisorRel->supervisor_id)->first();
			if(isset($color_info)){
					$bgcolor = $color_info->color_field;
				}else{
					$bgcolor = "";
				}
		}else{
			$bgcolor = "";
		}
		
		return $bgcolor;
	}
	
}
