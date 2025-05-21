<?php

namespace App\DTOs\Firebase;

use Spatie\LaravelData\Data;

class FirebaseDTO extends Data
{
    public function __construct(
        public FirebaseDataDTO $data,
        public FirebaseNotificationDTO $notification,
        public ?string $topic = null,
    ) {}
}
