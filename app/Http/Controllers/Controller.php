<?php

namespace App\Http\Controllers;

use App\Helpers\HideSensitiveDataFromLogging;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Info(
 *    title="Avtomato company-app API",
 *    version="1.0.0",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function logging($request, $response, $channel, $type, $method)
    {
        Log::channel($channel)->info($type, [
            'header' => HideSensitiveDataFromLogging::handle($request->headers->all()),
            'client' => get_called_class(),
            'method' => $method,
            'params' => HideSensitiveDataFromLogging::handle($request),
            'response' => $response,
        ]);
    }
}
