<?php

namespace App\Http\Middleware;

use App\Models\Company\LoyaltyProgram\User;
use App\Models\Company\PersonalAccessToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class IsSoftDeletedLoyaltyProgramClientToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     *
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse|RedirectResponse
    {
        if ($request->headers->has('Authorization') && str_contains($request->header('Authorization'), 'Bearer')) {

            [$bearer, $token] = explode(' ', $request->header('Authorization'));
            [$id, $token] = explode('|', $token);
            if (PersonalAccessToken::query()->where('id', $id)->where('tokenable_type', User::class)->doesntExist()) {
                abort(401, 'Unauthorized');
            }
        }

        return $next($request);
    }
}
