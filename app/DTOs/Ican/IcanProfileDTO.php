<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanProfileDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $type,
        public ?int $role_id,
        public ?int $company_id,
        public ?string $username,
        public ?string $phone,
        public ?string $created_at,
        public ?string $updated_at,
        public ?bool $is_confirmed,
        public ?string $partner_id,
        public ?string $sipuni_sip_number,
        public ?string $pinfl,
        public ?bool $is_deleted,
    ) {}
}
