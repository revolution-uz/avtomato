<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleByHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language') ?? config('app.locale');

        if (! in_array($locale, config('locales.supported'))) {
            $locale =  config('app.locale');
        }
        app()->setLocale($locale);

        return $next($request);
    }
}
