<?php

namespace App\Http\Middleware;

use App\Enums\FeatureEnum;
use App\Models\BaseFeature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthSayqal
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('X-Access-Token') !== $this->generateTransmitAccessToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }

    private function generateTransmitAccessToken(Request $request): string
    {
        $feature = BaseFeature::query()->where('name', FeatureEnum::SMS)->whereNotNull('original_data')->firstOrFail();
        $originalData = $feature->original_data;
        $statusDate = $request->get('statusdate');

        return md5("TransmitSMSStatus {$originalData['username']} {$originalData['secret']} {$statusDate}");
    }
}
