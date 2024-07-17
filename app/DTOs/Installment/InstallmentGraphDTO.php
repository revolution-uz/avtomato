<?php

namespace App\DTOs\Installment;

use App\Enums\Company\InstallmentGraphStatusEnum;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class InstallmentGraphDTO extends Data
{
    public function __construct(
        public int $id,
        public float $amount,
        public float $paid_amount,
        public int $overdue_days,
        public Carbon $starts_in,
        public InstallmentGraphStatusEnum $status,
    ) {}
}
