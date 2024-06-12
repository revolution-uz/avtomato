<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $lang = $request->header('Accept-Language', config('app.locale'));

        if (! in_array($lang, config('app.supported_locales'), true)) {
            $lang = config('app.locale');
        }
        app()->setLocale($lang);

        return $next($request);
    }
}
