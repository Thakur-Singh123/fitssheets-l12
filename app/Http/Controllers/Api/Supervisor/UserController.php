<?php

namespace App\Http\Controllers\Api\Supervisor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Supervisor\TimesheetssController;
use App\Http\Controllers\Supervisor\UserssController;
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
use Carbon\Carbon;
use App\UserSupervisorRel;
use App\UserVaccatioStatusn;
use App\UserVaccation;
use App\Payperiods;

class UserController extends Controller
{

   //Function for users
    public function index() {
        //Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //payperiods
        $payperiods_dates = payperiods();
        //Check payperiods
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
        //Getauth details
        $user = Auth::user()->id;
        $companies = UserManager::where('musers_id', '=', $user)->get();
        $company_id = [];
        $users = User::where('id', '=', $user)->first();
        $user_f_name = $users->first_name;

        if(isset($companies)){
            foreach($companies as $company){
                $company_id[] = $company->users_id;
            }
        }

        $users_id = UserManager::whereIn('users_id', $company_id)->get();
        $user_idss = [];

        if(isset($users_id)){
            foreach($users_id as $users_ids){
                $user_idss[] = $users_ids->musers_id;
            }
        }
        //Get data
        $data = User::with('companies')
            ->whereIn('id', $user_idss)
            ->where('role', 'user')
            ->orderBy('name', 'ASC')
            ->paginate(10);
        $response = [];
        foreach ($data as $datas) {
            $approved_time = UserssController::approved_time(
                $datas->id,
                $frm_date,
                $t_date
            );
            $total_time = UserssController::total_time(
                $datas->id,
                $frm_date,
                $t_date
            );
            $denied_time = UserssController::denied_time(
                $datas->id,
                $frm_date,
                $t_date
            );
            $user_companies = UserssController::user_companies($datas->id);
            $response[] = [
                'id' => $datas->id,
                'emp_id' => $datas->emp_id,
                'name' => $datas->last_name.' '.$datas->first_name,
                'supervisor' => $user_f_name,
                'department' => $datas->dept,
                'companies' => strip_tags($user_companies),
                'time' => $datas->last_login_at
                    ? date('h:i a', strtotime($datas->last_login_at))
                    : "",
                'day' => $datas->last_login_at
                    ? date('M d, Y', strtotime($datas->last_login_at))
                    : "",
                'created_at' => date(
                    'M d, Y',
                    strtotime($datas->created_at)
                ),
                'status' => $datas->status == 1
                    ? 'Active'
                    : 'Inactive',
                'total_hours' => $total_time,
                'approved_hours' => $approved_time,
                'declined_hours' => $denied_time,
            ];
        }
        //Response
        if(count($response) > 0){
            return response()->json([
                'status' => true,
                'message' => 'Users data fetch successfully.',
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ],
                'data' => $response
            ]);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong please try again',
                'data' => []
            ]);
        }
    }

    //Function for filter
    public function user_filter() {
        //Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Companies list
        $companiess = Company::orderBy('display_order', 'ASC')->get();

        //Payperiods list
        $payperiods_dates1 = Payperiods::with('companies')
            ->orderBy('created_at', 'DESC')
            ->get();
        //Current Payperiod
        $payperiods_dates = payperiods();

        if(isset($payperiods_dates)){
            $frm_date  = $payperiods_dates[0]['frm_date'];
            $t_date    = $payperiods_dates[0]['t_date'];

        }else{

            $frm_date = "";
            $t_date = "";
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Users filter data fetch successfully.',
            'frm_date' => $frm_date,
            'to_date' => $t_date,
            'companies_list' => $companiess,
            'payperiods_dates' => $payperiods_dates1

        ]);
    }

    //Function for time user
    public function time_index() {
        //Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }

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

        $user = Auth::user()->id;
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

        $companiess = Company::orderBy('display_order', 'ASC')->get();

        $data = User::with('companies')
            ->whereIn('id', $user_idss)
            ->where('role', '=', "user")
            ->orderBy('name', 'ASC')
            ->get();
            
        $response = [];
        foreach ($data as $datas) {
            $approved_time = UserssController::approved_time(
                $datas->id,
                $frm_date,
                $t_date
            );

            $total_time = UserssController::total_time(
                $datas->id,
                $frm_date,
                $t_date
            );

            $denied_time = UserssController::denied_time(
                $datas->id,
                $frm_date,
                $t_date
            );

            $user_companies = UserssController::user_companies($datas->id);

            $response[] = [
                'user_id' => $datas->id,
                'emp_id' => $datas->emp_id,
                'name' => $datas->first_name.' '.$datas->last_name,
                'company' => strip_tags($user_companies),
                'time' => $datas->last_login_at
                    ? date('h:i a', strtotime($datas->last_login_at))
                    : "",
                'day' => $datas->last_login_at
                    ? date('M d, Y', strtotime($datas->last_login_at))
                    : "",
                'created_at' => date('M d, Y', strtotime($datas->created_at)),
                'status' => $datas->status == 1 ? 'Active' : 'Inactive',
                'total_hours' => $total_time,
                'approved_hours' => $approved_time,
                'declined_hours' => $denied_time,
            ];
        }

        if(count($response) > 0){
            return response()->json([
                'status' => true,
                'message' => 'Users time data fetch successfully.',
                //'frm_date' => $frm_date,
                //'to_date' => $t_date,
                //'companies_list' => $companiess,
                // 'pagination' => [
                //     'total' => count($response)
                // ],
                'data' => $response
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong please try again',
                'data' => []
            ]);
        }
    }

    //Functino for user time approvel
    public function utimesheets(Request $request) {
        //Get auth 
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //json id
        $ids = json_decode($request->ids_value, true);
        //Check data fond or not
        if (empty($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No data found'
            ]);
        }

        //Update only selected records by ID
        $updated = TimeSheet::whereIn('id', $ids)
            ->where(function($q){
                $q->whereNull('approve')
                ->orWhere('approve','!=',2);
            })
            ->update([
                'remarks' => "",
                'approve' => 2,
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now()->toDateTimeString(),
            ]);

        if ($updated == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Selected user are already approved',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Selected user approved successfully',
        ]);
    }

    //Function for all timesheets data
    public function timesheets($id) {
        //Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Total Hours
        $total_hours = TimeSheet::where('users_id', $id)
            ->sum('hours_wrk');

        //Approved Hours
        $approved_hours = TimeSheet::where('users_id', $id)
            ->where('approve', 2)
            ->sum('hours_wrk');

        //Declined Hours
        $declined_hours = TimeSheet::where('users_id', $id)
            ->where('approve', 1)
            ->sum('hours_wrk');

        //Get timesheets
        $data = TimeSheet::with(['companies','users','houses'])
            ->where('users_id', $id)
            ->orderBy('created_at', 'ASC')
            ->get();

        if($data->isEmpty()){
            return response()->json([
                'status' => false,
                'message' => 'No record found',
                'data' => []
            ]);
        }

        //Response
        return response()->json([
            'status' => true,
            'message' => 'Timesheets fetch successfully.',
         
            'data' => $data->map(function($datas){
                return [
                    'id' => $datas->id,
                    'emp_id' => $datas->users->emp_id ?? "",
                    'name' => $datas->users->name ?? "",
                    'department' => $datas->users->dept ?? "",
                    'company' => $datas->companies->company ?? "",
                    'house' => $datas->houses->house_add ?? "",
                    'time_in' => $datas->time_in,
                    'time_out' => $datas->time_out,
                    'hours_worked' => str_replace('_', ':', $datas->hours_wrk),
                    'hours_day' => date(
                        "M d, Y(D)",
                        strtotime(str_replace('_', '/', $datas->hours_day))
                    ),
                    'hours_rate' => $datas->users->hourst_rate ?? "",
                    'remarks' => $datas->remarks,
                    'approve' => $datas->approve == 2 ? 'Yes' : 'Pending',
                    'approved_by' => !empty($datas->approved_by)
                        ? TimesheetssController::userName($datas->approved_by)
                        : "--",
                    'user_added_hours_at' => date(
                        'M d, Y h:i a',
                        strtotime($datas->created_at)
                    ),
                    'approved_at' => !empty($datas->approved_at)
                        ? date('M d, Y h:i a', strtotime($datas->approved_at))
                        : "--"
                ];
            }),
            'total_hours' => $total_hours,
            'approved_hours' => $approved_hours,
            'declined_hours' => $declined_hours,
        ]);
    }
}