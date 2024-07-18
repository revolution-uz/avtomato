<?php

namespace App\DTOs\FeatureData;

use Spatie\LaravelData\Data;

class FeatureSmsData extends Data
{
    public function __construct(
        public string $provider,
        public ?string $username,
        public string $secret,
    ) {}
}
