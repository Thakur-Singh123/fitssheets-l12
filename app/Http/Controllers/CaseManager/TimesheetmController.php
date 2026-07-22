<?php

namespace App\Http\Controllers\CaseManager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Excel;
use App\TimeSheet;
use App\User;
use App\Company;
use App\House;
use Carbon\Carbon;

class TimesheetmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $user)->orderBy('hours_day', 'DESC')->paginate(15);
		return view('user.timesheet.ts_view',compact('data'));
    }
	
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
		$user = $id;
		$companies = Company::orderBy('created_at', 'DESC')->get();
		$houses = House::orderBy('created_at', 'DESC')->get();
        return view('manager.timesheet.ts_add', compact('companies','user','houses'));
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
			'company_id'     =>  'required',
			'house_id'     =>  'required',
			'hours_day'    =>  'required',
			'time_in' 	   =>  'required',
			'time_out' 	   =>  'required',
		];
		$customMessages = [
			'company_id'     =>  'Please select company',
			'house_id'     =>  'Please select house',
			'hours_day'    =>  'Please add date',
			'time_in' 	   =>  'required',
			'time_out' 	   =>  'required',
		];
		$this->validate($request, $rules, $customMessages);
				
		if(!empty($request->vacc)){
			$vacc = $request->vacc;
		}else{
			$vacc = 0;
		}
		$date    = explode('-', $request->hours_day);
		$date = implode("_", $date);
		substr($request->time_in, 0, -2);
		substr($request->time_out, 0, -2);
		$starttimestamp = strtotime($request->time_in);
		$endtimestamp = strtotime($request->time_out);
		if (strpos($request->time_in, 'pm') !== false && strpos($request->time_out, 'am') !== false) {
				$hours = (abs(($endtimestamp - $starttimestamp)/3600));
				if($hours < 0){
					$hours = abs(($starttimestamp - $endtimestamp)/3600);
					$hours = 24-$hours;
				}else{
					$hours = 24-$hours;
				}
		}elseif($starttimestamp == $endtimestamp){
			$hours = 24;
		}else{
			$hours = (abs(($endtimestamp - $starttimestamp)/3600));
		}
		$form_data = array(
				'companies_id' => $request->company_id,
				'houses_id' => $request->house_id,
				'users_id' => $request->user_id,
				'hours_day' => $date,
				'time_in'  => $request->time_in,
				'time_out'  => $request->time_out,
				'hours_wrk' => $hours,
				'hours_price' => $request->hour_rate,
				'vacation_status' => $vacc,
				'approve' => $request->approved,
		);
		
		
		$ts_store = TimeSheet::create($form_data);
			
		if($ts_store){
			return redirect('/muser/timesheets/'.$request->user_id)->with(['success' => 'Hours Added Successfully!!']);
		}else{
			return redirect()->back()->with(['success' => 'Error while Adding Hours!!']);
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
    public function edit($id,$frm_dt,$to_dt)
    {

		$companies = Company::orderBy('created_at', 'DESC')->get();
		$houses = House::orderBy('created_at', 'DESC')->get();
		$data = TimeSheet::where('id', '=', $id)->get();
		return view('casemanager.timesheet.ts_edit',compact('data','companies','houses','id','frm_dt','to_dt'));
		
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
			'company_id'     =>  'required',
			'house_id'     =>  'required',
			'hours_day'    =>  'required',
			'time_in' 	   =>  'required',
			'time_out' 	   =>  'required',
		];
		$customMessages = [
			'company_id'     =>  'Please select company',
			'house_id'     =>  'Please select house',
			'hours_day'    =>  'Please add date',
			'time_in' 	   =>  'required',
			'time_out' 	   =>  'required',
		];
		$this->validate($request, $rules, $customMessages);
		$this->validate($request, $rules, $customMessages);
		if(!empty($request->vacc)){
			$vacc = $request->vacc;
		}else{
			$vacc = 0;
		}
		$date    = explode('-', $request->hours_day);
		$date = implode("_", $date);
		substr($request->time_in, 0, -2);
		substr($request->time_out, 0, -2);
		$starttimestamp = strtotime($request->time_in);
		$endtimestamp = strtotime($request->time_out);
		if (strpos($request->time_in, 'pm') !== false && strpos($request->time_out, 'am') !== false) {
				$hours = (abs(($endtimestamp - $starttimestamp)/3600));
				if($hours < 0){
					$hours = abs(($starttimestamp - $endtimestamp)/3600);
					$hours = 24-$hours;
				}else{
					$hours = 24-$hours;
				}
		}elseif($starttimestamp == $endtimestamp){
			$hours = 24;
		}else{
			$hours = (abs(($endtimestamp - $starttimestamp)/3600));
		}
		$user = Auth::user()->id;
		$form_data = array(
				'companies_id' => $request->company_id,
				'houses_id' => $request->house_id,
				'users_id' => $request->user_id,
				'hours_day' => $date,
				'time_in'  => $request->time_in,
				'time_out'  => $request->time_out,
				'hours_wrk' => $hours,
				'hours_price' => $request->hour_rate,
				'vacation_status' => $vacc,
				'cm_id' => $user,
				'cmcheck_status' => $request->cm_status,
				'cm_update_at' => Carbon::now()->toDateTimeString(),
		);
		$ts_update = TimeSheet::whereId($request->hidden_id)->update($form_data);

		if($ts_update){
			
			if(!empty($request->frm_dtt) && !empty($request->to_dtt)){
				return redirect('/cmuser/timesheets/'.$request->user_id.'/'.$request->frm_dtt.'/'.$request->to_dtt)->with(['success' => 'Hours Updated Successfully!!']);
			}else{
				return redirect('/cmuser/timesheets/'.$request->user_id)->with(['success' => 'Hours Updated Successfully!!']);
			}
			
		}else{
			return redirect()->back()->with(['success' => 'Error while updating Hours!!']);
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
        $data = TimeSheet::findOrFail($id);
        $data->delete();
    }
	
	public function srch_time(Request $request)
    {
		// print_r($request->all());
		// die;
		$user = $request->user_id;
		$from_date    = explode('-', $request->frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $request->todate);
		$to_date = implode("_", $to_date);
		
	
if($from_date == $to_date){
			$data = TimeSheet::with('companies')
								->with('users')
								->with('houses')
								->where('hours_day', $from_date)
								->where('users_id', '=', $user)
								->orderBy('created_at', 'DESC')
								->get();
		}else{
			$data = TimeSheet::with('companies')
								->with('users')
								->with('houses')
								->where('users_id', '=', $user)
								->whereBetween('hours_day', array($from_date, $to_date))
								->orderBy('created_at', 'DESC')
								->get();
		}
		 $count = 1;
			  if($data->count() != 0){
				foreach ($data as $datas){
					echo '<tr>';
					  echo '<td>'.$count.'</td>';
					  echo '<td>'.$datas->users->emp_id.'</td>';
					 echo '<td>';
						echo '<a  href="'.url('/cmuser/edit/timesheets/').'/'.$datas->id.'/'.$from_date.'/'.$to_date.'" title="Edit"><i class="fa fa-pencil"></i></a>';
						echo '<a style="margin-left: 5px;" data-baseURL="'.url('/').'" data-ID="'.$datas->id.'" class="delete_mts" title="Delete"><i class="fa fa-trash-o"></i></a>';
					  echo '</td>';
					  echo '<td><input type="checkbox" class="time_id"  value="'.$datas->id.'"';
						if($datas->cmcheck_status == 2){ echo "checked "; }
						echo 'name="time_id[]" ></td>';
					  echo '<td><input type="checkbox" class="time_idd"  value="'.$datas->id.'"';
						if($datas->cmcheck_status == 1){ echo "checked "; }
					  echo 'name="time_idd[]" ></td>';
					  echo '<td><input type="checkbox" class="time_idde"  value="'.$datas->id.'" name="time_idde[]" ></td>';
						
						echo '<td>'.$datas->users->name.'</td>';
					   echo '<td>'.$datas->users->dept.'</td>';
					  echo '<td>'.$datas->companies->company.'</td>';
					   echo '<td>'.$datas->houses->house_add.'</td>';
					    echo '<td>'.$datas->time_in.'</td>';
					  echo '<td>'.$datas->time_out.'</td>';
					  echo '<td>';
					  $hours    = explode('_', $datas->hours_wrk);$hours = implode(":", $hours); 
					  echo $hours;
					  echo '</td>';
					   echo '<td>';
					   $hours_day    = explode('_', $datas->hours_day);$hours_day = implode("/", $hours_day); $hours_day = date("M d, Y", strtotime($hours_day)); 
					   echo $hours_day;
					   echo '</td>';
					  echo '<td>'.$datas->users->hourst_rate.'</td>';
					 echo '<td>'.$datas->vacation_status.'</td>';
					  echo '<td>';
					  if($datas->approve == "2"){ echo "<h5 style='color:green'>Yes</h5>"; }elseif($datas->approve == "1"){ echo "<h5 style='color:red'>No</h5>"; }else{ echo "<h5 style='color:#a5a548'>Pending</h5>"; } 
					  echo '</td>';
					
					echo '</tr>';
				$count++;
				}
			  }else{
					echo "<p>Sorry No Data!!</p>";
			  }
    }
	
		/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export_data($id)
    {
		$user = $id;
        $data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $user)->orderBy('hours_day', 'DESC')->get();
		$time_sheet[] = array('#','Email','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved');
		$count = 1;
		foreach($data as $datas)
		{
			 $hours_day    = explode('_', $datas->hours_day);
			 $hours_day = implode("/", $hours_day); 
			 $hours_day = date("M d, Y", strtotime($hours_day)); 
			 if($datas->vacation_status == "0"){ 
				$vacation_status = "No"; 
			 }elseif($datas->vacation_status == "1"){
				 $vacation_status = "Yes";
				}else{
					$vacation_status = "";
				}
					 
			if($datas->approve == "2"){ 
					$approve = "Yes";
			}elseif($datas->approve == "1"){
				$approve = "No";
			}else{
				$approve = "Pending"; 
			}
			$time_sheet[] = array(
				'#' => $count,
				'Email'  => $datas->users->email,
				'Name'  => $datas->users->name,
				'Department'   => $datas->users->dept,
				'Company'  => $datas->companies->company,
				'House'  => $datas->houses->house_add,
				'Time In'   => $datas->time_in,
				'Time Out'   => $datas->time_out,
				'Hours Worked'   => $datas->hours_wrk,
				'Day'    => $hours_day,
				'Hours Rate'  => $datas->users->hourst_rate,
				'Vacation'   => $vacation_status,
				'Approved'    => $approve
			);
			$count++;
		}
		Excel::create('Time Sheet', function($excel) use ($time_sheet){
			$excel->setTitle('Time Sheet');
			$excel->sheet('Time Sheet', function($sheet) use ($time_sheet){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
			});
		})->download('xlsx');
    }
	
	
		/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function srcexport_data($frmdate,$todate, $id)
    {
		$user = $id;
        $from_date    = explode('-', $frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $todate);
		$to_date = implode("_", $to_date);
		
	
		if($from_date == $to_date){
			$data = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('hours_day', $from_date)
								->where('users_id', '=', $user)
								->orderBy('created_at', 'DESC')
								->get();
		}else{
			$data = TimeSheet::with('companies')
								->with('houses')
								->with('users')
								->where('users_id', '=', $user)
								->whereBetween('hours_day', array($from_date, $to_date))
								->orderBy('created_at', 'DESC')
								->get();
		}
		
		$time_sheet[] = array('#','Email','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved');
		$count = 1;
		foreach($data as $datas)
		{
			 $hours_day    = explode('_', $datas->hours_day);
			 $hours_day = implode("/", $hours_day); 
			 $hours_day = date("M d, Y", strtotime($hours_day)); 
			 if($datas->vacation_status == "0"){ 
				$vacation_status = "No"; 
			 }elseif($datas->vacation_status == "1"){
				 $vacation_status = "Yes";
				}else{
					$vacation_status = "";
				}
				
			if($datas->approve == "2"){ 
					$approve = "Yes";
			}elseif($datas->approve == "1"){
				$approve = "No";
			}else{
				$approve = "Pending"; 
			}
			$time_sheet[] = array(
				'#' => $count,
				'Email'  => $datas->users->email,
				'Name'  => $datas->users->name,
				'Department'   => $datas->users->dept,
				'Company'  => $datas->companies->company,
				'House'  => $datas->houses->house_add,
				'Time In'   => $datas->time_in,
				'Time Out'   => $datas->time_out,
				'Hours Worked'   => $datas->hours_wrk,
				'Day'    => $hours_day,
				'Hours Rate'  => $datas->users->hourst_rate,
				'Vacation'   => $vacation_status,
				'Approved'    => $approve
			);
			$count++;
		}
		Excel::create('Time Sheet', function($excel) use ($time_sheet){
			$excel->setTitle('Time Sheet');
			$excel->sheet('Time Sheet', function($sheet) use ($time_sheet){
				$sheet->fromArray($time_sheet, null, 'A1', false, false);
			});
		})->download('xlsx');
    }


	public function approve_time(Request $request)
    {
		$data = json_decode($request->ids_value, true);
		$user = Auth::user()->id;
		if(isset($data)){
			foreach($data as $datas){
				$form_data = array(
					'cmcheck_status' => 2,
					'cm_id' => $user,
					'cm_update_at' => Carbon::now()->toDateTimeString(),
				);
				$ts_update = TimeSheet::whereId($datas)->update($form_data);
			}
		}
		echo "Approved Successfully";
	}
	
	public function decline_time(Request $request)
    {
		$data = json_decode($request->ids_value, true);
		$user = Auth::user()->id;
		if(isset($data)){
			foreach($data as $datas){
				$form_data = array(
					'cmcheck_status' => 1,
					'cm_id' => $user,
					'cm_update_at' => Carbon::now()->toDateTimeString(),
				);
				$ts_update = TimeSheet::whereId($datas)->update($form_data);
			}
		}
		echo "Declined Successfully";
	}
	
	public function delete_time(Request $request)
    {
		$data = json_decode($request->data, true);
		if(isset($data)){
			foreach($data as $datas){
				$data = TimeSheet::findOrFail($datas);
				$data->delete();;
			}
		}
		echo "Deleted Successfully";
	}
}
