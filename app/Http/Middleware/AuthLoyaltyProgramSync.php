<?php

namespace App\Http\Middleware;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\CompanySettings;
use App\Models\Company\Feature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthLoyaltyProgramSync
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            (! tenancy()->initialized || ! tenant()->isActive()) &&
            ! Feature::isFeatureActive(FeatureEnum::LOYALTY_PROGRAM) &&
            $request->header('X-Access-Token') !== $this->generateTransmitAccessToken()
        ) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }

    private function generateTransmitAccessToken(): string
    {
        $setting = CompanySettings::query()->where('name', CompanySettingsEnum::LOYALTY_PROGRAM_SYNC_TOKEN)->firstOrFail();

        return $setting->value;
    }
}
