<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanPaymentScheduleDTO extends Data
{
    public function __construct(
        public ?array $data,
        public ?IcanPaymentScheduleTotalDTO $total,
    ) {}
}
