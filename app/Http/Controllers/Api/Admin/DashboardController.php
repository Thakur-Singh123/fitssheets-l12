<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\TimeSheet;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\House;
use App\Models\UserManager;
use Excel;
use App\Models\AdminMeta;
use Carbon\Carbon;
use App\Models\LoginLogouttime;
use DateTime;
use App\Models\Payperiods;
use Twilio\Rest\Client;
use App\Models\UserVaccatioStatusn;
use App\Models\UserVaccation;
use App\Models\SmsLog;

class DashboardController extends Controller
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
        //Admin count
        $admins = User::where('role', 'admin')->count();
        //Manager count
        $managers = User::where('role', 'manager')->count();
        //Supervisor count
        $supervisors = User::where('role', 'supervisor')->count();
        //User count
        $users = User::where('role', 'user')->count();
        //Approved timesheets count
        $dataapps = TimeSheet::where('approve', 2)->count();
        //Not approved timesheets count
        $datanapps = TimeSheet::where('approve', 1)->count();
        //Current date
        $dt = Carbon::now();
        $current_date_time = $dt->toDateString();

        $date = explode('-', $current_date_time);
        $date = implode("_", $date);

        //Today's timesheet data
        $data = TimeSheet::with('companies', 'users', 'houses')
            ->where('hours_day', $date)
            ->orderBy('hours_day', 'DESC')->select('id', 'users_id', 'companies_id', 'houses_id', 'time_in', 'time_out')
            ->get();
            //Response
            return response()->json([
                'status' => true,
                'message' => 'Dashboard data fetched successfully',
                'data' => [
                    'admins' => $admins,
                    //'managers' => $managers,
                    'supervisors' => $supervisors,
                    'users' => $users,
                    //'approved_timesheets' => $dataapps,
                    //'not_approved_timesheets' => $datanapps,
                    'current_date_time' => $current_date_time,
                    'timesheets' => $data,
                ]
            ], 200);
    }

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

    //Function for department
    public function department() {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get department
        $department = Department::orderBy('department', 'ASC')
            ->get(['id', 'department']);

        if ($department->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found',
                'data' => []
            ], 404);
        } 
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Department list fetched successfully',
            'data' => [
                'department' => $department
            ]
        ]);
    }
}
