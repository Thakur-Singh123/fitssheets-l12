<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
			if (Auth::user()->role == 'admin') {
				return redirect('dashboard');
			}
			elseif (Auth::user()->role == 'user' && Auth::user()->status == 1) {
				return redirect('user-dashboard');
			} 
			elseif (Auth::user()->role == 'casemanager' && Auth::user()->status == 1) {
				return redirect('casemanager-dashboard');
			} 
			elseif (Auth::user()->role == 'supervisor' && Auth::user()->status == 1) {
				return redirect('supervisor-dashboard');
			} 
			else
			{
				return redirect('/');
			}
		}
		return $next($request);
    }
}
