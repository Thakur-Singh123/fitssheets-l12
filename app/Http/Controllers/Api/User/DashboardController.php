<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use App\TimeSheet;
use App\User;
use App\Company;
use App\House;
use Carbon\Carbon;
use Image;
use DateTime;
use DatePeriod;
use DateInterval;
use App\LoginLogouttime;
use App\UserManager;

class DashboardController extends Controller
{
    //Functioin for dashboard
    public function dashboard(Request $request) {
        //Get user
        $user = request()->user();
        //Check auth exists or not
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get auth login id
        $user_id = $user->id;
        
        $dt = Carbon::now();
        $current_date_time = $dt->toDateString();
        $date = explode('-', $current_date_time);
        $date = implode("_", $date);

        $companies = UserManager::where('musers_id', '=', $user_id)->get();
        $company_id = array();

        if(isset($companies)){
            foreach($companies as $company){
                $company_id[] = $company->users_id;
            }
        }

        if(isset($c_id)){
            $c_id = $company_id[0];
        }else{
            $c_id = 0;
        }

        $payperiods_dates = paychecks($c_id);

        if(isset($payperiods_dates)){
            $frm_date  = $payperiods_dates[0]['frm_date'];
            $t_date = $payperiods_dates[0]['t_date'];
            $xfrm_date  = $payperiods_dates[0]['xfrm_date'];
            $xt_date = $payperiods_dates[0]['xt_date'];
        }else{
            $frm_date  = "";
            $t_date = "";
            $xfrm_date  = "";
            $xt_date = "";
        }

        if(!empty($frm_dt) && !empty($to_dt)){
            $frm_date = $frm_dt;
            $t_date = $to_dt;
        }

        if($frm_date && $t_date){
            $from_date = explode('-', $frm_date);
            $from_date = implode("_", $from_date);

            $to_date = explode('-', $t_date);
            $to_date = implode("_", $to_date);
        }

        $last_payperiod = $payperiods_dates[0]['payperiod'];

        $data = TimeSheet::with(['companies', 'users', 'houses'])
                ->where('hours_day', $date)
                ->where('users_id', $user_id)
                ->orderBy('created_at', 'DESC')
                ->select('id', 'users_id', 'companies_id', 'houses_id', 'time_in', 'time_out')
                ->get();
        $last_pay = TimeSheet::with('companies')
            ->with('users')
            ->with('houses')
            ->where('users_id', '=', $user_id)
            ->whereBetween('hours_day', array($from_date, $to_date))
            ->orderBy('created_at', 'DESC')
            ->sum('hours_wrk');

        if($xfrm_date && $xt_date){
            $paydate = date('M d, Y', strtotime($xt_date . ' + 5 days'));
        }

        $user = User::where('id', '=', $user_id)->first();
        $hourley_rate = $user->hourst_rate;
        $total_pay = $last_pay * $hourley_rate;
        $pay_work = "";

        //Response
        $status = $data->isEmpty() ? false : true;

        $message = $data->isEmpty() 
            ? 'Timesheet not found today' 
            : 'Dashboard data fetched successfully';

        return response()->json([
            'status' => $status,
            'message' => $message,

            'data' => [
                'current_date_time' => $current_date_time,
                'hourley_rate' => $hourley_rate,
                'total_pay' => $total_pay,
                'last_payperiod' => $last_payperiod,
                //'last_pay' => $last_pay,
                //'paydate' => $paydate ?? null,
                //'pay_work' => $pay_work,
                'data' => $data
            ]
        ]);
    }
}