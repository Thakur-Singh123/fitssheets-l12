<?php

namespace App\Http\Controllers\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Models\TimeSheet;
use App\Models\User;
use App\Models\House;
use App\Models\Company;
use DateTime;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TimesheetController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
	 
	//Function for show timesheet
    public function index() {
		//Get auth
		$user = Auth::user()->id;
		//Get timesheet
		$data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $user)->orderBy('hours_day', 'DESC')->paginate(10);
		//dd($data);
		return view('user.timesheet.ts_view',compact('data'));
    }
	
	/**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

	//Function for create timesheet
    public function create() {
		//Get auth login
		$user = Auth::user()->id;
		//Get companies
		$Company = Auth::user()->companies_id;
		//$companies = Company::orderBy('created_at', 'DESC')->get();
		//$companies = Company::orderBy('company', 'ASC')->get();
		$companies = Company::orderBy('display_order', 'ASC')->get();
		if($Company == "Quantumleap, Inc"){
			$houses = House::where('companies_id', '=', $Company)->orderBy('created_at', 'DESC')->get();
		}else{
			$Com = array();
			$Com[] = "Quantumleap, Inc";
			$houses = House::whereNotIn('companies_id', $Com)->orderBy('created_at', 'DESC')->get();
		}
        return view('user.timesheet.ts_add', compact('companies','user','houses'));
    }
	
	/**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

	//Function for store timesheet
    public function store(Request $request) {
		//Get auth id
		$user_id = Auth::user()->id;
		//$hours = $request->hours_day;
		//Validate input fields
		$request->validate([
			'company_id' => 'required',
			'house_id' => 'required',
			'hours_day' => 'required',
			'time_in' => 'required',
			'time_out' => 'required',
		]);
		//Get user
		$user = User::where('id', '=', $user_id)->first();
		//Check vacc exists or not
		if(!empty($request->vacc)) {
			$vacc = $request->vacc;
		} else {
			$vacc = 0;
		}
		
		$date = explode('-', $request->hours_day);
		$date = implode("_", $date);
		substr($request->time_in, 0, -2);
		substr($request->time_out, 0, -2);
		$starttimestamp = strtotime($request->time_in);
		$endtimestamp = strtotime($request->time_out);
		//Check if time am and pm
		if (strpos($request->time_in, 'pm') !== false && strpos($request->time_out, 'am') !== false) {
			$hours = (abs(($endtimestamp - $starttimestamp)/3600));
			if($hours < 0) {
				$hours = abs(($starttimestamp - $endtimestamp)/3600);
				$hours = 24-$hours;
			} else {
				$hours = 24-$hours;
			}
		} elseif($starttimestamp == $endtimestamp) {
			$hours = 24;
		} else {
			$hours = (abs(($endtimestamp - $starttimestamp)/3600));
		}
	   	//Check if timesheet exists or not
		$hasExpenseSavedForUser = TimeSheet::where('hours_day','=', $date)
			->where('users_id','=', $request->user_id)
			->where('time_in','=', $request->time_in)
			->exists();
		//Check if timesheets aleady exists or not
		if ($hasExpenseSavedForUser) {
			return back()->withErrors([
			    'hours_day' => 'Time already saved for this user on this date'
			]);
		} else {
			$form_data = array(
				'companies_id' => $request->company_id,
				'houses_id' => $request->house_id,
				'users_id' => $request->user_id,
				'hours_day' => $date,
				'time_in'  => $request->time_in,
				'time_out'  => $request->time_out,
				'hours_wrk' => $hours,
				'vacation_status' => $vacc,
				'cmcheck_status' => '1'
			);
			//Create timesheet
			$ts_store = TimeSheet::create($form_data);
			//Check if timesheet created or not
			if($ts_store){
				return redirect('/time-sheets')->with(['success' => 'Hours added successfully.']);
			}else{
				return redirect()->back()->with(['error' => 'Error while adding hours!!']);
			}
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

	//Function for edit timesheet
    public function edit($id) {
		//Get auth id
		$user = Auth::user()->id;
		$Company = Auth::user()->companies_id;
		//Get companies
		$companies = Company::orderBy('display_order', 'ASC')->get();
		if($Company == "Quantumleap, Inc") {
			$houses = House::where('companies_id', '=', $Company)->orderBy('created_at', 'DESC')->get();
		} else {
			$Com = array();
			$Com[] = "Quantumleap, Inc";
			$houses = House::whereNotIn('companies_id', $Com)->orderBy('created_at', 'DESC')->get();
		}
		//Get timesheet
		$data = TimeSheet::where('id', '=', $id)->get();

		return view('user.timesheet.ts_edit',compact('data','companies','user','houses'));
    }
	
	/**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

	//Function for update timesheet
    public function update(Request $request) {
        //Validation
		$request->validate([
			'company_id' => 'required',
			'house_id' => 'required',
			'hours_day' => 'required',
			'time_in' => 'required',
			'time_out' => 'required',
		]);
		if(!empty($request->vacc)) {
			$vacc = $request->vacc;
		} else {
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
		} elseif($starttimestamp == $endtimestamp) {
			$hours = 24;
		} else {
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
			'vacation_status' => $vacc,
		);
		//Update timesheets
		$ts_update = TimeSheet::whereId($request->hidden_id)->update($form_data);
        //Check if timesheet updated or not
		if($ts_update){
			return redirect('/time-sheets')->with(['success' => 'Hours updated successfully.']);
		}else{
			return redirect()->back()->with(['error' => 'Error while updating hours!!']);
		}
    }
	
	/**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

	public function destroy(Request $request) {
	  //Get ajax request for timesheet id
        $timesheet_id = $request->timesheet_id;
        //Delete timesheet
        $is_delete_timesheet = TimeSheet::where('id', $timesheet_id)->delete();
        //Check if timesheet delete or not
        if ($is_delete_timesheet) {
            echo '<p style="color:green;">Timesheet record delete succeesfully.</p>';
            echo '<script>setTimeout(function(){ window.location.href = ""; }, 3000);</script>';
        } else {
            echo '<p style="color:red;">Oops something wrong.</p>';
        } 
	}

	//Function for search time
	public function srch_time(Request $request) {
		//Get dates
		$from_date    = explode('-', $request->frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $request->todate);
		$to_date = implode("_", $to_date);
		$user = Auth::user()->id;
	    //Check if to date
		if($from_date == $to_date) {
			$data = TimeSheet::with('companies')
				->with('users')
				->with('houses')
				->where('hours_day', $from_date)
				->where('users_id', '=', $user)
				->orderBy('created_at', 'DESC')
				->get();
		} else {
			$data = TimeSheet::with('companies')
				->with('users')
				->with('houses')
				->where('users_id', '=', $user)
				->whereBetween('hours_day', array($from_date, $to_date))
				->orderBy('created_at', 'DESC')
				->get();
		}
		//var
		$count = 1;
		$total_hours = 0;
		$approved_hours = 0;
		$denied_hours = 0;
			if($data->count() != 0) {
				foreach ($data as $datas) {
					$total_hours = $total_hours + $datas->hours_wrk;
					echo '<tr>';
					echo '<td>'.$count.'.</td>';
					echo '<td >'.$datas->users->emp_id.'</td>';
					echo '<td>'.$datas->users->email.'</td>';
					echo '<td >'.$datas->users->name.'</td>';
					echo '<td>'.$datas->users->dept.'</td>';
					echo '<td>'.$datas->companies->company.'</td>';
					echo '<td>'.$datas->houses->house_add.'</td>';
					echo '<td>'.$datas->time_in.'</td>';
					echo '<td>'.$datas->time_out.'</td>';
					$hours    = explode('_', $datas->hours_wrk);
					$hours = implode(":", $hours);
					echo '<td>'. $hours.'</td>';
					$hours_day    = explode('_', $datas->hours_day);$hours_day = implode("/", $hours_day); $hours_day = date("M d, Y", strtotime($hours_day));
					echo '<td>'. $hours_day.'</td>';
					echo '<td>'. $datas->users->hourst_rate.'</td>';
					// echo '<td>';
					// if($datas->vacation_status == "0"){ echo "<h5 style='color:green'>No</h5>"; }elseif($datas->vacation_status == "1"){ echo "<h5 style='color:red'>Yes</h5>"; }
					// echo '</td>';
					echo  '<td>';
					if($datas->approve == "2"){ $approved_hours = $approved_hours + $datas->hours_wrk; echo "<h5 style='color:green'>Yes</h5>"; }
					elseif($datas->approve == "1"){ $denied_hours = $denied_hours + $datas->hours_wrk; echo "<h5 style='color:red'>No</h5>"; }
					else{ echo "<h5 style='color:#a5a548'>Pending</h5>"; } 
					echo '</td>';
					echo '<td>'. $datas->remarks.'</td>';
					echo '<td>';
					echo '<a href="' . route('time-sheets.edit', $datas->id) . '" title="Edit">
							<i class="fa fa-pencil"></i>
						</a>';
					echo '<a style="margin-left:10px;" 
							href="javascript:void(0);" 
							class="delete_timesheet_record"
							data-timesheet_id="'.$datas->id.'"
							title="Delete">
							<i class="fa fa-trash-o"></i>
						</a>';
					echo '</td>';
					echo '</tr>';
				    $count++;
				}
				    echo '<tr>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td>Payperiod</td>';
					echo '<td>'.date("d", strtotime($from_date)).'-'.date("d M", strtotime($to_date)).'</td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo  '<td></td>';
					echo  '<td></td>';
					echo '</tr>';
					echo '<tr>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td>Total Hours Worked</td>';
					echo '<td>'.$total_hours.'</td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo  '<td></td>';
					echo  '<td></td>';
					echo '</tr>';
					echo '<tr>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td>Approved Hours</td>';
					echo '<td>'.$approved_hours.'</td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo  '<td></td>';
					echo  '<td></td>';
					echo '</tr>';
					echo '<tr>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td>Declined Hours</td>';
					echo '<td>'.$denied_hours.'</td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo '<td></td>';
					echo  '<td></td>';
					echo  '<td></td>';
					echo '</tr>';
			} else {
					echo '
					<tr>
						<td colspan="16" class="no-data">
							Sorry, No data found!
						</td>
					</tr>
				';
			}  
    }
	
	/**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

	//Fuction for export timesheet data
    public function export_data() {
		//Get auth id
		$user = Auth::user()->id;
		//Get timesheet
        $data = TimeSheet::with('companies')->with('users')->with('houses')->where('users_id', '=', $user)->orderBy('hours_day', 'DESC')->get();
		$time_sheet[] = array('Sr No.','Email','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved');
		$count = 1.;
		foreach($data as $datas) {
			$hours_day    = explode('_', $datas->hours_day);
			$hours_day = implode("/", $hours_day); 
			$hours_day = date("M d, Y", strtotime($hours_day)); 
			if($datas->vacation_status == "0") { 
			$vacation_status = "No"; 
			} elseif($datas->vacation_status == "1") {
				$vacation_status = "Yes";
			} else {
				$vacation_status = "";
			}	
			if($datas->approve == "2") { 
					$approve = "Yes";
			} elseif($datas->approve == "1") {
				$approve = "No";
			} else {
				$approve = "Pending"; 
			}
			$time_sheet[] = array(
				'Sr No.' => $count,
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
		//spreadsheet
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'Time Sheet Records');
		$sheet->mergeCells('A1:M1');
		$sheet->fromArray($time_sheet, null, 'A2');

		$sheet->getStyle('A1:M1')->getFont()->setBold(true)->setSize(14);
		$sheet->getStyle('A1:M1')->getAlignment()->setHorizontal('center');

		$sheet->getStyle('A2:M2')->getFont()->setBold(true);

		foreach (range('A', 'M') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		return response()->streamDownload(function () use ($spreadsheet) {
			(new Xlsx($spreadsheet))->save('php://output');
		}, 'Time_Sheet.xlsx');
    }
    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

	//Function for srch export data
    public function srcexport_data($frmdate,$todate) {
		//Get auth id
		$user = Auth::user()->id;
        $from_date    = explode('-', $frmdate);
		$from_date = implode("_", $from_date);
		$to_date    = explode('-', $todate);
		$to_date = implode("_", $to_date);
		if($from_date == $to_date) {
			$data = TimeSheet::with('companies')
				->with('houses')
				->with('users')
				->where('hours_day', $from_date)
				->where('users_id', '=', $user)
				->orderBy('created_at', 'DESC')
				->get();
		} else {
			$data = TimeSheet::with('companies')
				->with('houses')
				->with('users')
				->where('users_id', '=', $user)
				->whereBetween('hours_day', array($from_date, $to_date))
				->orderBy('created_at', 'DESC')
				->get();
		}
		$time_sheet[] = array('Sr No.','Emp ID','Email','Name', 'Department','Company','House', 'Time In', 'Time Out', 'Hours Worked', 'Day', 'Hours Rate', 'Vacation', 'Approved', 'Approved By');
		$count = 1;
		$total_hours = 0;
		$approved_hours = 0;
		$denied_hours = 0;
		if($data->count() != 0) {
			foreach($data as $datas) {
				$hours_day = explode('_', $datas->hours_day);
				$hours_day = implode("/", $hours_day); 
				$hours_day = date("M d, Y", strtotime($hours_day)); 
				//$total_hour = $total_hours + $datas->hours_wrk;
				$total_hours += $datas->hours_wrk;
				if($datas->vacation_status == "0") { 
					$vacation_status = "No"; 
				} elseif($datas->vacation_status == "1") {
					$vacation_status = "Yes";
				} else {
					$vacation_status = "";
				}
				if($datas->approve == "2") { 
						$approve = "Yes";
						$approved_hours	= $approved_hours + $datas->hours_wrk;
				} elseif($datas->approve == "1") {
					$approve = "No";
					$denied_hours= $denied_hours + $datas->hours_wrk;
				} else {
					$approve = "Pending"; 
				} 
				$time_sheet[] = array(
					'Sr No.' => $count,
					'Emp ID'  => $datas->users->emp_id,
					'Email'  => $datas->users->email,
					'Name'  => $datas->users->name,
					'Department'   => $datas->users->dept,
					'Company'  => $datas->companies->company,
					'House'  => $datas->houses->house_add,
					'Time In'   => $datas->time_in,
					'Time Out'   => $datas->time_out,
					'Hours Worked'   => $datas->hours_wrk,
					'Day'    => $hours_day,
					'Hours Rate'  => $datas->users->hourst_rate ,
					'Vacation'   => $vacation_status,
					'Approved'    => $approve,
					'Approved By' => $datas->approved_by
				);
				$count++;
			}
			if($total_hours != 0){
				$time_sheet[] = array(
					'Sr No.' => "",
					'Emp ID'  => "",
					'Email'  => "",
					'Name'  => "",
					'Department'   => "",
					'Company'  => "",
					'House'  => "",
					'Time In'   => "",
					'Time Out'   => "Total Hours",
					'Hours Worked'   => $total_hours,
					'Day'    => "",
					'Hours Rate'  => "" ,
					'Vacation'   => "",
					'Approved'    => "",
					'Approved By' => ""
				);
				$time_sheet[] = array(
					'Sr No.' => "",
					'Emp ID'  => "",
					'Email'  => "",
					'Name'  => "",
					'Department'   => "",
					'Company'  => "",
					'House'  => "",
					'Time In'   => "",
					'Time Out'   => "Approval",
					'Hours Worked'   => "Approved",
					'Day'    => "",
					'Hours Rate'  => "" ,
					'Vacation'   => "",
					'Approved'    => "",
					'Approved By' => ""				
				);
				$time_sheet[] = array(
					'Sr No.' => "",
					'Emp ID'  => "",
					'Email'  => "",
					'Name'  => "",
					'Department'   => "",
					'Company'  => "",
					'House'  => "",
					'Time In'   => "",
					'Time Out'   => "Hours",
					'Hours Worked'   =>  $approved_hours,
					'Day'    => $denied_hours,
					'Hours Rate'  => "" ,
					'Vacation'   => "",
					'Approved'    => "",
					'Approved By' => ""
				);
					$time_sheet[] = array(
					'Sr No.' => "",
					'Emp ID'  => "",
					'Email'  => "",
					'Name'  => "",
					'Department'   => "",
					'Company'  => "",
					'House'  => "",
					'Time In'   => "",
					'Time Out'   => "Payperiod",
					'Hours Worked'   =>  date("d", strtotime($frmdate)).'-'.date("d M", strtotime($todate)),
					'Day'    => "",
					'Hours Rate'  => "" ,
					'Vacation'   => "",
					'Approved'    => "",
					'Approved By' => ""
				);
				$time_sheet[] = array(
					'Sr No.' => "",
					'Emp ID'  => "",
					'Email'  => "",
					'Name'  => "",
					'Department'   => "",
					'Company'  => "",
					'House'  => "",
					'Time In'   => "",
					'Time Out'   => "",
					'Hours Worked'   =>  "",
					'Day'    => "",
					'Hours Rate'  => "" ,
					'Vacation'   => "",
					'Approved'    => "",
					'Approved By' => ""
				);	 
			}
		}
		//spreadsheet
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Time Sheet Records');

		$sheet->mergeCells('A1:O1');
		$sheet->fromArray($time_sheet, null, 'A2');

		$sheet->getStyle('A1:O1')->getFont()->setBold(true)->setSize(14);
		$sheet->getStyle('A1:O1')->getAlignment()->setHorizontal(
			\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
		);

		$sheet->getStyle('A2:O2')->getFont()->setBold(true);

		foreach (range('A', 'O') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		$sheet->freezePane('A3');
		
		return response()->streamDownload(function () use ($spreadsheet) {
			(new Xlsx($spreadsheet))->save('php://output');
		}, 'Time_Sheet.xlsx');
    }
	
	/**
    * Search Timesheet View
    */
	//Function for search time view
    public function srch_time_view() {
		return view('user.timesheet.ts_search');
    }
	
	/**
    * Search Timesheet View
    */
	//Function for house
    public function house_com($company_id) { 
        //Get companies
		$companies = Company::where('id', '=', $company_id)->first();
		$com_name = $companies->company;
		//Get houses
		$houses = House::where('companies_id', '=', $com_name)->orderBy('created_at', 'DESC')->get();
		//Check if house exists or not
		if(isset($houses)) {
			foreach($houses as $house) {
				echo '<option value="'.$house->id.'" >'.$house->house_add.'</option>';
			}
		}
    }
	
	//Function fot totalhours
	public function totalhours($time_in,$time_out) {
		//Get time & time out
	    substr($time_in, 0, -2);
		substr($time_out, 0, -2);
		$starttimestamp = strtotime($time_in);
		$endtimestamp = strtotime($time_out);
		if (strpos($time_in, 'pm') !== false && strpos($time_out, 'am') !== false) {
			$hours = (abs(($endtimestamp - $starttimestamp)/3600));
			if($hours < 0) {
				$hours = abs(($starttimestamp - $endtimestamp)/3600);
				$hours = 24-$hours;
			} else {
				$hours = 24-$hours;
			}
		} elseif($starttimestamp == $endtimestamp) {
			$hours = 24;
		} else {
			$hours = (abs(($endtimestamp - $starttimestamp)/3600));
		}
		echo $hours;		
    }
}
