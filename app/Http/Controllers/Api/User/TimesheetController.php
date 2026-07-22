<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\TimeSheet;
use App\User;
use App\House;
use App\Company;
use Illuminate\Support\Facades\Validator;

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

        return response()->json([
            'status' => true,
            'message' => 'Company service list fetched successfully',
            'data' => [
                'company_services' => $company_services
            ]
        ]);
    }

    //Function for create timesheet
    public function store(Request $request) {
        //Get auth user
        $user = request()->user();

        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }

        //Get auth id
        $user_id = $user->id;

        //Validation
        $rules = [
            'company_id' => 'required',
            'house_id'   => 'required',
            'hours_day'  => 'required',
            'time_in'    => 'required',
            'time_out'   => 'required',
        ];

        $customMessages = [
            'company_id.required' => 'Please select company',
            'house_id.required'   => 'Please select house',
            'hours_day.required'  => 'Please add date',
            'time_in.required'    => 'Time in required',
            'time_out.required'   => 'Time out required',
        ];

        $validator = Validator::make(
            $request->all(),
            $rules,
            $customMessages
        );

        //Response
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        //Vacation
        if (!empty($request->vacc)) {
            $vacc = $request->vacc;
        } else {
            $vacc = 0;
        }

        //Date format convert
        $date = date('Y_m_d', strtotime($request->hours_day));

        //Time format
        $time_in  = date('h:i a', strtotime($request->time_in));
        $time_out = date('h:i a', strtotime($request->time_out));

        //Hours calculation
        $starttimestamp = strtotime($request->time_in);
        $endtimestamp   = strtotime($request->time_out);

        if (
            strpos(strtolower($request->time_in), 'pm') !== false &&
            strpos(strtolower($request->time_out), 'am') !== false
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

        //Check timesheet already exists or not
        $exists = TimeSheet::where('hours_day', $date)
            ->where('users_id', $user_id)
            ->where('time_in', $request->time_in)
            ->exists();

        if ($exists) {

            return response()->json([
                'status' => false,
                'message' => 'Time already saved for this user on this date'
            ], 409);
        }

        //Save data
        $form_data = [
            'companies_id'    => $request->company_id,
            'houses_id'       => $request->house_id,
            'users_id'        => $user_id,
            'hours_day'       => $date,
            'time_in'         => $request->time_in,
            'time_out'        => $request->time_out,
            'hours_wrk'       => $hours,
            'vacation_status' => $vacc,
            'cmcheck_status'  => '1'
        ];

        $ts_store = TimeSheet::create($form_data);

        if ($ts_store) {

            return response()->json([
                'status' => true,
                'message' => 'Hours Added Successfully!!',
                'data' => $ts_store
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error while Adding Hours!!'
        ], 500);
    }

    //Function for all timesheets
    public function index(Request $request) {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        
        //Get auth login id
        $user_id = $user->id;

        //Get timesheet
        $data = TimeSheet::with('companies','houses','users')
            ->where('users_id', '=', $user_id)
            ->orderBy('hours_day', 'DESC')->select('id', 'users_id', 'companies_id', 'houses_id', 'time_in', 'time_out', 'hours_wrk', 'hours_day', 'approve', 'remarks')
            ->paginate(15);

        //Check if empty timesheet
        if ($data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No timesheet records found',
                'data' => []
            ], 404);
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Timesheet list fetched successfully',
            'data' => $data
        ]);
    }

    //Function for update timesheet
    public function update(Request $request, $id) {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get auth id
        $user_id = $user->id;
        
        //Get timesheet
        $record = TimeSheet::where('id', $id)->first();
        
        //Check if record found not
        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }
        //Rules
        $rules = [
            'company_id' => 'required',
            'house_id'   => 'required',
            'hours_day'  => 'required',
            'time_in'    => 'required',
            'time_out'   => 'required',
        ];
        //Message
        $customMessages = [
            'company_id' => 'Please select company',
            'house_id'   => 'Please select house',
            'hours_day'  => 'Please add date',
            'time_in'    => 'required',
            'time_out'   => 'required',
        ];
        //Validate input filed
        $validator = \Validator::make($request->all(), $rules, $customMessages);
        //Response
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        if (!empty($request->vacc)) {
            $vacc = $request->vacc;
        } else {
            $vacc = 0;
        }

        $date = explode('-', $request->hours_day);
        $date = implode("_", $date);

        substr($request->time_in, 0, -2);
        substr($request->time_out, 0, -2);

        $starttimestamp = strtotime($request->time_in);
        $endtimestamp   = strtotime($request->time_out);

        if (strpos($request->time_in, 'pm') !== false && strpos($request->time_out, 'am') !== false) {
            $hours = (abs(($endtimestamp - $starttimestamp) / 3600));
            if ($hours < 0) {
                $hours = abs(($starttimestamp - $endtimestamp) / 3600);
                $hours = 24 - $hours;
            } else {
                $hours = 24 - $hours;
            }
        } elseif ($starttimestamp == $endtimestamp) {
            $hours = 24;
        } else {
            $hours = (abs(($endtimestamp - $starttimestamp) / 3600));
        }

        $form_data = array(
            'companies_id'    => $request->company_id,
            'houses_id'       => $request->house_id,
            'users_id'        => $user_id,
            'hours_day'       => $date,
            'time_in'         => $request->time_in,
            'time_out'        => $request->time_out,
            'hours_wrk'       => $hours,
            'vacation_status' => $vacc,
        );
        
        //Update time sheets
        $ts_update = TimeSheet::whereId($id)->update($form_data);

        //Response
        if ($ts_update) {
            return response()->json([
                'status' => true,
                'message' => 'Hours Updated Successfully!!',
                'data' => $form_data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Error while updating Hours!!',
                'data' => null
            ], 500);
        }
    }

    //Function for delete time sheet
    public function destroy($id) {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Check record first
        $data = TimeSheet::where('id', $id)->first();
        //Response
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }
        //Delete record
        $data->delete();
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Timesheet deleted successfully'
        ]);
    }

    //Function for search time sheet
    public function srch_time(Request $request) {
        //Get check
        $user = request()->user();
        //Check auth exists or not
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }

        //Get auth id
        $user_id = $user->id;

        //Filter
        $from_date = implode("_", explode('-', $request->frm_dt));
        $to_date   = implode("_", explode('-', $request->to_dt));

        if ($from_date == $to_date) {
            $data = TimeSheet::with(['companies','users','houses'])
                ->where('hours_day', $from_date)
                ->where('users_id', $user_id)
                ->orderBy('created_at', 'DESC')
                ->get();
        } else {
            $data = TimeSheet::with(['companies','users','houses'])
                ->where('users_id', $user_id)
                ->whereBetween('hours_day', [$from_date, $to_date])
                ->orderBy('created_at', 'DESC')
                ->get();
        }

        if ($data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry No Data found!!',
                'data' => []
            ]);
        }

        $total_hours = 0;
        $approved_hours = 0;
        $denied_hours = 0;

        $result = [];

        foreach ($data as $key => $datas) {

            $total_hours += $datas->hours_wrk;

            if ($datas->approve == "2") {
                $approved_hours += $datas->hours_wrk;
                $approve_status = "Approved";
            } elseif ($datas->approve == "1") {
                $denied_hours += $datas->hours_wrk;
                $approve_status = "Declined";
            } else {
                $approve_status = "Pending";
            }

            $vacation = $datas->vacation_status == "1" ? "Yes" : "No";

            $hours = implode(":", explode('_', $datas->hours_wrk));

            $hours_day = implode("/", explode('_', $datas->hours_day));
            $hours_day = date("M d, Y", strtotime($hours_day));

            $result[] = [
                'sr_no' => $key + 1,
                'emp_id' => $datas->users->emp_id,
                'name' => $datas->users->name,
                'email' => $datas->users->email,
                'dept' => $datas->users->dept,
                'company' => $datas->companies->company,
                'house' => $datas->houses->house_add,
                'time_in' => $datas->time_in,
                'time_out' => $datas->time_out,
                'hours' => $hours,
                'date' => $hours_day,
                'rate' => $datas->users->hourst_rate,
                'vacation' => $vacation,
                'approval' => $approve_status
            ];
        }
        
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Timesheet data fetched successfully',
            'summary' => [
                'payperiod' => date("d", strtotime($from_date)) . '-' . date("d M", strtotime($to_date)),
                'total_hours' => $total_hours,
                'approved_hours' => $approved_hours,
                'denied_hours' => $denied_hours
            ],
            'data' => $result
        ]);
    }
}