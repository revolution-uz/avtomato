<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanMetaDTO extends Data
{
    public function __construct(
        public ?int $current_page,
        public ?int $from,
        public ?int $last_page,
        // links
        public ?string $path,
        public ?int $per_page,
        public ?int $to,
        public ?int $total,
    ) {}
}
