<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
         // Allow access to the login route
         if ($request->is('admin/login')) {
            return $next($request);
        }

        // Check if the user is authenticated and an admin
        if (!auth()->user() || !auth()->user()->is_admin) {
            return redirect('/');
        }
        
        return $next($request);
    }
}
