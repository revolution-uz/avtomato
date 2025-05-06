<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanUserDocumentDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public ?string $type,
        public ?IcanUserDocumentDataDTO $data,
        public ?bool $is_valid,
        public ?string $created_at,
        public ?string $updated_at
    ) {}
}
