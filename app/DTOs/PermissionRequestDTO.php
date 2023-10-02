<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class PermissionRequestDTO extends Data
{
    public function __construct(
        public ?int $branch_id,
        public array $permission_ids,
    ) {
    }
}
