<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
   public function handle(Request $request, Closure $next)
{
    // Check if user is logged in via the standard 'web' guard
    if (auth()->guard('web')->check() && auth()->guard('web')->user()->email === 'joelletchoffo92@gmail.com') {
        return $next($request);
    }

    return redirect('/login')->with('error', 'You must be logged in as an administrator.');
}


}
