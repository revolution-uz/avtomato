<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Log;

abstract class BaseClient
{
    abstract public function getClientCode(): ClientCodeEnum;

    protected function logRequest(HttpRequestLogDTO $request): void
    {
        Log::channel('http-client')->info(get_called_class(), $request->toArray());
    }
}
