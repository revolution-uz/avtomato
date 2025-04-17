<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthUnired
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization', '');
        $uniaccess_token = $this->getStaticToken();
        $berear_uniaccess_token = 'Bearer ' . $uniaccess_token;
        if ($token != $this->getStaticToken() && $token != $berear_uniaccess_token) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }

    private function getStaticToken(): string
    {
        return strval(config('clients.unired.callback-token', ''));
    }
}
