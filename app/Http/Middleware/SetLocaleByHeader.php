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
        $defaultLocale = config('app.locale');
        $locale = $request->header('language') ?? config('app.locale');

        if (! in_array($locale, config('locales.supported'))) {
            $locale = $defaultLocale;
        }
        app()->setLocale($locale);

        return $next($request);
    }
}
