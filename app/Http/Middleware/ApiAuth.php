<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ApiAuth
{
    public function handle($request, Closure $next) {
        //Passport auth check
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Not authenticated, please login first'
            ], 401);
        }

        return $next($request);
    }
}