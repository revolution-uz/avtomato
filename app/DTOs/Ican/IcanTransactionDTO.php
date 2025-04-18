<?php

namespace App\DTOs\Ican;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class IcanTransactionDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $credit_id,
        public ?int $company_id,
        public ?int $employee_id,
        public ?string $service_type,
        public ?IcanServiceDataDTO $service_data,
        public ?float $amount,
        public ?string $payment_type,
        public ?string $status,
        public ?int $cancel_user_id,
        public ?string $cancel_reason,
        public ?string $created_at,
        public ?int $wasyt_payment_id,
        public ?string $money_transfer_status,
        #[DataCollectionOf(IcanPaymentDTO::class)]
        public ?Collection $payments,
        public ?IcanCreditDTO $credit,
    ) {}
}
