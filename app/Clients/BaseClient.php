<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Log;

abstract class BaseClient
{
    protected string $channel = 'http-client';

    abstract public function getClientCode(): ClientCodeEnum;

    protected function logRequest(HttpRequestLogDTO $request, string $logType = 'info'): void
    {
        Log::channel($this->channel)->$logType(get_called_class(), $request->toArray());
    }
}
