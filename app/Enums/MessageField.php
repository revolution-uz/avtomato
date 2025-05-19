<?php

namespace App\Enums;

use Spatie\LaravelData\Data;

class MessageField extends Data
{
    public function __construct(
        public string $message_en,
        public string $message_uz,
        public string $message_ru,
    ) {}
}
