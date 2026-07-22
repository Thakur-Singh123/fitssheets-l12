<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
   public function handle($request, Closure $next)
{
    if (!$request->bearerToken()) {
        return response()->json([
            'status' => false,
            'message' => 'Already logged out. Please login first'
        ], 401);
    }

    return $next($request);
}
}
