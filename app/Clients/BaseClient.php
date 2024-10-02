<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

abstract class BaseClient
{
    protected string $channel = 'http-client';

    abstract public function getClientCode(): ClientCodeEnum;

    protected function logRequest(HttpRequestLogDTO $request, string $logType = 'info'): void
    {
        $path = storage_path('logs/' . $this->channel . '.log');
        Config::set('logging.channels.' . $this->channel . '.path', $path);
        Log::channel($this->channel)->$logType(get_called_class(), $request->toArray());
    }
}
