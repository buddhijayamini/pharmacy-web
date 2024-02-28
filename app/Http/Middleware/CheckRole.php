<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Check if the user has any of the specified roles
            if (Auth::user()->hasAnyRole($roles)) {
                return $next($request);
            }
        }

        // If the user does not have any of the specified roles, return an unauthorized response
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
