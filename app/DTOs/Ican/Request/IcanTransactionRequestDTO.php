<?php

namespace App\DTOs\Ican\Request;

use Spatie\LaravelData\Data;

class IcanTransactionRequestDTO extends Data
{
    public function __construct(
        public int $credit_id,
        public int $amount,
        public int $employee_id,
        public string $payment_type = 'mib', // blacklist, card, card_click, cash, discount, mib, terminal
    ) {}
}
