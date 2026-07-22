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

class VaccationController extends Controller
{
    //Function for vocation hours
    public function vocations_hours() {
        //Check auth
        $auth = request()->user();
        //Response
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Login user id
        $user = $auth->id;
        //Get companies
        $companies = UserManager::where('musers_id', $user)->get();
        $company_id = array();
        foreach ($companies as $company) {

            $company_data = Company::where('id', $company->users_id)->first();

            if ($company_data) {

                $company_id[] = $company_data->company;
            }
        }
        //Get users
        $users = User::with('companies')
            ->whereIn('companies_id', $company_id)
            ->where('role', 'user')
            ->orderBy('created_at', 'DESC')
            ->get();

        $user_arr = array();
        foreach ($users as $userss) {
            $user_arr[] = $userss->id;
        }
        //Get vacation records
        $approve_vchour = UserVaccatioStatusn::whereIn('user_id', $user_arr)
            ->orderBy('created_at', 'DESC')
            ->get();
        //No data
        if ($approve_vchour->isEmpty()) {

            return response()->json([
                'status' => false,
                'message' => 'No vacation records found',
                'data' => []
            ]);
        }
        $data = array();
        foreach ($approve_vchour as $datas) {
            //User info
            $user_info = User::find($datas->user_id);
            //Vacc start
            $vacc_frm = explode('_', $datas->vacc_start);
            $vacc_frm = implode("-", $vacc_frm);
            //Vacc end
            $vacc_end = explode('_', $datas->vacc_end);
            $vacc_end = implode("-", $vacc_end);
            //Reporting
            $vacc_rbu = explode('_', $datas->vacc_rbu);
            $vacc_rbu = implode("-", $vacc_rbu);
            //Status
            if ($datas->vacc_status == 0) {
                $status = "Pending";
            } elseif ($datas->vacc_status == 1) {
                $status = "Approved";
            } else {
                $status = "Declined";
            }
            $data[] = [
                'id' => $datas->id,
                'user_id' => $auth->id,
                'user' => $user_info->name ?? '',
                'from' => date('M d, Y', strtotime($vacc_frm)),
                'to' => date('M d, Y', strtotime($vacc_end)),
                'reporting' => date('M d, Y', strtotime($vacc_rbu)),
                'created' => date('M d, Y', strtotime($datas->created_at)),
                'status' => $status,
            ];
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Vacation records fetched successfully',
            'data' => $data
        ]);
    }

    //Function for vocations approve
    public function vacc_approve($id) {
        //Get auth
        $user = request()->user();
        //Response
        if(!$user){
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ],401);
        }
        //Check Vaccation ID Exists
        $data_user_vacc = UserVaccatioStatusn::where('id','=', $id)
            ->orderBy('created_at', 'DESC')
            ->first();
        //Response
        if(!$data_user_vacc){
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ],404);
        }
        //Auth login id
        $app_id = $user->id;
        //Check vacc status exists or not
        if($data_user_vacc->vacc_status != 1){
            $vacc_user = $data_user_vacc->user_id;
            //Get user vocation data
            $data_vacc = UserVaccation::where('user_id','=', $vacc_user)
                ->orderBy('created_at', 'DESC')
                ->first();
            //vacc start and end
            $vacc_frm = explode('_', $data_user_vacc->vacc_start);
            $vacc_frm = implode("-", $vacc_frm);
            $vacc_to = explode('_', $data_user_vacc->vacc_end);
            $vacc_to = implode("-", $vacc_to);

            $date1 = new DateTime(date('m/d/y', strtotime($vacc_frm)));
            $date2 = new DateTime(date('m/d/y', strtotime($vacc_to)));

            $diff = $date2->diff($date1);

            $days = $diff->days;
            $hours = $diff->h;
            $hours = $hours + ($diff->days * 24);
            $hours = floatval(8 * $days);

            if(isset($data_vacc)){
                $used_hours = $data_vacc->vacc_vc;
                $avail_hours = $data_vacc->vacc_sl;
                $hours_requested = $hours;
                $check_hours = $avail_hours - $hours_requested;

                if($check_hours >= 0){
                    $used_hours = $used_hours + $hours_requested;
                    $avail_hours = $avail_hours - $hours_requested;
                    $form_data = array(
                        'vacc_sl' => $avail_hours,
                        'vacc_vc' => $used_hours,
                        'vacc_aprby' => $app_id,
                    );
                    $form_dat1 = array(
                        'vacc_status' => 1,
                    );

                    $user_update = UserVaccation::where('user_id','=', $vacc_user)
                        ->update($form_data);

                    $user_status_update = UserVaccatioStatusn::where('user_id','=', $vacc_user)
                        ->update($form_dat1);
                    //Response
                    if($user_update){

                        return response()->json([
                            'status' => true,
                            'message' => 'Vaccation approved successfully.'
                        ],200);

                    }else{
                        //Response
                        return response()->json([
                            'status' => false,
                            'message' => 'Error!'
                        ],404);
                    }
                }else{
                    //Response
                    return response()->json([
                        'status' => false,
                        'message' => 'Vaccation hours are not left or have used your all hours for this user.'
                    ],404);
                }
            }else{
                //Response
                return response()->json([
                    'status' => false,
                    'message' => 'Vaccation hours are not assign to this user, please contact admin'
                ],404);
            }
        }else{
            //Response
            return response()->json([
                'status' => false,
                'message' => 'Vaccation already Approved'
            ],404);
        } 
    }

    //Function for vacation decline
    public function vacc_decline($id) {
        //Get auth
        $user = request()->user();
        //Response
        if(!$user){
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ],401);
        }
        //Check Vaccation ID Exists
        $data_user_vacc = UserVaccatioStatusn::where('id','=', $id)
            ->orderBy('created_at', 'DESC')
            ->first();
        //Response
        if(!$data_user_vacc){
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ],404);
        }
        //Auth login id
        $app_id = $user->id;
        //Check already declined
        if($data_user_vacc->vacc_status == 2){
            //Response
            return response()->json([
                'status' => false,
                'message' => 'Vaccation already declined'
            ],400);
        }

        $vacc_user = $data_user_vacc->user_id;
        //Get user vaccation data
        $data_vacc = UserVaccation::where('user_id','=', $vacc_user)
            ->orderBy('created_at', 'DESC')
            ->first();

        //Check if vaccation data not found
        if(!$data_vacc){
            return response()->json([
                'status' => false,
                'message' => 'Vaccation hours are not assign to this user, please contact admin'
            ],404);
        }

        //Update approver in UserVaccation table
        $form_data = array(
            'vacc_aprby' => $app_id,
        );
       
        UserVaccation::where('user_id','=', $vacc_user)
            ->update($form_data);

        //Update status in UserVaccatioStatusn table
        $form_dat1 = array(
            'vacc_status' => 2,
        );
        
        $user_status_update = UserVaccatioStatusn::where('id','=', $id)
            ->update($form_dat1);

        //Response
        if($user_status_update){
            return response()->json([
                'status' => true,
                'message' => 'Vaccation declined Successfully.'
            ],200);

        }else{
            //Response
            return response()->json([
                'status' => false,
                'message' => 'Error!'
            ],500);
        }
    }
}

