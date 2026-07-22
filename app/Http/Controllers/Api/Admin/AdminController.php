<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\TimeSheet;
use App\User;
use App\Company;
use App\Holiday;
use App\House;
use App\UserManager;
use Excel;
use App\AdminMeta;
use Carbon\Carbon;
use App\LoginLogouttime;
use DateTime;
use App\Payperiods;
use Twilio\Rest\Client;
use App\UserVaccatioStatusn;
use App\UserVaccation;
use App\SmsLog;
use App\SmsLogCompany;

class AdminController extends Controller
{
    //Function for all users list
    public function all_users() {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get users
        $users = User::where('role', 'user')
            ->orderBy('created_at', 'DESC')->select('id', 'name')
            ->get();
        //Response
        return response()->json([
            'status' => true,
            'message' => 'User list fetched successfully',
            'data' => [
                'Users' => $users
            ]
        ]);
    }

    //Function for send sms
    public function send_sms(Request $request) {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Validate input fields
        $validator = Validator::make($request->all(), [
            'message' => 'required', 
        ]);
        //Validation
        if ($validator->fails()) {
            //Response
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        //Twilio
        $client = new Client(
            env('TWILIO_SID'), 
            env('TWILIO_TOKEN')
        );
        //Var
        $numbers = [];
        $userIds = [];
        $companyIds = [];
        //Get all Users
        if ($request->all_user == 1) {
            $userIds = User::where('role', 'user')
                ->pluck('id')
                ->toArray();
        }
        //Get users IDs
        if (!empty($request->users_id)) {
            if (is_array($request->users_id)) {
                $userIds = $request->users_id;
            } else {
                $decoded = json_decode($request->users_id, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $userIds = $decoded;
                } else {
                    $userIds = [$request->users_id];
                }
            }
        }
        //Company IDs
        if (!empty($request->company_id)) {
            if (is_array($request->company_id)) {
                $companyIds = $request->company_id;
            } else {
                $decoded = json_decode($request->company_id, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $companyIds = $decoded;
                } else {
                    $companyIds = [$request->company_id];
                }
            }
            $companyUsers = UserManager::whereIn(
                'users_id',
                $companyIds
            )->pluck('musers_id')->toArray();

            $userIds = array_merge($userIds, $companyUsers);
        }

        $userIds = array_unique($userIds);
        $userIds = array_values($userIds);

        foreach ($userIds as $id) {
            $user = User::find($id);
            if ($user && !empty($user->phone_no)) {
                $phone = preg_replace(
                    '/[^0-9]/',
                    '',
                    $user->phone_no
                );
                if (strlen($phone) == 10) {
                    $numbers[] = [
                        'users_id' => $id,
                        'phone_no' => '+91' . $phone
                    ];
                }
            } else {
                if (!empty($request->numbers)) {
                    foreach (explode(',', $request->numbers) as $no) {
                        $phone = preg_replace(
                            '/[^0-9]/',
                            '',
                            trim($no)
                        );
                        if (strlen($phone) == 10) {
                            $numbers[] = [
                                'users_id' => $id,
                                'phone_no' => '+91' . $phone
                            ];
                        }
                    }
                } else {
                    $log = SmsLog::create([
                        'users_id' => $id,
                        'phone_no' => null,
                        'message' => $request->message,
                        'status' => 'No Mobile Number'
                    ]);
                    foreach ($companyIds as $cid) {
                        SmsLogCompany::create([
                            'sms_log_id' => $log->id,
                            'company_id' => $cid
                        ]);
                    }
                }
            }
        }
        $numbers = collect($numbers)
            ->unique('phone_no')
            ->values()
            ->toArray();
        $dbMessage = $request->message;
        $message = "FitSheets Alert: " .
            $request->message .
            " - FitSheets Team";
        $count = 0;
        $failed = 0;
        foreach ($numbers as $data) {
            try {
                $client->messages->create(
                    $data['phone_no'],
                    [
                        'from' => env('TWILIO_FROM'),
                        'body' => $message
                    ]
                );
                $log = SmsLog::create([
                    'users_id' => $data['users_id'],
                    'phone_no' => $data['phone_no'],
                    'message' => $dbMessage,
                    'status' => 'Sent'
                ]);
                foreach ($companyIds as $cid) {
                    SmsLogCompany::create([
                        'sms_log_id' => $log->id,
                        'company_id' => $cid
                    ]);
                }
                $count++;
            } catch (\Exception $e) {
                $log = SmsLog::create([
                    'users_id' => $data['users_id'],
                    'phone_no' => $data['phone_no'],
                    'message' => $dbMessage,
                    'status' => 'Failed',
                    'error' => $e->getMessage()
                ]);
                foreach ($companyIds as $cid) {
                    SmsLogCompany::create([
                        'sms_log_id' => $log->id,
                        'company_id' => $cid
                    ]);
                }
                $failed++;
            }
        } 
        //Response
        return response()->json([
            'status' => true,
            'message' => $count . ' SMS sent successfully!',
        ]);
    }
}


