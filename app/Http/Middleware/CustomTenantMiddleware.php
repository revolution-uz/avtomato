<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            Domain::query()->where('domain', $request->getHost())->doesntExist() and
            ! in_array($request->getHost(), config('tenancy.central_domains'))
        ) {
            abort(404, 'Domain not found');
        }

        return $next($request);
    }
}
