<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    public function handle($request, Closure $next, $role) {
        //Get auth login
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Not authenticated, please login first'
            ], 401);
        }
        //Check auth not found
        if ($user->role !== $role) {
            return response()->json([
                'status' => false,
                'message' => 'You are logged in as ' . $user->role . ', please login as ' . $role . ' first'
            ], 403);
        }
        return $next($request);
    }
}