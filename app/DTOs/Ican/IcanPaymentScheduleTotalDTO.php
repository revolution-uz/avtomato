<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanPaymentScheduleTotalDTO extends Data
{
    public function __construct(
        public ?float $total_debt,
        public ?float $total_must_be_paid,
        public ?float $total_paid,
    ) {}
}
