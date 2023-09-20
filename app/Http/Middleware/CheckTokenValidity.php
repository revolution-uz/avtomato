<?php

namespace App\Http\Middleware;

use App\Http\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTokenValidity
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::hasUser()) {
            // Check if the user's token is still valid
            if (Auth::user()->token()->expires_at < now()) {
                // Token has expired; you can log the user out or take appropriate action.
                Auth::user()->token()->delete(); // Delete the token
                return ApiResponse::error('Token has expired');
            }
        }

        return $next($request);
    }
}
