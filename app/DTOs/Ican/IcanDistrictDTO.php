<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanDistrictDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $region_id,
        public ?string $name,
        public ?int $iso_code,
        public ?string $katm_code,
        public ?int $pochta_area_id,
        public ?string $created_at,
        public ?string $updated_at,
        public ?bool $is_deleted,
        public ?IcanRegionDTO $region,
    ) {}
}
