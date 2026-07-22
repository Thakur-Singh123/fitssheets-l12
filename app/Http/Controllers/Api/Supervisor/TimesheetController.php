<?php

namespace App\Http\Controllers\Api\Supervisor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Excel;
use App\TimeSheet;
use App\User;
use App\Company;
use App\House;
use App\UserManager;
use Carbon\Carbon;
use App\UserSupervisorRel;
use App\UserCasemanagerRel;
use App\UserVaccatioStatusn;
use App\UserVaccation;
use Validator;

class TimesheetController extends Controller
{
    //Function for companies
    public function companies() {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        $companies = Company::orderBy('display_order', 'ASC')
            ->get(['id', 'company']);

        //Response
        if ($companies->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Companies not found',
                'data' => []
            ], 404);
        }
        
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Companies list fetched successfully',
            'data' => [
                'company' => $companies
            ]
        ]);
    }

    //Function for company services
    public function company_services() {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get companies
        $company_services = House::orderBy('created_at', 'ASC')
            ->get(['id', 'companies_id', 'house_add']);

        if ($company_services->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Company service not found',
                'data' => []
            ], 404);
        } 
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Company service list fetched successfully',
            'data' => [
                'company_services' => $company_services
            ]
        ]);
    }

    //Function for update timesheet
    public function update(Request $request, $id) {
        //Auth check
        $user = request()->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Check record exists
        $timesheet = TimeSheet::find($id);
        //Check timesheet exists or not
        if (!$timesheet) {
            return response()->json([
                'status' => false,
                'message' => 'No record found'
            ], 404);
        }
        //Validation
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'house_id'   => 'required',
            'hours_day'  => 'required',
            'time_in'    => 'required',
            'time_out'   => 'required',
        ]);
        //Response
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        //Vacation status
        $vacc = !empty($request->vacc) ? $request->vacc : 0;
        //Date convert
        $date = explode('-', $request->hours_day);
        $date = implode('_', $date);

        //Hours calculation
        $starttimestamp = strtotime($request->time_in);
        $endtimestamp = strtotime($request->time_out);
        if (
            strpos($request->time_in, 'pm') !== false &&
            strpos($request->time_out, 'am') !== false
        ) {
            $hours = abs(($endtimestamp - $starttimestamp) / 3600);
            if ($hours < 0) {
                $hours = abs(($starttimestamp - $endtimestamp) / 3600);
                $hours = 24 - $hours;
            } else {
                $hours = 24 - $hours;
            }
        } elseif ($starttimestamp == $endtimestamp) {
            $hours = 24;
        } else {
            $hours = abs(($endtimestamp - $starttimestamp) / 3600);
        }
        //Update data
        $form_data = [
            'companies_id'    => $request->company_id,
            'houses_id'       => $request->house_id,
              'users_id'        => $timesheet->users_id, 
            'hours_day'       => $date,
            'time_in'         => $request->time_in,
            'time_out'        => $request->time_out,
            'hours_wrk'       => $hours,
            'hours_price'     => $request->hour_rate,
            'vacation_status' => $vacc,
            'approve'         => $request->approved,
        ];
        //Update record
        $ts_update = $timesheet->update($form_data);
        //Check if data updated or not
        if ($ts_update) {
            return response()->json([
                'status' => true,
                'message' => 'Hours Updated Successfully',
                'data' => $timesheet->fresh()
            ]);
        }
        //Response
        return response()->json([
            'status' => false,
            'message' => 'Error while updating hours'
        ], 500);
    }

    //Functino for delete time sheet
    public function destroy($id) {
        //Auth check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Find record
        $timesheet = TimeSheet::find($id);
        //Response
        if (!$timesheet) {
            return response()->json([
                'status' => false,
                'message' => 'No record found'
            ], 404);
        }
        //Delete
        $timesheet->delete();
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Timesheet deleted successfully'
        ]);
    }

    //Function for approve timesheet
    public function approve_time(Request $request) {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get data json
        $data = json_decode($request->ids_value, true);
        //Get auth user
        $user = Auth::user()->id;

        //Check data exists or not
        if(isset($data)) {
            foreach($data as $datas){
                //Check already approved
                $check = TimeSheet::where('id', $datas)
                    ->where('approve', 2)
                    ->first();
                //Check if already approve or not
                if($check){
                    return response()->json([
                        'status' => false,
                        'message' => 'Already approved timesheet, please decline first then approve it.'
                    ]);
                }
                $form_data = [
                    'remarks' => "",
                    'approve' => 2,
                    'approved_by' => $user,
                    'approved_at' => Carbon::now()->toDateTimeString(),

                ];
                //Update status
                TimeSheet::whereId($datas)->update($form_data);
            }
            //Response
            return response()->json([
                'status' => true,
                'message' => 'Approved Successfully'
            ]);
        }
        //Response
        return response()->json([
            'status' => false,
            'message' => 'No data found'
        ]);
    }

    //Function for decline timesheet
    public function decline_time(Request $request) {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get data json
        $data = json_decode($request->ids_value, true);
        $dec_msg = $request->dec_msg;
        //Get auth
        $user = Auth::user()->id;

        //Check data exists or not
        if(isset($data)){
            foreach($data as $datas){
                //Check already declined
                $check = TimeSheet::where('id', $datas)
                    ->where('approve', 1)
                    ->first();
                //Check if timesheet already declined or not
                if($check){
                    return response()->json([
                        'status' => false,
                        'message' => 'Already declined timesheet, please approved first then declined it.'
                    ]); 
                }
                $form_data = [
                    'remarks' => $dec_msg,
                    'approve' => 1,
                    'approved_by' => $user,
                    'approved_at' => Carbon::now()->toDateTimeString(),
                ];

                //Update status
                TimeSheet::whereId($datas)->update($form_data);
            }

            //Response
            return response()->json([
                'status' => true,
                'message' => 'Declined Successfully'
            ]);
        }

        //Response
        return response()->json([
            'status' => false,
            'message' => 'No data found'
        ]);
    }

    //Functino for delete timesheet
    public function delete_time(Request $request) {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get data
        $data = json_decode($request->data, true);
        //Check if data exists or not
        if(isset($data)) {
            foreach($data as $datas) {
                //Get timesheet
                $timesheet = TimeSheet::find($datas);
                if($timesheet) {
                    $timesheet->delete();
                }
            }
            //Response
            return response()->json([
                'status' => true,
                'message' => 'Deleted Successfully'
            ]);
        }
        //Response
        return response()->json([
            'status' => false,
            'message' => 'No data found'
        ]);
    }

    //Function for search payperiod
    public function search_payperiod(Request $request) {
        //Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Current payperiod
        $payperiods_dates = payperiods();
        if(isset($payperiods_dates) && count($payperiods_dates) > 0){
            //Convert - to _
            $from_date = str_replace(
                '-',
                '_',
                $payperiods_dates[0]['frm_date']
            );
            $to_date = str_replace(
                '-',
                '_',
                $payperiods_dates[0]['t_date']
            );
        }else{

            return response()->json([
                'status' => false,
                'message' => 'Payperiod not found'
            ]);
        }
        //Custom date filter
        if($request->frmdate && $request->todate){
            $from_date = explode('-', $request->frmdate);
            $from_date = implode("_", $from_date);
            $to_date = explode('-', $request->todate);
            $to_date = implode("_", $to_date);
        }
        //Company filter
        $ssearch_by_comp = $request->ssearch_by_comp;
        //Login user
        $user = $auth->id;
        $usersm = User::where('id', $user)->first();
        $user_f_name = $usersm->first_name ?? '';
        //var
        $company_id = [];
        $user_idss = [];

        //Company wise filter
        if(isset($ssearch_by_comp) && $ssearch_by_comp != 0){
            $companies = UserManager::where('users_id', $ssearch_by_comp)
                ->where('musers_id', $user)
                ->get();
            if(isset($companies)){
                foreach($companies as $company){
                    $company_id[] = $company->users_id;
                }
            }
            $users_id = UserManager::whereIn('users_id', $company_id)->get();
            if(isset($users_id)){
                foreach($users_id as $users_ids){
                    $user_idss[] = $users_ids->musers_id;
                }
            }
        }else{
            $companies = UserManager::where('musers_id', $user)->get();
            if(isset($companies)){
                foreach($companies as $company){
                    $company_id[] = $company->users_id;
                }
            }
            $users_id = UserManager::whereIn('users_id', $company_id)->get();
            if(isset($users_id)){
                foreach($users_id as $users_ids){
                    $user_idss[] = $users_ids->musers_id;
                }
            }
        }
        //Get users
        $users = User::with('companies')
            ->whereIn('id', $user_idss)
            ->where('role', 'user')
            ->orderBy('created_at', 'DESC')
            ->get();
        $user_arr = [];
        if(isset($users)){
            foreach($users as $userss){
                $user_arr[] = $userss->id;
            }
        }
        //Get timesheet users
        $data = TimeSheet::with('companies')
            ->whereBetween('hours_day', [$from_date, $to_date])
            ->whereIn('users_id', $user_arr)
            ->distinct()
            ->get(['users_id']);

        $user_time = [];
        if(isset($data)){
            foreach($data as $datas){
                $user_time[] = $datas->users_id;
            }
        }
        //Final users data
        $users_data = User::with('companies')
            ->whereIn('id', $user_time)
            ->where('role', 'user')
            ->orderBy('created_at', 'DESC')
            ->get();

        $response = [];
        if(isset($users_data)){
            foreach($users_data as $user_data){
                //Total time
                $total_time = $this->ttotal_time(
                    $user_data->id,
                    $from_date,
                    $to_date
                );
                //Approved time
                $approved_time = $this->tapproved_time(
                    $user_data->id,
                    $from_date,
                    $to_date
                );
                //Denied time
                $denied_time = $this->tdenied_time(
                    $user_data->id,
                    $from_date,
                    $to_date
                );
                $response[] = [
                    'user_id' => $user_data->id,
                    'employee_id' => $user_data->emp_id,
                    'name' => $user_data->last_name . ' ' . $user_data->first_name,
                    'supervisor' => $user_f_name,
                    'department' => $user_data->dept,
                    'companies' => strip_tags(
                        $this->user_companies($user_data->id)
                    ),
                    'time' => $user_data->last_login_at
                        ? date('h:i a', strtotime($user_data->last_login_at))
                        : null,
                    'day' => $user_data->last_login_at
                        ? date('M d, Y', strtotime($user_data->last_login_at))
                        : null,
                    'created_date' => date(
                        'M d, Y(D)',
                        strtotime($user_data->created_at)
                    ),
                    'status' => $user_data->status == 1
                        ? 'Active'
                        : 'Inactive',
                    'total_hours' => $total_time,
                    'approved_hours' => $approved_time,
                    'declined _hours' => $denied_time,
                ];
            }
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Payperiod data fetched successfully',
            //'payperiod' => [
            //'from_date' => $from_date,
            //'to_date' => $to_date
            //],
            'data' => $response
        ], 200);
    }
  
    //Function for search payperiod
    public function nsearch_payperiod(Request $request) {
        //Get date
        $from_date = str_replace('-', '_', $request->frmdate);
        $to_date   = str_replace('-', '_', $request->todate);
        $ssearch_by_comp = $request->ssearch_by_comp;
        
        //Get auth detail
        $user = Auth::id();

        $company_id = [];
        $user_idss = [];

        if (!empty($ssearch_by_comp) && $ssearch_by_comp != 0) {
            $users_id = UserManager::where('users_id', $ssearch_by_comp)->get();
            foreach ($users_id as $users_ids) {
                $user_idss[] = $users_ids->musers_id;
            }
        } else {
            $companies = UserManager::where('musers_id', $user)->get();
            foreach ($companies as $company) {
                $company_id[] = $company->users_id;
            }
            $users_id = UserManager::whereIn('users_id', $company_id)->get();
            foreach ($users_id as $users_ids) {
                $user_idss[] = $users_ids->musers_id;
            }
        }
        $users = User::whereIn('id', $user_idss)
            ->where('role', 'user')
            ->pluck('id')
            ->toArray();

        if ($from_date == $to_date) {
            $data = TimeSheet::where('hours_day', $from_date)
                ->whereIn('users_id', $users)
                ->distinct()
                ->pluck('users_id')
                ->toArray();
        } else {
            $data = TimeSheet::whereBetween('hours_day', [$from_date, $to_date])
                ->whereIn('users_id', $users)
                ->distinct()
                ->pluck('users_id')
                ->toArray();
        }

        $users_data = User::with('companies')
            ->whereIn('id', $data)
            ->where('role', 'user')
            ->orderBy('created_at', 'DESC')
            ->get();
        $response = [];
        foreach ($users_data as $user_data) {
            $total_time    = $this->ttotal_time($user_data->id, $from_date, $to_date);
            $approved_time = $this->tapproved_time($user_data->id, $from_date, $to_date);
            $denied_time   = $this->tdenied_time($user_data->id, $from_date, $to_date);
            //Response
            $response[] = [
                'user_id' => $user_data->id,
                'emp_id'  => $user_data->emp_id,
                'name' => $user_data->first_name . ' ' . $user_data->last_name,
                'company' => strip_tags($this->user_companies($user_data->id)),
                'time' => $user_data->last_login_at
                    ? date('h:i a', strtotime($user_data->last_login_at))
                    : null,
                'day' => $user_data->last_login_at
                    ? date('M d, Y', strtotime($user_data->last_login_at))
                    : null,
                'created_date' => date('M d, Y(D)', strtotime($user_data->created_at)),
                'status' => $user_data->status == 1
                    ? 'Active'
                    : 'Inactive',
                'total_hours'    => $total_time,
                'approved_hours' => $approved_time,
                'denied_hours'   => $denied_time,
            ];
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Timesheet filter data fetched successfully',
            'data' => $response
        ]);
    }

    //Function for total time
    public static function ttotal_time($id, $from_date, $to_date) {
		if($from_date == $to_date){
			$total_time = TimeSheet::with('companies')
                ->with('houses')
                ->with('users')
                ->where('hours_day', $from_date)
                ->where('users_id', '=', $id)
                ->sum('hours_wrk');
		}else{
			$total_time = TimeSheet::with('companies')
                ->whereBetween('hours_day', array($from_date, $to_date))
                ->where('users_id', '=', $id)
                ->sum('hours_wrk');
		}
		return $total_time;
    }
    
    //Function for approved time
	public static function tapproved_time($id, $from_date, $to_date) {
		if($from_date == $to_date){
			$approved_time = TimeSheet::with('companies')
                ->with('houses')
                ->with('users')
                ->where('hours_day', $from_date)
                ->where('users_id', '=', $id)
                ->where('approve', '=', 2)
                ->sum('hours_wrk');
		}else{
			$approved_time = TimeSheet::with('companies')
                ->whereBetween('hours_day', array($from_date, $to_date))
                ->where('users_id', '=', $id)
                ->where('approve', '=', 2)
                ->sum('hours_wrk');
		}
		return $approved_time;
    }
	
    //Function for denied time
	public static function tdenied_time($id, $from_date, $to_date) {
		if($from_date == $to_date){
			$denied_time = TimeSheet::with('companies')
                ->with('houses')
                ->with('users')
                ->where('hours_day', $from_date)
                ->where('users_id', '=', $id)
                ->where('approve', '=', 1)->sum('hours_wrk');
		}else{
			$denied_time = TimeSheet::with('companies')
                ->whereBetween('hours_day', array($from_date, $to_date))
                ->where('users_id', '=', $id)
                ->where('approve', '=', 1)->sum('hours_wrk');
		}
		return $denied_time;
    }

    //Function for total time
	public static function total_time($id) {
        $total_time = TimeSheet::where('users_id', '=', $id)->sum('hours_wrk');
		return $total_time;
    }
	
    //Function for approved time
	public static function approved_time($id) {
        $approved_time = TimeSheet::where('users_id', '=', $id)->where('approve', '=', 2)->sum('hours_wrk');
		return $approved_time;
    }
	
    //Function for denied time
	public static function denied_time($id) {
        $denied_time = TimeSheet::where('users_id', '=', $id)->where('approve', '=', 1)->sum('hours_wrk');
		return $denied_time;
    }

    //Function for user companies
	public static function user_companies($id) {
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
}

