<?php

namespace App\Http\Middleware;

use App\Enums\FeatureEnum;
use App\Models\Company\CompanySettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthLoyaltProgramSync
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('X-Access-Token') !== $this->generateTransmitAccessToken()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }

    private function generateTransmitAccessToken(): string
    {
        $setting = CompanySettings::query()->where('name', FeatureEnum::LOYALTY_PROGRAM->name . '-sync-token')->firstOrFail();

        return $setting->value;
    }
}
