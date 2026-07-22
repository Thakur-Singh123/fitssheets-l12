<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\LoginLogouttime;
use App\Mail\ResetPasswordMail;
use Laravel\Passport\Token;

class LoginController extends Controller
{
    //Function for login
    public function login(Request $request) {
        //Validate input fields
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);
        //Response
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()
            ], 422);
        }

        //Check Attempt login
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        //Get user detail
        $user   = Auth::user();
        $role   = $user->role;
        $status = $user->status;

        //Check if inactive user
        if ($status != "1" && $role != "admin") {
            Auth::logout();
            return response()->json([
                'status' => false,
                'message' => 'Your account is not activated, contact admin'
            ], 403);
        }

        //Update login info
        $user->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);

        LoginLogouttime::create([
            'users_id' => $user->id,
            'last_login_at' => Carbon::now()->toDateTimeString(),
        ]);

        //Delete token
        $user->tokens()->delete();

        //Generate token (Passport simple)
        $token = $user->createToken('LoginToken')->accessToken;

        //Response
        return response()->json([
            'status'     => true,
            'message'    => 'Login successful',
            'token'      => $token,
            'data'    => [
                'name' => $user->name,
                'first_name'  => $user->first_name,
                'last_name'  => $user->last_name,
                'emp_id'  => $user->emp_id,
                'phone_no'  => $user->phone_no,
                'role'  => $user->role,
                'dept'  => $user->dept,
                'companies_id'  => $user->companies_id,
                'status'  => $user->status,
            ]
        ]);
    }

    //Function for logout
    public function logout(Request $request) {
        //Get auth 
        $user = auth('api')->user();
        //Check user already exists 
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Already logged out, please login first'
            ], 401);
        }
        //Revoke token
        $user->token()->revoke();
        //Response
        return response()->json([
            'status' => true,
            'message' => 'Logout successful'
        ]);
    }

    //Function for forgot password
    public function forgot(Request $req) {
       //Validate fields
        $validator = Validator::make($req->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $email = $req->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email not found. Please enter correct email.'
            ], 400);
        }

        $otp = rand(100000, 999999);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            ['token' => $otp, 'created_at' => now()]
        );

        Mail::to($email)->send(new ResetPasswordMail($otp));

        return response()->json([
            'status' => true,
            'message' => "We’ve sent a verification code to your email. Enter the code to reset your password."
        ]);
    }

    //Function for reset password
    public function reset(Request $req) {

        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $email = $req->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email not found. Please enter correct email.'
            ], 400);
        }

        $check = DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $req->otp)
            ->first();

        if (!$check) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP. Please enter the correct code.'
            ], 400);
        }

        $otpTime = \Carbon\Carbon::parse($check->created_at);

        if ($otpTime->diffInMinutes(now()) >= 5) {
            return response()->json([
                'status' => false,
                'message' => 'OTP has expired. Please request a new one.'
            ], 400);
        }

        $user->password = Hash::make($req->password);
        $user->save();

        DB::table('password_resets')->where('email', $email)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Password reset successfully.'
        ], 200);
    }
}