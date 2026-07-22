<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\TimesheetaController;
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
use App\House;
use App\Department;
use App\UserManager;
use DateTime;
use App\Payperiods;
use App\UserSupervisorRel;
use App\UserCasemanagerRel;
use App\UserVaccatioStatusn;
use App\UserVaccation;

class TimesheetController extends Controller
{
    //Function for show timesheets
    public function timesheets(Request $request, $id) {
        //Check auth
        $auth_user = request()->user();
        //Response
        if (!$auth_user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Check user
        $user = User::where('role', 'user')
            ->where('id', $id)
            ->first();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
        //Get timesheet record
        $query = TimeSheet::with(['users','companies','houses'])->where('users_id', $id);
        //Payperiod Filter
        if (!empty($request->payperiods)) {
            $dates = explode('-', $request->payperiods);
            if (count($dates) == 2) {
                $from_date = trim($dates[0]);
                $to_date   = trim($dates[1]);
                //query
                $query->whereBetween('hours_day', [
                    $from_date,
                    $to_date
                ]);
            }
        }

        //Date Range Filter
        if (!empty($request->from_date) && !empty($request->to_date)) {
            $query->whereBetween('hours_day', [
                str_replace('-', '_', $request->from_date),
                str_replace('-', '_', $request->to_date)
            ]);
        }

        //Company Filter
        if (!empty($request->search_by_compp)) {
            $query->where('companies_id', $request->search_by_compp);
        }

        $data = $query->orderBy('created_at', 'DESC')->get();
        //Response
        if ($data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry No Data!!',
                'name' => $user->name,
                'data' => []
            ], 200);
        }
        //Var
        $result = [];
        foreach ($data as $datas) {
            $result[] = [
                'id' => $datas->id,
                'emp_id' => $datas->users->emp_id ?? '',
                'name' => $datas->users->name ?? '',
                'department' => $datas->users->dept ?? '',
                'company' => $datas->companies->company ?? '',
                'house' => isset($datas->houses->house_add) ? substr($datas->houses->house_add, 0, 14) : '',
                'time_in' => $datas->time_in,
                'time_out' => $datas->time_out,
                'hours_worked' => str_replace('_',':', $datas->hours_wrk),
                'date' => !empty($datas->hours_day) ? date('M d, Y(D)', strtotime(str_replace('_', '/', $datas->hours_day))) : '',
                'hours_rate' => $datas->users->hourst_rate ?? '',
                'remarks' => $datas->vacation_status == "1" ? 'Yes': 'No',
                'approved' => $datas->approve == "2" ? 'Yes' : ($datas->approve == "1" ? 'No' : 'Pending'),
                'approved_by' => !empty($datas->approved_by) ? TimesheetaController::userName($datas->approved_by) : '--',
                'user_added_hours_at' => !empty($datas->created_at) ? date('M d, Y h:i a', strtotime($datas->created_at)) : '',
                'approved_at' => !empty($datas->approved_at) ? date('M d, Y h:i a', strtotime($datas->approved_at)) : '--'
            ];
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Timesheets fetched successfully',
            'name' => $user->name,
            'data' => $result
        ], 200);
    }

    //Function for update timesheets
    public function update(Request $request, $id) {
        //Check auth
        $auth_user = $request->user();
        //Response
        if (!$auth_user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get timesheet
        $timesheet = TimeSheet::find($id);
        //Response
        if (!$timesheet) {
            return response()->json([
                'status' => false,
                'message' => 'Timesheet not found'
            ], 404);
        }
        //Validate input fields
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
                'message' => $validator->errors()->first()
            ], 422);
        }

        $vacc = !empty($request->vacc) ? $request->vacc : 0;
        $date = str_replace('-', '_', $request->hours_day);
        $starttimestamp = strtotime($request->time_in);
        $endtimestamp = strtotime($request->time_out);

        if (strpos(strtolower($request->time_in), 'pm') !== false && strpos(strtolower($request->time_out), 'am') !== false) {
            $hours = abs(($endtimestamp - $starttimestamp) / 3600);
            $hours = 24 - $hours;
        } elseif ($starttimestamp == $endtimestamp) {
            $hours = 24;
        } else {
            $hours = abs(($endtimestamp - $starttimestamp) / 3600);
        }
        //Update timesheet
        $timesheet->update([
            'companies_id'    => $request->company_id,
            'houses_id'       => $request->house_id,
            'users_id'        => $request->user_id,
            'hours_day'       => $date,
            'time_in'         => $request->time_in,
            'time_out'        => $request->time_out,
            'hours_wrk'       => $hours,
            'hours_price'     => $request->hour_rate,
            'remarks'         => $request->remarks,
            'vacation_status' => $vacc,
            'approve'         => $request->approved,
        ]);
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Hours Updated Successfully',
            'data' => $timesheet
        ], 200);
    }
}
