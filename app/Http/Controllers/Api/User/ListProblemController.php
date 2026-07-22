<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
    //Function for create issue
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
        //Get auth login id
        $user_id = $user->id;
        $name = $user->name;
        $ssn = $user->ssn_no;
        //rules
        $rules = [
            'company_id' => 'required',
            'issue' => 'required',
        ];
        //Show message
        $customMessages = [
            'company_id.required' => 'Please select company',
            'issue.required' => 'Please add issue',
        ];
        //Validate input fields
        $validator = \Validator::make($request->all(), $rules, $customMessages);
        //response
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        //Get user
        $user = User::where('id', '=', $user_id)->first();
        //Create data
        $form_data = array(
            'companies_id' => $request->company_id,
            'ssn' => $ssn,
            'user_id' => $user_id,
            'name' => $name,
            'issue' => $request->issue,
            'resolution_remarks' => $request->resolution_remarks
        );
       
        $ls_store = ListProblem::create($form_data);
        //Response
        if ($ls_store) {
            return response()->json([
                
                'status' => true,
                'message' => 'Issue Added Successfully!!',
                'data' => $ls_store
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Error while Adding Issue!!',
                'data' => null
            ], 500);
        }
    }

    //Function for all list issues 
    public function index(Request $request) {
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

        //Get issues
        $data = ListProblem::with('companies')
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'DESC')
            ->get();
        
        //Check if issue found or not
        if ($data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No Issue Found',
                'data' => []
            ]);
        }

        $issue_arr = [];

        foreach ($data as $value) {

            //date format
            $date_key = $value->created_at->format('y_m_d');
            $date_format = date('M d Y', strtotime(str_replace('_','-',$date_key)));

            $company_name = optional($value->companies)->company;

            $text = $value->name 
                . ' / ' . substr($value->ssn, -4) 
                . ' / ' . $company_name 
                . ' / ' . $value->issue;

            $issue_arr[$date_format][] = [
                'id' => $value->id,
                'text' => $text,
                'status' => $value->status,
            ];
        }
        
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Issues fetched successfully',
            'data' => $issue_arr
        ]);
    }
}
