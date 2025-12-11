<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Check if the user is authenticated
        if (! $request->user()) {
            return redirect('/login'); // Redirect unauthenticated users
        }

        // 2. Check if the authenticated user's role is in the allowed roles list
        if (! in_array($request->user()->role, $roles)) {
            // User does not have the required role, redirect them or abort
            return redirect('/login')->with('error', 'Unauthorized access.'); 
            // OR abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
