<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanPersonMainDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $credit_id,
        public ?int $user_document_id,
        public ?int $district_id,
        public ?string $passport_address,
        public ?string $live_address,
        public ?string $mfy_address,
        public ?string $workplace,
        public ?string $position,
        public ?array $phones,
        public ?string $image,
        public ?bool $is_guarantor,
        public ?string $created_at,
        public ?string $updated_at,
        public ?bool $is_deleted,
        public ?IcanUserDocumentDTO $user_document,
    ) {}
}
