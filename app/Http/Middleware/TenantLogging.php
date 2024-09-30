<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TenantLogging
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (tenant()) {
            $tenant = tenant()->getTenantKey();
            $logPath = storage_path('logs/company_' . $tenant . '.log');
            Config::set('logging.channels.tenant.path', $logPath);
            $response = $next($request);
            $jsonDecodedResponse = json_decode($response->getContent(), true);
            $shortUrl = substr($request->url(), strpos($request->url(), '/graphql'));
            $logData = [
                'method' => $request->method(),
                'url' => $request->url(),
                'request' => $request->all(),
                'status' => $response->getStatusCode(),
                'response' => isset($jsonDecodedResponse['errors']) ? $jsonDecodedResponse['errors'][0]['message'] : $jsonDecodedResponse,
            ];
            $logType = ($response->getStatusCode() === Response::HTTP_OK && ! isset($jsonDecodedResponse['errors'])) ? 'info' : 'error';

            Log::channel('tenant')->$logType('company-' . $tenant . $shortUrl, $logData);

            return $response;
        }

        return $next($request);
    }
}
