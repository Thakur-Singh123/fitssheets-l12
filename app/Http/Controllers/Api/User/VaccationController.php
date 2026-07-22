<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Excel;
use App\TimeSheet;
use App\User;
use App\House;
use App\Company;
use DateTime;
use Carbon\Carbon;
use App\UserVaccatioStatusn;
use App\UserVaccation;

class VaccationController extends Controller
{
    //Function for store vocation
    public function store(Request $request) {
        //Get check
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        $rules = [
            'vacc_frm' => 'required',
        ];

        $customMessages = [
            'vacc_frm' => 'From not selected'
        ];

        $validator = \Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = Auth::guard('api')->user()->id;

        $vacc_frm = explode('-', $request->vacc_frm);
        $vacc_frm = implode("_", $vacc_frm);

        $vacc_to = explode('-', $request->vacc_to);
        $vacc_to = implode("_", $vacc_to);

        $report_by = explode('-', $request->report_by);
        $report_by = implode("_", $report_by);

        $data_vacc = UserVaccation::where('user_id', '=', $user)
            ->orderBy('created_at', 'DESC')
            ->first();

        $date1 = new \DateTime(date('m/d/y', strtotime($request->vacc_frm)));
        $date2 = new \DateTime(date('m/d/y', strtotime($request->vacc_to)));

        $diff = $date2->diff($date1);

        $days = $diff->days;
        $hours = $diff->h;
        $hours = $hours + ($diff->days * 24);
        $hours = floatval(8 * $days);

        // echo "Hello";
        // print_r($data_vacc);
        // die;

        // if(isset($data_vacc)){

        // $used_hours = $data_vacc->vacc_vc;
        // $avail_hours = $data_vacc->vacc_sl;
        // $hours_requested = $hours;
        // $check_hours = $avail_hours - $hours_requested;
        // if($check_hours >= 0){
        
        //Create data
        $form_data = array(
            'user_id' => $user,
            'vacc_start' => $vacc_frm,
            'vacc_end' => $vacc_to,
            'vacc_comments' => $request->comments,
            'vacc_top' => $request->time_policy,
            'vacc_rbu' => $report_by,
            'vacc_status' => 0,
        );

        $ts_store = UserVaccatioStatusn::create($form_data);
        
        //Response
        if ($ts_store) {
            return response()->json([
                'status' => true,
                'message' => 'Vaccation Added Successfully!!',
                'data' => $ts_store
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Error while Adding Vaccation!!'
            ], 500);
        }

        // $used_hours = $used_hours + $hours_requested;
        // $avail_hours = $avail_hours - $hours_requested;
        // $form_data = array(
        // 'vacc_sl' => $avail_hours,
        // 'vacc_vc' => $used_hours,
        // );
        // $user_update = UserVaccation::where('user_id','=', $user)->update($form_data);

        // }else{
        //  return response()->json([
        //      'status' => false,
        //      'message' => 'Vaccation hours are not left or you have used your all hours.'
        //  ], 400);
        // }   
        // }else{
        //  return response()->json([
        //      'status' => false,
        //      'message' => 'Vaccation hours are not assign to you, please contact admin'
        //  ], 400);
        // }
    }

    //Function for all vaccations
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
        //Get user id
        $user_id = $user->id;
        //Get UserVaccatioStatusn data
        $data = UserVaccatioStatusn::where('user_id', $user_id)->get();
        //Response
        if ($data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No vacation records found',
                'data' => []
            ]);
        }

        $result = [];

        foreach ($data as $datas) {
            $vacc_frm = date('M d, Y', strtotime(implode("-", explode('_', $datas->vacc_start))));
            $vacc_end = date('M d, Y', strtotime(implode("-", explode('_', $datas->vacc_end))));
            $vacc_rbu = date('M d, Y', strtotime(implode("-", explode('_', $datas->vacc_rbu))));
            $created  = date('M d, Y', strtotime($datas->created_at));
            $vacc_comments  = $datas->vacc_comments;

            $result[] = [
                'id' => $datas->id,
                'vacc_start' => $vacc_frm,
                'vacc_end' => $vacc_end,
                'report_by' => $vacc_rbu,
                'created_at' => $created,
                'vacc_comments' => $vacc_comments,
                'vacc_status' => ($datas->vacc_status == 1) ? 'Approved' : 'Pending'
            ];
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Vacation data fetched successfully',
            'data' => $result
        ]);
    }

    //Function for update vacation
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
        //Check record exists or not
		$data = UserVaccatioStatusn::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        //Response
		if (!$data) {
			return response()->json([
				'status' => false,
				'message' => 'Record not found'
			], 404);
		}
        //Validation
        $rules = [
            'vacc_frm'     =>  'required',
        ];
        $customMessages = [
            'vacc_frm'     =>  'From not selected'
        ];

        $validator = \Validator::make($request->all(), $rules, $customMessages);
        //Response
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        //Get auth id
        $user_id = $user->id;

        $vacc_frm = explode('-', $request->vacc_frm);
        $vacc_frm = implode("_", $vacc_frm);

        $vacc_to = explode('-', $request->vacc_to);
        $vacc_to = implode("_", $vacc_to);

        $report_by = explode('-', $request->report_by);
        $report_by = implode("_", $report_by);

        $data_vacc = UserVaccation::where('user_id','=', $user_id)->orderBy('created_at', 'DESC')->first();

        $date1 = new DateTime(date('m/d/y', strtotime($request->vacc_frm)));
        $date2 = new DateTime(date('m/d/y', strtotime($request->vacc_to)));

        $diff = $date2->diff($date1);

        $days = $diff->days;
        $hours = $diff->h;
        $hours = $hours + ($diff->days*24);
        $hours = floatval(8*$days);

        //if(isset($data_vacc)){

            // $used_hours = $data_vacc->vacc_vc;
            // $avail_hours = $data_vacc->vacc_sl;
            // $hours_requested = $hours;
            // $check_hours = $avail_hours - $hours_requested;
            // if($check_hours >= 0){

                $form_data = array(
                    'user_id' => $user_id, 
                    'vacc_start' => $vacc_frm,
                    'vacc_end' => $vacc_to,
                    'vacc_comments' => $request->comments,
                    'vacc_top'  => $request->time_policy,
                    'vacc_rbu'  => $report_by,
                );

                $ts_update = UserVaccatioStatusn::whereId($id)->update($form_data);
                

                if($ts_update){
                    return response()->json([
                        'status' => true,
                        'message' => 'Vaccation Updated Successfully!!',
                        'data' => $form_data
                    ]);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Error while updating Vaccation!!'
                    ], 500);
                }

                // $used_hours = $used_hours + $hours_requested;
                // $avail_hours = $avail_hours - $hours_requested;
                // $form_data = array(
                // 'vacc_sl' => $avail_hours,
                // 'vacc_vc' => $used_hours,
                // );
                // $user_update = UserVaccation::where('user_id','=', $user_id)->update($form_data);

            // }else{
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Vaccation hours are not left or you have used your all hours.'
            //     ]);
            // }

        // }else{
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Vaccation hours are not assign to you, please contact admin'
        //     ]);
        // }
    }

    //Function for delete vaccation
    public function destroy($id) {
        //Check auth
        $user = request()->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Check record
        $vaccation = UserVaccatioStatusn::find($id);
        //Record not found
        if (!$vaccation) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }
        //Delete record
        $vaccation->delete();
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Vacation deleted successfully'
        ]);
    }
}


