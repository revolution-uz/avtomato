<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanTransactionDTO extends Data
{
    public function __construct(
        public ?string $id,
        public ?string $credit_id,
        public ?string $company_id,
        public ?string $employee_id,
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
        public ?IcanCreditDTO $credit,
        public ?IcanEmployeeDTO $employee,
        public ?IcanCompanyDTO $company,
        // payments
    ) {}
}
