<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use App\TimeSheet;
use App\User;
use App\Company;
use App\House;
use Carbon\Carbon;
use Image;
use DateTime;
use DatePeriod;
use DateInterval;
use App\LoginLogouttime;
use App\UserManager;

class ProfileController extends Controller
{
    //Function for update
    public function profile_update(Request $request) {
        try {
            //Get auth user
            $user = $request->user();
            //Response
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access',
                    'data' => null
                ], 401);
            }

            //Validate input fields
            $validator = Validator::make($request->all(), [
                'fname' => 'required|string',
                'lname' => 'nullable|string'
            ]);
            
            //Response
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            //Prepare name 
            $name = trim($request->fname . ' ' . ($request->lname ?? ''));

            //Update user
            $user->update([
                'name' => $name,
                'first_name' => $request->fname,
                'last_name' => $request->lname,
            ]);

            //Response
            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                ]
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong, Please try again',
            ], 500);
        }
    }

    //Function for reset password
    public function change_password(Request $request)
{
    // validation
    $validator = Validator::make($request->all(), [
        'current_password'     => 'required',
        'new_password'         => 'required|min:6',
        'new_confirm_password' => 'required|same:new_password',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ], 422);
    }

    // auth user
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized access'
        ], 401);
    }

    // check current password
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Current password does not match'
        ], 400);
    }

    // update password (same logic)
    $user->password = bcrypt($request->new_password);
    $user->pass     = $request->new_password; 
    $user->save();

    return response()->json([
        'status' => true,
        'message' => 'Your password is updated successfully'
    ], 200);
}
}
