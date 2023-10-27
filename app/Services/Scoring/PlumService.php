<?php

namespace App\Services\Scoring;

use App\Clients\PlumClient;

class PlumService
{
    public function __construct(
        private readonly PlumClient $plumClient
    ) {
    }
}
