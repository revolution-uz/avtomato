<?php

namespace App\DTOs\Firebase;

use Spatie\LaravelData\Data;

class FirebaseNotificationDTO extends Data
{
    public function __construct(
        public string $title,
        public string $body,
    ) {}
}
