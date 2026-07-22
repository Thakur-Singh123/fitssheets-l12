<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\UserInfoController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Validator;
use App\TimeSheet;
use Excel;
use App\User;
use App\AdminMeta;
use App\Company;
use App\Holiday;
use App\Department;
use App\UserManager;
use DateTime;
use App\Payperiods;
use App\UserSupervisorRel;
use App\UserCasemanagerRel;
use App\UserVaccatioStatusn;
use App\UserVaccation;


class UserController extends Controller
{
    //Function for user filter
    public function users_filter() {
        //Get check
        $auth_user = request()->user();
        //Response
        if (!$auth_user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get payperiod dates
        $payperiods_dates = payperiods();
        //Check if payperiods exists or not
        if(isset($payperiods_dates) && count($payperiods_dates) > 0) {
            $frm_date = $payperiods_dates[0]['frm_date'];
            $t_date   = $payperiods_dates[0]['t_date'];
        } else {
            $frm_date = "";
            $t_date   = "";
        }
        //Get payperiods data
        $payperiods = Payperiods::with('companies')->orderBy('created_at', 'DESC')->get();
        //Get companies
        $companies = Company::orderBy('company', 'ASC')->get();
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Filter data fetched successfully',
            'frm_date' => $frm_date,
            't_date'   => $t_date,
            'companies' => $companies,
            'payperiods' => $payperiods,
        ]);
    }
    
    //Function for user_search
    public function user_search(Request $request) {
        //Search
        $searchTerm = $request->srch_user;
        //Get users
        $data = User::where('role', 'user')
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('emp_id', $searchTerm);
            })
            ->orderBy('name', 'ASC')
            ->get();

        if ($data->count() == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry No Data!!',
                'data' => []

            ]);
        }

        $response = [];
        $count = 1;

        foreach ($data as $datas) {

            $approved_by = $this->approved_by($datas->id);

            $name = strtoupper($approved_by);
            $words = explode(" ", $name);

            $firstName = reset($words);
            $last_name = !empty($words[1]) ? $words[1] : '';

            $approved_by = substr($firstName, 0, 1) . ' ' . $last_name;

            $color_info = $this->color_info($datas->id);

            $total_hours = $this->total_time($datas->id);
            $approved_hours = $this->approved_time($datas->id);
            $denied_hours = $this->denied_time($datas->id);

            $user_companies = strip_tags($this->user_companies($datas->id));

        $response[] = [
                'sr_no' => $count,
                'name' => trim($datas->first_name . ' ' . $datas->last_name),
                'emp_id' => $datas->emp_id,
                'phone_no' => $datas->phone_no,
                'status' => $datas->status == 1 ? 'Active' : 'Inactive',
                'hourst_rate' => $datas->hourst_rate,
                'time' => $datas->last_login_at ? date('h:i a', strtotime($datas->last_login_at)) : null,
                'day' => $datas->last_login_at  ? date('M d, Y', strtotime($datas->last_login_at)) : null,
                'created' => date('M d, Y', strtotime($datas->created_at)),
                'total_hours' => $total_hours,
                'approved_hours' => $approved_hours,
                'declined_hours' => $denied_hours,
                'approved_by' => UserInfoController::approved_by($datas->id),
                'driving_license' => $datas->drivers_license ? url('/public/assets/uploads/driving-license/' . $datas->drivers_license) : null,
                'covid_report' => $datas->covid_report ? url('/public/assets/uploads/covid-report/' . $datas->covid_report) : null,
                'email' => $datas->email,
                'user_password' => $datas->pass,
                'companies' => strip_tags(UserInfoController::user_companies($datas->id)),
                    'department' => $datas->dept,



            
            ];

            $count++;
        }

        return response()->json([
            'status' => true,
            'message' => 'Users fetched successfully',
            'total_records' => count($response),
            'data' => $response
        ]);
    }

    //Function for index
    public function index(Request $request) {
        //Get check
        $auth_user = request()->user();
        //Response
        if (!$auth_user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get payperiods
        $payperiods_dates = payperiods();
        //Check if payperiod date exists or not
        if (isset($payperiods_dates)) {
            $frm_date = $payperiods_dates[0]['frm_date'];
            $t_date   = $payperiods_dates[0]['t_date'];
        } else {
            $frm_date = "";
            $t_date   = "";
        }
        //Get users
        $users = User::with('companies')->where('role', 'user')->orderBy('name', 'ASC')->paginate(10);
        //Get payperiods data
        $payperiods = Payperiods::with('companies')->orderBy('created_at', 'DESC')->get();
        //Get companies
        $companies = Company::orderBy('company', 'ASC')->get();
        //Var
        $result = [];
        //User
        foreach ($users as $user) {
            //total Hours
            if ($frm_date && $t_date) {
                $frm = str_replace('-', '_', $frm_date);
                $to  = str_replace('-', '_', $t_date);
                $total_hours    = UserInfoController::ttotal_time($user->id, $frm, $to);
                $approved_hours = UserInfoController::tapproved_time($user->id, $frm, $to);
                $declined_hours = UserInfoController::tdenied_time($user->id, $frm, $to);
            } else {
                $total_hours    = UserInfoController::total_time($user->id);
                $approved_hours = UserInfoController::approved_time($user->id);
                $declined_hours = UserInfoController::denied_time($user->id);
            }
            $result[] = [
                'id' => $user->id,
                'name' => trim($user->last_name . ' ' . $user->first_name),
                'emp_id' => $user->emp_id,
                'phone_no' => $user->phone_no,
                'status' => $user->status == 1 ? 'Active' : 'Inactive',
                'hourst_rate' => $user->hourst_rate,
                'time' => $user->last_login_at ? date('h:i a', strtotime($user->last_login_at)) : null,
                'day' => $user->last_login_at  ? date('M d, Y', strtotime($user->last_login_at)) : null,
                'created' => date('M d, Y', strtotime($user->created_at)),
                'total_hours' => $total_hours,
                'approved_hours' => $approved_hours,
                'declined_hours' => $declined_hours,
                'approved_by' => UserInfoController::approved_by($user->id),
                'driving_license' => $user->drivers_license ? url('/public/assets/uploads/driving-license/' . $user->drivers_license) : null,
                'covid_report' => $user->covid_report ? url('/public/assets/uploads/covid-report/' . $user->covid_report) : null,
                'email' => $user->email,
                'user_password' => $user->pass,
                'companies' => strip_tags(UserInfoController::user_companies($user->id)),
                'department' => $user->dept,
            ];
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Users data fetched successfully',
            'frm_date' => $frm_date,
            'to_date' => $t_date,
            'companies' => $companies,
            'payperiods' => $payperiods,
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'per_page' => $users->perPage(),
            'data' => $result
        ]); 
    }

    //Function for store user
    public function store(Request $request) {
        //Get check
        $auth_user = request()->user();
        //Response
        if (!$auth_user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role' => 'required',
            'driving_license' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',   
        ]);
        //Validation
        if ($validator->fails()) {
            //Response
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        //Check if driving license exists or not
        if ($request->hasFile('driving_license')) {
            $image = $request->file('driving_license');
            $iname = 'emp_driving_license' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/assets/uploads/driving-license/');
            $image->move($destinationPath, $iname);
        } else {
            $iname = "";
        }
        $name = $request->first_name . " " . $request->last_name;
        //Get last insert if
        $last_id = DB::select(
            'SELECT id FROM users WHERE role="user" ORDER BY id DESC LIMIT 1'
        );

        $last_idd = $last_id[0]->id;
        $last_idd = $last_idd + 1;
        $last_idd = str_pad($last_idd,3,"0", STR_PAD_LEFT);
        $emp_id = "FITSS-" . $last_idd;
        
        //Get companies
        if (isset($request->companies_id)) {
            $companies = Company::where('id', '=', $request->companies_id[0] )->orderBy('created_at', 'DESC')->get();
            $company_nm = $companies[0]->company;
        } else {
            $company_nm = "";
        }
        $date = explode( '-', $request->hours_day);
        $date = implode("_", $date);
        //Get daa
        $form_data = [
            'name' => $name,
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'emp_id' => $emp_id,
            'dob' => $date,
            'phone_no' => $request->phone_no,
            'ssn_no' => $request->ssn_no,
            'child_sup' => $request->child_sup,
            'health_insurance' => $request->health_insurance,
            'email' => $request->email,
            'role' => $request->role,
            'dept' => $request->dept,
            'status' => $request->status, 
            'companies_id' => $company_nm,
            'hourst_rate' => $request->hourst_rate,
            'password' => Hash::make($request->password),
            'pass' => $request->password,
            'drivers_license' => $iname,
        ];
        //User create
        $user_store = User::create($form_data);
        //Get manager id
        $manager_id = $user_store->id;
        if (isset($request->companys_id)) {
            foreach ($request->companys_id as $company) {
                //Create
                UserManager::create([
                    'musers_id' => $manager_id,
                    'users_id' => $company
                ]);
            }
        }
        //Check if data store or not
        if($user_store) {
            //Check if user exists or not
            if($request->role == "user") {
                //Response
                return response()->json([
                    'status' => true,
                    'message' => 'User Created Successfully!!',
                    'data' => $user_store
                ], 200);
            //Check if manager exists or not
            } elseif($request->role == "manager") {
                //Response
                return response()->json([
                    'status' => true,
                    'message' => 'Manager Created Successfully!!',
                    'data' => $user_store
                ], 200);
            //Check if supervisor exists or not
            } elseif($request->role == "supervisor") {
                //Response
                return response()->json([
                    'status' => true,
                    'message' => 'Supervisor Created Successfully!!',
                    'data' => $user_store
                ], 200);
            }
        } else {
            //Response
            return response()->json([
                'status' => false,
                'message' => 'Error while creating User!!'
            ], 500);
        }
    }
    
    //Function for update
    public function update(Request $request, $id) {
        //Get check
        $auth_user = request()->user();
        //Response
        if (!$auth_user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Validate input fields
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
            'dept' => 'required',   
        ]);
        //Validation
        if ($validator->fails()) {
            //Response
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        //Get user
        $user = User::whereId($id)->first();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
        //Check if driving license exists or not
        if ($request->hasFile('driving_license')) {
            if ($user->drivers_license != '') {
                $old_file = public_path('/assets/uploads/driving-license/' . $user->drivers_license);
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            $image = $request->file('driving_license');
            $iname = 'emp_driving_license' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/assets/uploads/driving-license/');
            $image->move($destinationPath, $iname);
        } else {
            $iname = $user->drivers_license;
        }
        $name = $request->first_name . " " . $request->last_name;
        if (isset($request->companies_id)) {
            $companies = Company::where('id', '=', $request->companies_id[0])
                ->orderBy('created_at', 'DESC')
                ->get();
            $company_nm = isset($companies[0]) ? $companies[0]->company : '';
        } else {
            $company_nm = "";
        }

        $date = explode('-', $request->hours_day);  
        $date = implode("_", $date);
        //data
        $form_data = [
            'name'             => $name,
            'username'         => $request->username,
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'emp_id'           => $request->emp_id,
            'dob'              => $date,
            'phone_no'         => $request->phone_no,
            'ssn_no'           => $request->ssn_no,
            'child_sup'        => $request->child_sup,
            'health_insurance' => $request->health_insurance,
            'drivers_license'  => $iname,
            'email'            => $request->email,
            'role'             => $request->role,
            'dept'             => $request->dept,
            'status'           => $request->status,
            'hourst_rate'      => $request->hourst_rate,
            'companies_id'     => $company_nm
        ];
        //User update
        $user_update = User::whereId($id)->update($form_data);

        if (isset($request->companys_id)) {
            UserManager::where("musers_id", "=", $id)->delete();
            foreach ($request->companys_id as $company) {
                $user_comp = [
                    'musers_id' => $id,
                    'users_id'  => $company
                ];

                UserManager::create($user_comp);
            }
        }

        if (isset($request->users_id)) {
            foreach ($request->users_id as $users) {
                $users_arrr[] = $users;
            }
        }

        if (isset($users_arrr)) {
            $users_arrr = array_unique($users_arrr);
            UserSupervisorRel::where("supervisor_id", "=", $id)->delete();
            foreach ($users_arrr as $users_arr) {
                $form_data = [
                    'users_id'      => $users_arr,
                    'supervisor_id' => $id,
                ];
                UserSupervisorRel::create($form_data);
            }
        }

        if (isset($request->cmusers_id)) {
            foreach ($request->cmusers_id as $users) {
                $cmusers_arrr[] = $users;
            }
        }

        if (isset($cmusers_arrr)) {
            $cmusers_arrr = array_unique($cmusers_arrr);
            UserCasemanagerRel::where("casemanager_id", "=", $id)->delete();
            foreach ($cmusers_arrr as $users_arr) {
                $form_data = [
                    'casemanager_id' => $id,
                    'users_id'       => $users_arr,
                ];
                UserCasemanagerRel::create($form_data);
            }
        }
        //Check if user updated or not
        if ($user_update) {
            if ($request->role == "user") {
                $message = "User Updated Successfully!!";
            } elseif ($request->role == "casemanager") {
                $message = "Manager Updated Successfully!!";
            } elseif ($request->role == "supervisor") {
                $message = "Supervisor Updated Successfully!!";
            } else {
                $message = "User Updated Successfully!!";
            }
            //Response
            return response()->json([
                'status' => true,
                'message' => $message,
                //'user_id' => $id,
                //'driving_license' => $user->drivers_license,
                //'driving_license_url' => url('public/assets/uploads/driving-license/' . $iname)
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Error while updating User!!'
            ], 500);
        }
    }

    //Function for update password
    public function update_password(Request $request, $id) {
        //Get check
        $auth_user = request()->user();
        //Response
        if (!$auth_user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Validate input fields
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
                'new_password' => 'min:6|required_with:confirm_password|same:new_confirm_password',
                'new_confirm_password' => 'required',  
            ]);
            //Validation
            if ($validator->fails()) {
                //Response
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
        //update user password
        $user = User::where('id', '=', $id)->first();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
        //Check if pass
        if(!Hash::check($request['current_password'], $user->password)) {
            //Response
            return response()->json([
                'status' => false,
                'message' => 'Current password does not match'
            ], 400);
        } else {
            $update_pass = DB::table('users')
            ->where('id', $user->id)
            ->where('role', $user->role)
            ->update([
                'password' => bcrypt($request['new_password']),
                'pass' => $request['new_password'],
            ]);
            //Check if update or not
            if($update_pass){
                return response()->json([
                    'status' => true,
                    'message' => 'Your Password is updated successfully!'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Oops something went wrong'
                ], 500);
            }
        }
    }

    //Function for delete user
    public function destroy($id) {
        //Get auth
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get user
        $data = User::find($id);
        //Check if user fond or not
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
        //delete record
        $data->delete();
        //Response
        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }

    //Function for user search payperiod
    public function usearch_payperiod(Request $request) {
        //Get auth
        $auth_user = request()->user();
        //Response
        if (!$auth_user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        if (empty($request->from_date) && empty($request->to_date) && empty($request->search_by_payu)) {
            return response()->json([
                'status' => false,
                'message' => 'Please select payperiod or date range'
            ], 422);
        }

        if (!empty($request->from_date) && !empty($request->to_date)) {
            $from_dates = str_replace('-', '_', $request->from_date);
            $to_dates   = str_replace('-', '_', $request->to_date);
            $from_date = $request->from_date;
            $to_date   = $request->to_date;
        } else {
            $bet_dates = explode('-', $request->search_by_payu);
            $from_dates = $bet_dates[0] ?? '';
            $to_dates   = $bet_dates[1] ?? '';

            $xfrom_date = explode('_', $from_dates);
            $from_date  = implode('-', $xfrom_date);

            $xto_date = explode('_', $to_dates);
            $to_date  = implode('-', $xto_date);
        }

        $search_by_comp = $request->search_by_compp;
        $users_arrr = [];

        if (isset($search_by_comp) && $search_by_comp != 0) {
            $user_companies = UserManager::where(
                'users_id',
                '=',
                $search_by_comp
            )->get();
            if ($user_companies) {
                foreach ($user_companies as $user_company) {
                    $users_arrr[] = $user_company->musers_id;
                }
            }
        }
        
        //Get users
        $users = User::with('companies')
            ->whereIn('id', $users_arrr)
            ->where('role', 'user')
            ->orderBy('name', 'ASC')
            ->get();

        $user_arr = [];

        foreach ($users as $userss) {
            $user_arr[] = $userss->id;
        }

        if ($from_date == $to_date) {
            $data = TimeSheet::with('companies')
                ->with('houses')
                ->with('users')
                ->where('hours_day', $from_dates)
                ->whereIn('users_id', $user_arr)
                ->distinct('users_id')
                ->get(['users_id']);

        } else {
            $data = TimeSheet::with('companies')
                ->whereBetween('hours_day', [$from_dates, $to_dates])
                ->whereIn('users_id', $user_arr)
                ->distinct('users_id')
                ->get(['users_id']);
        }

        $user_time = [];
        foreach ($data as $datas) {
            $user_time[] = $datas->users_id;
        }

        $users_data = User::with('companies')
            ->whereIn('id', $user_time)
            ->where('role', 'user')
            ->orderBy('name', 'ASC')
            ->get();

        $response = [];

        foreach ($users_data as $user_data) {
            $approved_by = $this->tapproved_by(
                $user_data->id,
                $from_dates,
                $to_dates
            );

            $color_info = $this->color_info($user_data->id);
            $response[] = [
                'id' => $user_data->id,
                'name' => $user_data->name,
                'emp_id' => $user_data->emp_id,
                'status' => $user_data->status == 1 ? 'Active' : 'Inactive',
                'hourst_rate' => $user_data->hourst_rate,
                'time' => $user_data->last_login_at ? date('h:i a', strtotime($user_data->last_login_at)) : null,
                'date' => $user_data->last_login_at ? date('M d, Y', strtotime($user_data->last_login_at)) : null,
                'created' => $user_data->created_at ? date('M d, Y(D)', strtotime($user_data->created_at)) : null,
                'total_hours' => $this->ttotal_time($user_data->id,$from_dates,$to_dates),
                'approved_hours' => $this->tapproved_time($user_data->id,$from_dates,$to_dates),
                'declined_hours' => $this->tdenied_time($user_data->id,$from_dates,$to_dates),
                'approved_by' => $approved_by,
                //'color_info' => $color_info,
                'drivers_license' => !empty($user_data->drivers_license) ? url('/assets/uploads/driving-license/' . $user_data->drivers_license): null,
                'covid_report' => !empty($user_data->covid_report) ? url('/assets/uploads/covid-report/' . $user_data->covid_report) : null,
                'email' => $user_data->email,
                'password' => (Auth::user()->id == 72 || Auth::user()->id == 50 || Auth::user()->id == 282) ? null : $user_data->pass,
                'companies' => strip_tags($this->user_companies($user_data->id)),
                'dept' => $user_data->dept,
            ];
        }
        //Check if response exists or not
        if (count($response) == 0) {
            return response()->json([
                'status' => true,
                'count' => 0,
                'message' => 'No records found',
                'data' => []
            ], 200);
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Search records fetched successfully',
            'data' => $response
        ], 200);
    }

    //Function for export data
    public function allexport_data(Request $request) {
        //Get check
        $auth_user = request()->user();
        //Response
        if (!$auth_user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        if(!empty($request->from_date) && !empty($request->to_date)) {
            $frmdate = $request->from_date;
            $todate  = $request->to_date;
        } else {
            $bet_dates = explode('-', $request->search_by_payu);
            $frmdate = str_replace('_', '-', $bet_dates[0] ?? '');
            $todate  = str_replace('_', '-', $bet_dates[1] ?? '');
        }

        $search_by_comp = $request->search_by_compp;

        $from_date    = explode('-', $frmdate);
            $from_date = implode("_", $from_date);
            $to_date    = explode('-', $todate);
            $to_date = implode("_", $to_date);
            $paydate = date("M d", strtotime('+5 days', strtotime($todate)));
            $users_arrr = array();
            if(isset($search_by_comp) && $search_by_comp != 0){
                $user_companies = UserManager::where('users_id', '=',$search_by_comp)->get();
                
                /* if($search_by_comp == 9){
                    $searchbycomparr = array($search_by_comp,"12");
                    $user_companies = UserManager::whereIn('users_id', $searchbycomparr)->get();
                } 
                elseif($search_by_comp == 12){
                    $searchbycomparr = array($search_by_comp,"9");
                    $user_companies = UserManager::whereIn('users_id', $searchbycomparr)->get();
                } 
                else{
                    $user_companies = UserManager::where('users_id', '=',$search_by_comp)->get();
                }
                */
                if(isset($user_companies)){
                    foreach($user_companies as $user_company){
                        $users_arrr[] = $user_company->musers_id;
                    }
                }
                
            }
            
            $users = User::with('companies')->whereIn('id', $users_arrr)->where('role', '=', "user")->orderBy('name', 'ASC')->get();
            $user_arr = array();
            $user_count = 1;
            if(isset($users)){
                foreach($users as $userss){
                    $user_arr[] = $userss->id;
                    if($from_date == $to_date){
                        $data = TimeSheet::with('companies')
                                            ->with('houses')
                                            ->with('users')
                                            ->where('hours_day', $from_date)
                                            ->where('users_id', $userss->id)
                                            ->orderBy('hours_day', 'DESC')
                                            ->get();
                                            
                        $approved_by = $this->approved_by($userss->id);
                        $color_info = $this->color_info($userss->id); 
                    }else{
                        $data = TimeSheet::with('companies')
                                            ->with('houses')
                                            ->with('users')
                                            ->whereBetween('hours_day', array($from_date, $to_date))
                                            ->where('users_id', $userss->id)
                                            ->orderBy('hours_day', 'DESC')
                                            ->get();
                        $approved_by = $this->tapproved_by($userss->id,$from_date, $to_date);
                        $color_info = $this->color_info($userss->id); 
                    }
                    
                    if(!empty($approved_by)){
                        $approver_name = $approved_by;
                    }else{
                        $approver_name = "";
                    }
                    if(!empty($color_info)){
                        $color_info = $color_info;
                    }else{
                        $color_info = "";
                    }
                    
                    $count = 1;
                    $total_hours = 0;
                    $reg_hours = 0;
                    $holidy_hours = 0;
                    $approved_hours = 0;
                    $denied_hours = 0;
                    $htotal_pays=0;
                    if($data->count() != 0){
                        foreach ($data as $datas){
                            //$reg_hours = $datas->hours_wrk;
                            $total_hours = $total_hours + $datas->hours_wrk;
                            if($datas->approve == "2"){ 
                                    $approved_hours					  = $approved_hours + $datas->hours_wrk;
                            }elseif($datas->approve == "1"){
                                $denied_hours					  = $denied_hours + $datas->hours_wrk;
                            }
                            
                        }
                    }
            
                // print_r($approved_hours);

                if(!empty($user_companies)){
                    
                        $user_company_name = $user_companies;
                    }else{
                        
                        $user_company_name = "";
                    }
                // $user_company_name = $user_companies;
                
                
                // Holiday Hours
                    $holidays  = Holiday::all();
                    //$holidays = $holidays->date;
                    //dd($holidays);
                    $cxto_date = explode('_',$to_date);
                    $cxto_date = implode('-',$cxto_date);
                    $cxfrom_date = explode('_',$from_date);
                    $cxfrom_date = implode('-',$cxfrom_date);
                    $holiday_dt = "";
                    $holiday_dt_arr = array();
                    if(isset($holidays)){
                        foreach($holidays as $holiday){
                            //dd($holiday->date);
                            $holiday = new DateTime($holiday->date);
                            $cto_date = new DateTime($cxto_date);
                            $cfrom_date  = new DateTime($cxfrom_date);
                            if (
                            $holiday->format('y-m-d') >= $cfrom_date->format('y-m-d') && 
                            $holiday->format('y-m-d') <= $cto_date->format('y-m-d')){
                            //$holiday_dt = $holiday->format('Y/m/d');
                            $holiday_dt_arr[] = $holiday->format('Y-m-d');
                            }
                        }
                        
                    }
                    $holiday_time = 0;
                    $holiday_tm = 0; 
                    $holiday_count = 1;
                    $test =0;
                    
                    if(isset($holiday_dt_arr)){
                            foreach($holiday_dt_arr as $holiday_dt_ar){
                                    $holiday_dt_ar = explode('-',$holiday_dt_ar);
                                    $holiday_dt_ar = implode('_',$holiday_dt_ar);
                                    $holiday_time  = TimeSheet::where('users_id','=', $userss->id)
                                            ->where('approve', '=', 2)
                                            ->where('hours_day','=', $holiday_dt_ar)
                                            ->orderBy('created_at', 'DESC')
                                            ->first();
                                            
                                            if(isset($holiday_time)){
                                                $holiday_tm = $holiday_time->hours_wrk + $holiday_tm;
                                            }
                            //  $test = $holiday_time->hours_wrk;
                            }
                        //	print_r($holiday_time);die();
                            
                        }
                        
                    if(isset($holiday_tm) && $holiday_tm > 0){
                        $reg_hours = $approved_hours - $holiday_tm;
                        $holiday_time = $holiday_tm;
                    }else{
                        $reg_hours = $approved_hours;
                        $holiday_time = 0;
                    }
                $total_pay = $reg_hours * $userss->hourst_rate;
                
                if($userss->hourst_rate > 0){
                    $billed_rate =$userss->hourst_rate + number_format("6",2);
                }else{
                    $billed_rate = 0;
                }
                if($billed_rate > 0){
                    // $holiday_hourley =$billed_rate * number_format("1.5",2);
                    $holiday_hourley = $userss->hourst_rate * number_format("1.5",2);
                }else{
                    $holiday_hourley = 0;
                }
                
                $user_companies = $this->exp_user_companies($userss->id);
                $total_billed = $reg_hours * $billed_rate;
                $profit = $total_billed-$total_pay;
                $htotal_pay = $holiday_time * $holiday_hourley;
                $Total_with_holiday = $htotal_pay + $total_billed;
                $rate = $holiday_hourley + number_format("6",2);
                // $rate = 0;
                $htotal_pays=$holidy_hours * $holiday_hourley;
                
                $usersd = TimeSheet::where('users_id','=', $userss->id)->first();
                
                $holidy_hours = TimeSheet::where('users_id','=', $userss->id)
                                    ->where('approve', '=', 2)
                                    ->where('hours_day', $from_date)
                                    ->whereIn('users_id', $user_arr)
                                    ->sum('hours_wrk');
                                    
                            
                                
                    $reg_pay = $reg_hours * $userss->hourst_rate;		
                    $t_pay = $reg_pay + $htotal_pay;					  	
                    $breg_pay = $reg_hours * $billed_rate;
                    $bhh_pay = $holiday_tm * $rate;		
                    $bt_pay = $breg_pay + $bhh_pay;	

                                
                //echo $ht_pay;
                    //echo "<pre>";
                    // print_r($supervisor_arr);
                    // die;
                
                $time_sheet[] = array(

                            '#' => $user_count,
                            'SSN' => $userss->emp_id,
                            'Last Name'   => $userss->last_name,
                            'First Name'   => $userss->first_name,
                            'Company'   => $user_companies,
                            'Reg Hours' => $reg_hours,
                            'Holiday Hours'   => $holiday_tm,
                            'Total Hours'   => $approved_hours,
                            'Hourley Rate'   => '$'.$userss->hourst_rate,
                            'Holiday Rate'   => '$'.$holiday_hourley,
                            'Total Pay'   => $t_pay,
                            'Billed Rate'   => '$'.$billed_rate,
                            'Rate'          => '$'.$rate,
                            'Total Billed'   => $bt_pay,
                            'approver_name' => $approver_name,
                            'approver_color' => $color_info,
                            'Holiday Pay' => $htotal_pay,
                            'Total Holiday' => $Total_with_holiday,
                            
                            
                        );			  	
                    $user_count++;					
                }
            }
            // die();
            //$user_company_name = "";
            //$time_sheet = "";
            $stitle = $user_company_name." Timesheet details for ".$paydate;
            //$stitle1 = "Employee Details";
            Excel::create('Time Sheet', function($excel) use ($time_sheet,$stitle,$paydate,$frmdate,$todate){
                $excel->setTitle('Time Sheet');
                $excel->sheet('Time Sheet', function($sheet) use ($time_sheet,$stitle,$paydate,$frmdate,$todate){
                    $sheet->fromArray($time_sheet, null, 'A1', false, false);
                    
                    $sheet->row(1, array('','', '','','',$stitle,'', '','', '', '','', '', '',''));
                    $sheet->row(2, array('','', '','','','', '','','Employee Details', '','','Client Billing Details','','',''));
                    $sheet->row(3, array('#','SSN', 'Last Name','First Name', 'Company','Reg Hours','Holiday Hours','Total Hours', 'Hourley Rate','Holiday Rate', 'Total Pay($)', 'Billed Rate','Rate','Total Billed($)', 'Approved By'));
                    $sheet->cell('P1', function($cell) {
                                $cell->setValue('');
                            });
                    $sheet->cell('P2', function($cell) {
                                $cell->setValue('');
                            });
                    $sheet->cell('P3', function($cell) {
                                $cell->setValue('');
                            });
                    $sheet->cell('Q1', function($cell) {
                                $cell->setValue('');
                            });
                    $sheet->cell('Q2', function($cell) {
                                $cell->setValue('');
                            });
                    $sheet->cell('Q3', function($cell) {
                                $cell->setValue('');
                            });
                    $sheet->cell('R1', function($cell) {
                                $cell->setValue('');
                            });
                    $sheet->cell('R2', function($cell) {
                                $cell->setValue('');
                            });
                    $sheet->cell('R3', function($cell) {
                                $cell->setValue('');
                            });
                    $sheet->cells('A1:O1', function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(22);
                                    $cells->setAlignment('center');
                                });
                    $sheet->Cells('I2:K2', function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#FCD5B4');
                                    $cells->setAlignment('center');
                                });
                    $sheet->Cells('L2:N2', function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('##D8D8D8');
                                    $cells->setAlignment('center');
                                });			
                    $sheet->cells('A3:O3', function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#BDB76B');
                                    $cells->setAlignment('center');
                                });			
                    $i = 4;$j = 0;$k = 0;$l = 0;$m = 0;$n = 0;$o = 0;$p = 0; $q = 0; $r = 0;$s = 0;$t = 0;$u = 0;$v = 0;$w = 0;$x = 0; $y = 0; $z = 0;$emp = 0;$emp_wt_hrs = 0;
                    $total_hours = 0;
                    $t_Pay = 0;
                    $total_billed = 0;
                    $htotal_hours = 0;
                    $htotal_pay = 0;
                    $htotal_pays = 0;
                    $whtotal_billed = 0;
                    $bt_pay = 0;
                    
                    foreach ($time_sheet as $cleans) {

                        $sheet->row($i, array($cleans['#'], $cleans['SSN'], $cleans['Last Name'],$cleans['First Name'], $cleans['Company'], $cleans['Reg Hours'],  $cleans['Holiday Hours'], $cleans['Total Hours'],$cleans['Hourley Rate'],$cleans['Holiday Rate'], $cleans['Total Pay'], $cleans['Billed Rate'], $cleans['Rate'], $cleans['Total Billed'], $cleans['approver_name']));
                    
                        if($cleans['approver_color'] != ""){

                            $sheet->cell('P'.$i, function($cell) {
                                $cell->setValue('');
                            });
                            $sheet->cell('R'.$i, function($cell) {
                                $cell->setValue('');
                            });
                            $bgcolor = $cleans['approver_color'];
                            $sheet->cells('A'.$i.':O'.$i, function ($cells) use ($bgcolor) {
                                $cells->setFontColor('#000000');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontSize(14);
                                $cells->setBackground($bgcolor);
                                $cells->setAlignment('center');
                            });
                        }else{

                            $sheet->cell('P'.$i, function($cell) {
                                $cell->setValue('');
                            });
                            $sheet->cell('R'.$i, function($cell) {
                                $cell->setValue('');
                            });
                            $sheet->cells('A'.$i.':O'.$i, function ($cells) {
                                $cells->setFontColor('#000000');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontSize(14);
                                $cells->setBackground('#ffffff');
                                $cells->setAlignment('center');
                            });
                        }
                        if($cleans['approver_name'] == "Vladimir Ndebugre"){
                            $j++;
                        }elseif($cleans['approver_name'] == "Holly Wolfe"){
                            $k++;
                        }elseif($cleans['approver_name'] == "Regina Quartey"){
                            $l++;
                        }elseif($cleans['approver_name'] == "Long Caitlin"){
                            $m++;
                        }elseif($cleans['approver_name'] == "Emmanuel ndyia"){
                            $o++;
                        }elseif($cleans['approver_name'] == "John Seshie"){
                            $p++;
                        }elseif($cleans['approver_name'] == "Onbridges"){
                            $q++;
                        }elseif($cleans['approver_name'] == "Owura Kusi"){
                            $r++;
                        }elseif($cleans['approver_name'] == "Kasim Sulemana"){
                            $s++;
                        }elseif($cleans['approver_name'] == "William Kesson"){
                            $t++;
                        }
                        if($cleans['Total Hours'] > 0){
                            $emp_wt_hrs++;
                        }else{
                            $emp++;
                        }
                        $total_hours += (float)$cleans['Total Hours'];
                        $t_Pay += (float)$cleans['Total Pay'];
                        $htotal_hours += (float)$cleans['Holiday Hours'];
                        $htotal_pay  += (float)$cleans['Holiday Pay'];
                        $bt_pay += (float)$cleans['Total Billed'];
                        $total_billed += (float)$cleans['Total Holiday'];
                        $i++;
                    }
                    $supervisor_arr = array();
                    
                    if($j > 0){
                        $supervisor_arr[] = array('supervisor' => 'Vladimir', 'count' => $j);
                    }
                    if($k > 0){
                        $supervisor_arr[] = array('supervisor' => 'Holly', 'count' => $k);
                    }
                    if($l > 0){
                        $supervisor_arr[] = array('supervisor' => 'Regina' , 'count'=> $l);
                    }
                    if($m > 0){
                        $supervisor_arr[] = array('supervisor' => 'Long' , 'count'=> $m);
                    }
                    if($o > 0){
                        $supervisor_arr[] = array('supervisor' => 'Emmanuel' , 'count'=> $o);
                    }
                    if($p > 0){
                        $supervisor_arr[] = array('supervisor' => 'John' , 'count'=> $p);
                    }
                    if($q > 0){
                        $supervisor_arr[] = array('supervisor' => 'Onbridges' , 'count'=> $q);
                    }
                    if($r > 0){
                        $supervisor_arr[] = array('supervisor' => 'Owura' , 'count'=> $r);
                    }
                    if($s > 0){
                        $supervisor_arr[] = array('supervisor' => 'Kasim' , 'count'=> $s);
                    }
                    if($t > 0){
                        $supervisor_arr[] = array('supervisor' => 'William' , 'count'=> $t);
                    }
                    // echo $s;
                    // echo "<pre>";
                    // print_r($supervisor_arr);
                    // die;
                //sheet->row($i, array('','', '','','Sum', '', '$'.$htotal_hours,',$total_hours,'','',$'.$total_Pay,'','','$'.$total_billed));
                    $sheet->row($i, array('','', '','','Sum', '','',$total_hours,'','', '$'.$t_Pay, '','', '$'.$bt_pay));
                    $sheet->cells('E'.$i.':O'.$i, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setFontWeight('bold');
                                    $cells->setBackground('#fcd5b4');
                                    $cells->setAlignment('center');
                                });
                    $row1 = $i+1;	
                    $sheet->row($row1, array('','Summary', '','','','', '', '','', '', ''));
                    $sheet->cells('A'.$row1.':D'.$row1, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setFontWeight('bold');
                                    $cells->setBackground('#ffffff');
                                    $cells->setAlignment('center');
                                    
                                });
                    
                    $row2 = $i+2;
                    $sheet->row($row2, array('# emp', '',$emp_wt_hrs,'','', '', '','', '', ''));
                    $sheet->cells('A'.$row2.':D'.$row2, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#dbeef3');
                                    $cells->setAlignment('center');
                                });
                    $row3 = $i+3;
                    $sheet->row($row3, array('# Emp with 0hrs', '',$emp,'','', '', '','', '', ''));
                    $sheet->cells('A'.$row3.':D'.$row3, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#dbeef3');
                                    $cells->setAlignment('center');
                                });			
                    $row4 = $i+4;
                    $sheet->row($row4, array('Holiday Hours', '',$htotal_hours,'','', '', '','', '', ''));
                    $sheet->cells('A'.$row4.':D'.$row4, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#dbeef3');
                                    $cells->setAlignment('center');
                                });	
                    $row5 = $i+5;
                    $sheet->row($row5, array('Holiday Pay', '','$'.$htotal_pay,'','', '', '','', '', ''));
                    $sheet->cells('A'.$row5.':D'.$row5, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#dbeef3');
                                    $cells->setAlignment('center');
                                });	
                    $row6= $i+6;
                    $sheet->row($row6, array('Holiday Approved', '','$'.$htotal_pays,'','', '', '','', '', ''));
                    $sheet->cells('A'.$row6.':D'.$row6, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#dbeef3');
                                    $cells->setAlignment('center');
                                }); 
                    $row7 = $i+7;
                    $sheet->row($row7, array('payperiod',date("d-M-y", strtotime($frmdate)), date("d-M-y", strtotime($todate)),date("d-M-y", strtotime($paydate)),'','', '', '','', '', ''));
                    $sheet->cells('A'.$row7.':D'.$row7, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#ffffff');
                                    $cells->setAlignment('center');
                                });
                    $kkk = $row7+1;
                    if(!empty($supervisor_arr) && isset($supervisor_arr)){
                        foreach($supervisor_arr as $supervisor_ar){
                            $sheet->row($kkk, array($supervisor_ar['supervisor'],'Approved', $supervisor_ar['count'],'','','', '', '','', '', ''));
                            $sheet->cells('A'.$kkk.':D'.$kkk, function ($cells) {
                                        $cells->setFontColor('#000000');
                                        $cells->setFontFamily('Calibri');
                                        $cells->setFontSize(14);
                                        $cells->setBackground('#dbeef3');
                                        $cells->setAlignment('center');
                                    });
                            $kkk++;
                        }
                        
                    } 
                    $row11 = $kkk;
                    $sheet->row($row11, array('Total Billed', '','$'.$bt_pay,'','', '', '','', '', ''));
                    $sheet->cells('A'.$row11.':D'.$row11, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#dbeef3');
                                    $cells->setAlignment('center');
                                }); 
                    $row12 = $kkk+1;
                    $sheet->row($row12, array('Total Holiday', '','$'.$htotal_pay,'','', '', '','', '', ''));
                    $sheet->cells('A'.$row12.':D'.$row12, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#dbeef3');
                                    $cells->setAlignment('center');
                                });	
                    $row13 = $kkk+2;
                    $sheet->row($row13, array('Total hrs', '',$total_hours,'','', '', '','', '', ''));
                    $sheet->cells('A'.$row13.':D'.$row13, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#dbeef3');
                                    $cells->setAlignment('center');
                                });
                    $row14 = $kkk+3;
                    $sheet->row($row14, array('Total Pay', '','$'.$t_Pay,'','', '', '','', '', ''));
                    $sheet->cells('A'.$row14.':D'.$row14, function ($cells) {
                                    $cells->setFontColor('#000000');
                                    $cells->setFontFamily('Calibri');
                                    $cells->setFontSize(14);
                                    $cells->setBackground('#dbeef3');
                                    $cells->setAlignment('center');
                                });							
                
                    
                    
                    
                }); 
            })->download('xlsx');
    }

    //Function for t approved 
    public static function tapproved_by($id, $from_date, $to_date) {
		if($from_date != "" && $to_date != ""){
			$approved_by = TimeSheet::where('users_id', '=', $id)
                ->whereBetween('hours_day', array($from_date, $to_date))
                ->where('approve', '=', 2)
                ->get();
		}
		$approved_users = array();
		if(isset($approved_by)){
			foreach($approved_by as $approved_bys){
				$approved_users[] = $approved_bys->approved_by;
			}
			
		}
		//Get users
		$app_users = User::with('companies')->whereIn("id", $approved_users)->orderBy('name', 'ASC')->get();
		$user_name = "";
		//$user_detail = array();
		$count = 1;
		if(isset($app_users)){
			foreach($app_users as $app_user){
				if($count > 1){
					$user_name .= ", ".$app_user->name;
				}else{
					$user_name .= $app_user->name;
				}
				// $user_detail[] = $user_name;
				// $user_detail[] = $app_user->color_field;
				$count++;
			}
			
		}
        //$approved_time = TimeSheet::where('users_id', '=', $id)->where('approve', '=', 2)->sum('hours_wrk');
		return $user_name;
    }

    //Function for color info
    public static function color_info($id) {
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

    //Function for ttotal time
    public static function ttotal_time($id, $from_date, $to_date) {
        //$total_time = TimeSheet::where('users_id', '=', $id)->sum('hours_wrk');
		if($from_date == $to_date){
			$total_time = TimeSheet::with('companies')
                ->with('houses')
                ->with('users')
                ->where('hours_day', $from_date)
                ->where('users_id', '=', $id)
                //->orderBy(sum('hours_wrk'), 'DESC')
                //->get();
                ->sum('hours_wrk');					
		}else{
			$total_time = TimeSheet::with('companies')
                ->whereBetween('hours_day', array($from_date, $to_date))
                ->where('users_id', '=', $id)
                //->orderBy(sum('hours_wrk'), 'DESC')
                //->get();
                ->sum('hours_wrk');
		}
	    //print_r($total_time);
		return $total_time;
    }

    //Function t approved time
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

    //Function for tdenied time
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

    //Fnction ofr user companies
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

    //Function for user companies
    public static function exp_user_companies($id) {
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

   
public static function approved_by($id)
    {
		
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}

		if($frm_date != "" && $t_date != ""){
			 $frm_date    = explode('-', $frm_date);
						$frm_date = implode("_", $frm_date);
						$t_date    = explode('-', $t_date);
					   $t_date = implode("_", $t_date);
			$approved_by = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($frm_date, $t_date))
								->where('approve', '=', 2)
								->get();
								
		}
		$approved_users = array();
		
		if(isset($approved_by)){
			foreach($approved_by as $approved_bys){
				$approved_users[] = $approved_bys->approved_by;
			}
			
		}
		
		$app_users = User::with('companies')->whereIn("id", $approved_users)->orderBy('name', 'ASC')->get();
		$user_name = "";
		// $user_detail = array();
		$count = 1;
		if(isset($app_users)){
			foreach($app_users as $app_user){
				if($count > 1){
					$user_name .= ", ".$app_user->name;
				}else{
					$user_name .= $app_user->name;
				}
				// $user_detail[] = $user_name;
				// $user_detail[] = $app_user->color_field;
				$count++;
			}
			
		}
		return $user_name;
	}
    public static function total_time($id)
    {
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		
		$users_arrr = array();
		$users = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->get();
		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}

		
		if($frm_date != "" && $t_date != ""){
			$total_time = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($frm_date, $t_date))
								->sum('hours_wrk');
		}else{
			$total_time = "";
		}

		return $total_time;
    }
	
	public static function approved_time($id)
    {
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		
		$users_arrr = array();
		$users = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->get();
		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}

		if($frm_date != "" && $t_date != ""){
			$approved_time = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($frm_date, $t_date))
								->where('approve', '=', 2)
								->sum('hours_wrk');
		}else{
			$approved_time = "";
		}
        // $approved_time = TimeSheet::where('users_id', '=', $id)->where('approve', '=', 2)->sum('hours_wrk');
		return $approved_time;
    }
	
	public static function denied_time($id)
    {
		$payperiods_dates = payperiods();
		if(isset($payperiods_dates)){
			 $frm_date  = $payperiods_dates[0]['frm_date'];
			 $t_date = $payperiods_dates[0]['t_date'];
		}else{
			$frm_date  = "";
			$t_date = "";
		}
		$users_arrr = array();
		$users = User::with('companies')->where("role", "=", "user")->orderBy('name', 'ASC')->get();
		if(isset($users)){
			foreach($users as $user){
				$users_arrr[] = $user->id;
			}
		}

		if($frm_date != "" && $t_date != ""){
			$denied_time = TimeSheet::where('users_id', '=', $id)
								->whereBetween('hours_day', array($frm_date, $t_date))
								->where('approve', '=', 1)
								->sum('hours_wrk');
		}else{
			$denied_time = "";
		}
        // $denied_time = TimeSheet::where('users_id', '=', $id)->where('approve', '=', 1)->sum('hours_wrk');
		return $denied_time;
    }
}
