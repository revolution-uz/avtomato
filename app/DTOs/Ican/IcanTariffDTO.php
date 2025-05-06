<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanTariffDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $company_id,
        public ?string $name,
        public ?string $type,
        public ?int $percent,
        public ?int $grace_period,
        public ?int $min_period,
        public ?int $max_period,
        public ?int $min_amount,
        public ?int $max_amount,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $period_type,
        public ?bool $is_deleted,
    ) {}
}
