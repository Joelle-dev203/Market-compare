<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedAs
{
    public function handle($request, Closure $next, $guard)
    {
        if (Auth::guard($guard)->check()) {
            return $next($request);
        }

        // If not logged in as the required guard, redirect to login
        return redirect()->route('login')->with('error', 'Please login to access this area.');
    }
}