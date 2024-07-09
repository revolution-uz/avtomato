<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;

class OptionalAuthenticate extends Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (! $request->bearerToken()) {
            return $next($request);
        }

        return parent::handle($request, $next, ...$guards);
    }
}
