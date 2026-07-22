<?php

namespace App\Http\Controllers\Api\Supervisor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use App\TimeSheet;
use App\User;
use App\Company;
use App\House;
use Excel;
use Carbon\Carbon;
use App\UserManager;
use App\LoginLogouttime;
use App\Payperiods;
use App\UserVaccatioStatusn;
use DateTime;
use App\UserVaccation;

class DashboarController extends Controller
{
    //Function for dashboard
    public function dashboard() {
        //Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }

        //Get login id
        $user = $auth->id;
        //Get companies
        $companies = UserManager::where('musers_id', '=', $user)->get();
        $company_id = array();

        if (isset($companies)) {
            foreach ($companies as $company) {
                $companies = Company::where('id', '=', $company->users_id)->first();
                $company_id[] = $companies->company;
            }
        }
        //Get users
        $users = User::with('companies')
            ->whereIn('companies_id', $company_id)
            ->where('role', '=', "user")
            ->orderBy('created_at', 'DESC')
            ->get();

        $user_arr = array();

        $user_count = 1;

        if (isset($users)) {

            foreach ($users as $userss) {

                $user_arr[] = $userss->id;

                $user_count++;
            }
        }
        //Current date
        $dt = Carbon::now();
        $current_date_time = $dt->toDateString();
        $date = explode('-', $current_date_time);
        $date = implode("_", $date);

        //Get timesheet
        $data = TimeSheet::with('companies')
            ->with('users')
            ->with('houses')
            ->where('hours_day', '=', $date)
            ->whereIn('users_id', $user_arr)
            ->orderBy('created_at', 'DESC')->select('id', 'users_id', 'companies_id', 'houses_id', 'time_in', 'time_out')
            ->get();

        //Response
        return response()->json([
            'status' => true,
            'message' => 'Dashboard data fetched successfully',
            'current_date_time' => $current_date_time,
            'user_count' => $user_count,
            'data' => $data
        ]);
    }
}



 