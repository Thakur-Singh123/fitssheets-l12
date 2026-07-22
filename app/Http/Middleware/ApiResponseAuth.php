<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ApiResponseAuth
{
    public function handle($request, Closure $next) {
        //Get auth
        Auth::shouldUse('api'); 
        $user = Auth::guard('api')->user();
        //Response
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token, please login first'
            ], 401);
        }
        return $next($request);
    }
}