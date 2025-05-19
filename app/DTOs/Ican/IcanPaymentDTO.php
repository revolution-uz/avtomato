<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanPaymentDTO extends Data
{
    public function __construct(
        public ?string $id,
        public ?bool $is_initial_payment,
        public ?float $main_debt,
        public ?int $main_paid,
        public ?int $percent_debt,
        public ?int $percent_paid,
        public ?float $total_debt,
        public ?float $total_paid,
        public ?int $credit_id,
        public ?int $transaction_id,
        public ?string $created_at,
    ) {}
}
