<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class HttpRequestLogDTO extends Data
{
    public function __construct(
        public string $method,
        public string $url,
        public int $status,
        public array|string|null $response,
        public ?int $duration,
        public ?array $params = null,
        public ?array $headers = null,
        public ?array $body = null,
    ) {}
}
