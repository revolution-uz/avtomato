<?php

namespace App\DTOs\Firebase;

use Spatie\LaravelData\Data;

class FirebaseDataDTO extends Data
{
    public function __construct(
        public ?int $notifId = null,
        public ?string $title = null,
        public ?string $body = null,
        public ?string $type = null,
        public ?string $image = null,
        public ?string $click_action = null,
    ) {}
}
