<?php

namespace App\Http\Controllers\Api\Supervisor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\TimeSheet;
use App\ListProblem;
use App\User;
use App\House;
use App\Company;
use DateTime;
use Carbon\Carbon;

class ListProblemController extends Controller
{
    //Function for all issues list
    public function index(Request $request) {
        //Get auth
        $user = $request->user();
        //Resposne
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get issues
        $data = ListProblem::with('companies')->orderBy('created_at', 'DESC')->get();

        $issue_arr = [];

        if ($data->count() > 0) {
            //Group by date
            $groupedData = $data->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

            foreach ($groupedData as $date => $issues) {

                $issue_list = [];

                foreach ($issues as $value) {
                    
                    $issue_list[] = [
                        'id'         => $value->id,
                        'name'       => $value->name,
                        'ssn' => $value->ssn != null ? substr($value->ssn, -4) : '',
                        'company_name' => isset($value->companies) ? $value->companies->company : '',
                        'issue'      => $value->issue,
                        'status'     => $value->status,
                        //'resolution' => $value->resolution_remarks,
                        //'created_at' => $value->created_at->format('Y-m-d'),
                    ];
                }
                $issue_arr[] = [
                    'date' => $date,
                    'issues' => $issue_list
                ];
            }
        }
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Issue list fetched successfully.',
            'data' => $issue_arr
        ], 200);
    }

    //Function for store issue
    public function store(Request $request) {
        //Get auth user
        $user = $request->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Validation
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'issue' => 'required',
        ]);
        //Validation response
       if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                 'message' => $validator->errors()->first()
            ], 422);
        }
        //Auth details
        $user_id = $user->id;
        $name = $user->name;
        $ssn = $user->ssn_no;
        //Store data
        $form_data = [
            'companies_id' => $request->company_id,
            'ssn' => $ssn,
            'user_id' => $user_id,
            'name' => $name,
            'issue' => $request->issue,
            'resolution_remarks' => $request->resolution_remarks,
        ];
        //Create issue
        $ls_store = ListProblem::create($form_data);
        //Response
        if ($ls_store) {
            return response()->json([
                'status' => true,
                'message' => 'Issue added successfully.',
                'data' => $ls_store,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Error while adding issue.'
            ], 500);
        }
    }

    //Fucntion for checked issue
    public function issue_approve(Request $request, $id) {
        //Get auth
        $user = $request->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get record
        $data = ListProblem::find($id);
        //Response
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }
        //Check already approved or not
        if ($data->status == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Issue already checked, please first unchecked then checked again.'
            ], 400);
        }
        //Update status
        $data->status = 1;
        $data->save();
        //Response
        return response()->json([
            'status' => true,
            'message' => 'List point checked successfully.',
        ], 200);
    }

    //Function for unchecked issue
    public function decline(Request $request, $id) {
        //Get auth
        $user = $request->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 401);
        }
        //Get issue
        $data = ListProblem::find($id);
        //Response
        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }
        //Check already unchecked or not
        if ($data->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Issue already unchecked, please first checked then unchecked again.'
            ], 400);

        }
        //Update Status
        $data->status = 0;
        $data->save();
        //Response
        return response()->json([
            'status' => true,
            'message' => 'List point unchecked successfully.',
        ], 200);
    }
}
