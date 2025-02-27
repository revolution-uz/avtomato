<?php

namespace App\Http\Middleware;

use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class LoyaltyProgramMiddleware
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
        if (! tenancy()->initialized || ! tenant()->isActive()) {
            abort(403, __('api.inactive_company'));
        }

        if (! Feature::isFeatureActive(FeatureEnum::LOYALTY_PROGRAM)) {
            abort(403, __('api.inactive_company'));
        }

        return $next($request);
    }
}
