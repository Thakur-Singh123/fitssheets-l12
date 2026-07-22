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

class ReportController extends Controller
{
    //Function for all users list
    public function all_users() {
        //Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ],401);
        }
        $users_arrr = [];
        $company_id = [];
        $user_id = Auth::user()->id;

        $companies = UserManager::where('musers_id',$user_id)->get();
        if(isset($companies)){
            foreach($companies as $company){
                $company_id[] = $company->users_id;
            }
        }

        $users_id = UserManager::whereIn('users_id',$company_id)->get();
        if(isset($users_id)){
            foreach($users_id as $users_ids){
                $users_arrr[] = $users_ids->musers_id;
            }
        }
        //Get users
        $users = User::where('role',"user")
            ->whereIn('id',$users_arrr)
            ->orderBy('created_at','DESC') 
            ->select('id', 'emp_id', 'drivers_license', 'email', 'name', 'first_name', 'last_name', 'dept', 'companies_id', 'hourst_rate')->get();

        //Check if data found or not
        if($users->isEmpty()){
            return response()->json([
                'status' => false,
                'message' => 'No users found',
                'data' => []
            ]);
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Users report fetched successfully',
            'data' => $users
        ]);
    }
    
    //Function for all user export report
    public function all_users_report() {
        //Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ],401);
        }
        //Var
        $users_arrr = [];
        $company_id = [];
        $user_id = Auth::id();
        $companies = UserManager::where('musers_id',$user_id)->get();

        foreach($companies as $company){
            $company_id[] = $company->users_id;
        }
        $users_id = UserManager::whereIn('users_id',$company_id)->get();
        foreach($users_id as $users_ids){
            $users_arrr[] = $users_ids->musers_id;
        }
        //Get users
        $user = User::where('role','user')
            ->whereIn('id',$users_arrr)
            ->orderBy('created_at','DESC')
            ->get();

        $users_report = [];
        $users_report[] = [
            'Sr.No',
            'Emp ID',
            'Email',
            'Name',
            'First Name',
            'Last Name',
            'Department',
            'Company',
            'Hourly Rate($)',
            'Drivers License'
        ];

        $count = 1;
        foreach($user as $userss){
            $user_companies = '';
            if(method_exists($this,'export_user_companies')){
                $user_companies = $this->export_user_companies($userss->id);
            }
            $users_report[] = [
                $count,
                $userss->emp_id,
                $userss->email,
                $userss->name,
                $userss->first_name,
                $userss->last_name,
                $userss->dept,
                $user_companies,
                $userss->hourst_rate,
                $userss->drivers_license,
            ];
            $count++;
        }
        return Excel::create('All Users', function($excel) use ($users_report){
            $excel->sheet('All Users', function($sheet) use ($users_report){
                $sheet->rows($users_report);
            });
        })->download('xlsx');
    }

    //Function for sign signout users list
    public function sign_signout_users() {
        //Get users
        $user = Auth::user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //var
        $users_arrr = [];
        $company_id = [];

        //Companies
        $companies = UserManager::where('musers_id', $user->id)->get();
        foreach ($companies as $company) {
            $company_id[] = $company->users_id;
        }

        $users_id = UserManager::whereIn('users_id', $company_id)->get();
        foreach ($users_id as $users_ids) {
            $users_arrr[] = $users_ids->musers_id;
        }
        //Get login times
        $LoginLogouttime = LoginLogouttime::whereIn('users_id', function($query) use ($users_arrr) {
            $query->select('id')
            ->from('users')
            ->whereIn('id', $users_arrr)
            ->where('role', 'user');
        })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $responseData = [];

        foreach ($LoginLogouttime as $datas) {
            $userData = User::where('id', $datas->users_id)->first();
            if ($userData) {
                if ($datas->last_logout_at != null) {
                    $status = "Log Out";
                    $datetime = $datas->last_logout_at;
                } else {
                    $status = "Log In";
                    $datetime = $datas->last_login_at;
                }
                //Response
                $responseData[] = [
                    'emp_id' => $userData->emp_id,
                    'name' => $userData->name,
                    'type' => $userData->role,
                    'status' => $status,
                    'date' => date('M d, Y', strtotime($datetime)),
                    'time' => date('h:i a', strtotime($datetime))
                ];
            }
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Users sign signout report fetched successfully',
            'current_page' => $LoginLogouttime->currentPage(),
            'last_page' => $LoginLogouttime->lastPage(),
            'total' => $LoginLogouttime->total(),
            'data' => $responseData
        ], 200);
    }

    //Function for sign signout export report
    public function sign_signout_users_report() {
        //Auth check
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //var
        $users_arrr = [];
        $company_id = [];
        $user_id = $auth->id;
        //Get companies
        $companies = UserManager::where('musers_id', $user_id)->get();
        foreach ($companies as $company) {
            $company_id[] = $company->users_id;
        }
        //Get users
        $users_id = UserManager::whereIn('users_id', $company_id)->get();

        foreach ($users_id as $users_ids) {
            $users_arrr[] = $users_ids->musers_id;
        }
        //Login logout records
        $LoginLogouttime = LoginLogouttime::whereIn(
            'users_id',
            $users_arrr
        )->orderBy('created_at', 'DESC')->get();

        $users_report = [];
        //Header
        $users_report[] = ['Sr.No','Emp ID','Name','Type','Status','Date','Time'];
        $count = 1;
        foreach ($LoginLogouttime as $LoginLogouttimes) {
            $user = User::find($LoginLogouttimes->users_id);
            if (!$user || $user->role != 'user') {
                continue;
            }

            $status = '';
            $date = '';
            $time = '';

            if ($LoginLogouttimes->last_logout_at != null) {
                $status = "Log Out";
                $date = date(
                    'M d, Y',
                    strtotime($LoginLogouttimes->last_logout_at)
                );
                $time = date(
                    'h:i a',
                    strtotime($LoginLogouttimes->last_logout_at)
                );
            } elseif ($LoginLogouttimes->last_login_at != null) {
                $status = "Log In";
                $date = date(
                    'M d, Y',
                    strtotime($LoginLogouttimes->last_login_at)
                );
                $time = date(
                    'h:i a',
                    strtotime($LoginLogouttimes->last_login_at)
                );
            }
            $users_report[] = [
                $count,
                $user->emp_id,
                $user->name,
                $user->role,
                $status,
                $date,
                $time
            ];
            $count++;
        }
        return Excel::create(
            'All_Users_LogIn_LogOut',
            function($excel) use ($users_report){
                $excel->sheet(
                    'Report',
                    function($sheet) use ($users_report){
                        $sheet->rows($users_report);
                    }
                );
            }
        )->download('xlsx');
    }

    //Function for payroll filter
    public function payroll_filter() {
        //Get companies
        $companies = Company::orderBy('display_order', 'ASC')->select('id','company')->get();
        //Get payperiods dates
        $payperiods_dates = Payperiods::orderBy('created_at', 'DESC')->select('payperiod')->get();
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Payroll filter fetched successfully',
            'data' => [
                'companies' => $companies,
                'payperiods' => $payperiods_dates
            ]
        ], 200);
    }

    //Function for payroll report
    public function payroll_report(Request $request) {
        $users_arrr = [];
        //Company filter
        if($request->company_id && $request->company_id != 0){
            $user_companies = UserManager::where(
                'users_id',
                $request->company_id
            )->get();
            foreach($user_companies as $user_company){
                $users_arrr[] = $user_company->musers_id;
            }
        }
        //Payperiod filter
        $bet_dates = explode('-',$request->payperiod);

        $from_date = $bet_dates[0];
        $to_date   = $bet_dates[1];

        $xto_date = implode('-',explode('_',$to_date));
        $xfrom_date = implode('-',explode('_',$from_date));
        $users_arrr = array_unique($users_arrr);

        //Get timesheets
        $data = TimeSheet::with('companies','users')
                    ->whereIn('users_id',$users_arrr)
                    ->whereBetween(
                        'hours_day',
                        [$from_date,$to_date]
                    )
                    ->distinct('users_id')
                    ->get();

        $response = [];
        if($data->count() > 0){
            $user_pay = User::with('companies')
                ->whereIn('id',$users_arrr)
                ->where('role','user')
                ->orderBy('name','ASC')
                ->get();

            foreach($user_pay as $user){
                $total_time = $this->ttotal_time(
                    $user->id,
                    $from_date,
                    $to_date
                );

                if($total_time > 0){
                    $response[] = [
                        'date' => date(
                            'm/d/y',
                            strtotime($xto_date)
                        ),
                        'emp_id' => $user->emp_id,
                        'last_name' => $user->last_name,
                        'first_name' => $user->first_name,
                        'payroll_code' => '01',
                        'hours' => $total_time,
                        'hour_rate' => $user->hourst_rate
                    ];
                }
            }
        }
        //Response
        if(empty($response)){
            return response()->json([
                'status' => false,
                'message' => 'No records found',
                'data' => []
            ],404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Payroll data fetched successfully',
            'data' => $response
        ],200);
    }
    
    //Function for search payperiod
    public function search_by_payperiod() {
        // Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ],401);
        }
        //payperiods dates
        $payperiods_dates = Payperiods::orderBy('created_at','DESC')->select('payperiod')->get();
        //Response
        if($payperiods_dates->isEmpty()){
            return response()->json([
                'status' => false,
                'message' => 'No pay periods found',
                'data' => []
            ]);
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Pay periods fetched successfully',
            'data' => $payperiods_dates
        ]);
    }

    //Function for payperiod report
    public function search_by_payperiod_report(Request $request) {
        //Var
        $users_arrr = [];
        $company_id = [];
        //Auth User
        $user_id = $request->user()->id;
        //Get company ids
        $companies = UserManager::where(
            'musers_id',
            $user_id
        )->get();

        foreach($companies as $company){
            $company_id[] = $company->users_id;
        }
        //Get users ids
        $users = UserManager::whereIn(
            'users_id',
            $company_id
        )->get();

        foreach($users as $user){
            $users_arrr[] = $user->musers_id;
        }
        //Check payperiod
        if(empty($request->payperiod)){

            return response()->json([
                'status'=>false,
                'message'=>'Payperiod required'
            ],400);
        }
        //Example:
        //2026_06_08-2026_06_21
        $bet_dates = explode('-', $request->payperiod);
        if(count($bet_dates)<2){

            return response()->json([
                'status'=>false,
                'message'=>'Invalid payperiod'
            ],400);
        }

        $from_date = trim($bet_dates[0]);
        $to_date = trim($bet_dates[1]);

        // Get timesheet data
        $data = TimeSheet::with([
                    'companies',
                    'users',
                    'houses'
                ])
                ->whereIn(
                    'users_id',
                    $users_arrr
                )
                ->whereBetween(
                    'hours_day',
                    [$from_date,$to_date]
                )
                ->orderBy(
                    'created_at',
                    'DESC'
                )
                ->get();

        if($data->count()==0){

            return response()->json([
                'status'=>false,
                'message'=>'No records found',
                'data'=>[]
            ],404);
        }

        $response = [];

        foreach($data as $key=>$datas){

            // Vacation
            $vacation="";

            if($datas->vacation_status=="0"){
                $vacation="No";
            }
            elseif($datas->vacation_status=="1"){
                $vacation="Yes";
            }

            // Approved
            $approve="Pending";

            if($datas->approve=="2"){
                $approve="Yes";
            }
            elseif($datas->approve=="1"){
                $approve="No";
            }

            // Display date
            $show_date = date(
                'M d,Y',
                strtotime(
                    str_replace(
                        '_',
                        '-',
                        $datas->hours_day
                    )
                )
            );
            $response[]=[
                'id'=>$key+1,
                'emp_id'=>$datas->users->emp_id ?? '',
                'email'=>$datas->users->email ?? '',
                'last_name'=>$datas->users->last_name ?? '',
                'first_name'=>$datas->users->first_name ?? '',
                'name'=>$datas->users->name ?? '',
                'department'=>$datas->users->dept ?? '',
                'company'=>$datas->companies->company ?? '',
                'house'=>$datas->houses->house_add ?? '',
                'time_in'=>$datas->time_in,
                'time_out'=>$datas->time_out,
                'hours_worked'=>$datas->hours_wrk,
                'day'=>$show_date,
                'hours_rate'=>$datas->users->hourst_rate ?? '',
                'vacation'=>$vacation,
                'approved'=>$approve
            ];
        }

        return response()->json([
            'status'=>true,
            'message'=>'Timesheet fetched successfully',
            'pay_period'=>[
                'from_date'=>$from_date,
                'to_date'=>$to_date
            ],
            'data'=>$response

        ],200);
    }













public function post_data(Request $request)
{
    $bet_dates = explode('-', $request->payperiod);

    if(count($bet_dates) < 2){
        return response()->json([
            'status'=>false,
            'message'=>'Invalid payperiod format'
        ]);
    }

    $from_date = $bet_dates[0];
    $to_date = $bet_dates[1];

    $xpto_date = date(
        'M d, Y',
        strtotime(str_replace('_','-',$to_date).' +5 days')
    );

    $users_arrr = UserManager::whereIn(
        'users_id',
        UserManager::where('musers_id',Auth::id())
        ->pluck('users_id')
    )->pluck('musers_id');

    $data = TimeSheet::with('companies','users','houses')
        ->whereIn('users_id',$users_arrr)
        ->whereBetween('hours_day',[$from_date,$to_date])
        ->orderBy('created_at','DESC')
        ->get();

    if($data->isEmpty()){
        return response()->json([
            'status'=>false,
            'message'=>'No Timesheet found'
        ]);
    }

    $time_sheet[] = [
        'Sr.No',
        'Emp ID',
        'Pay Period',
        'Pay Date',
        'Email',
        'Name',
        'Company',
        'Hours Worked',
        'Day',
        'Approved'
    ];

    $count = 1;

    foreach($data as $row){

        $time_sheet[] = [

            $count,
            $row->users->emp_id ?? '',
            date('M d,Y',strtotime(str_replace('_','-',$from_date)))
            .' To '.
            date('M d,Y',strtotime(str_replace('_','-',$to_date))),
            $xpto_date,
            $row->users->email ?? '',
            $row->users->name ?? '',
            $row->companies->company ?? '',
            $row->hours_wrk,
            date(
                'M d,Y',
                strtotime(str_replace('_','-',$row->hours_day))
            ),
            $row->approve == 2 ? 'Yes' : 'Pending'
        ];

        $count++;
    }

    return Excel::create(
        'Time Sheet By Payperiod',
        function($excel) use($time_sheet){

            $excel->sheet('Time Sheet', function($sheet) use($time_sheet){

                $sheet->fromArray(
                    $time_sheet,
                    null,
                    'A1',
                    false,
                    false
                );

            });

        }

    )->download('xlsx');
}

    //Function for export companies
    public static function export_user_companies($id) {
		$user_companies = UserManager::where('musers_id', '=', $id)->get();
		$com_out = "";
		if(isset($user_companies)){
			foreach($user_companies as $user_company){
				$company = Company::where('id', '=', $user_company->users_id)->first();				
				$com_out .= $company->company;
			}
		}
		return $com_out;
	}


    public static function ttotal_time($id, $from_date, $to_date)
    {
        // $total_time = TimeSheet::where('users_id', '=', $id)->sum('hours_wrk');
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
}
