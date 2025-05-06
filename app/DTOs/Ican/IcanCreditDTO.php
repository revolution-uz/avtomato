<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanCreditDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $creator_id,
        public ?int $company_id,
        public ?int $employee_id,
        public ?int $responsible_id,
        public ?int $tariff_id,
        public ?int $client_id,
        public ?int $number,
        public ?int $amount,
        public ?int $initial_payment,
        public ?int $main_debt,
        public ?int $percent_debt,
        public ?int $initial_payment_debt,
        public ?float $overdue_debt,
        public ?int $percent,
        public ?int $grace_period,
        public ?int $period,
        public ?int $payment_day,
        public ?string $start_date,
        public ?string $finish_date,
        public ?string $katm_sir,
        public ?string $products_note,
        public ?string $comment,
        public ?string $status,
        public ?string $cancel_reason,
        public ?string $client_rating,
        public ?array $phones_for_sms,
        public ?bool $is_my_uzcard_auto_payment_turned_on,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $katm_registration_claim_id,
        public ?string $activated_at,
        public ?string $canceled_at,
        public ?string $client_type,
        public ?bool $is_vat_enabled,
        public ?float $amount_without_vat,
        public ?bool $is_imported,
        public ?string $period_type,
        public ?bool $is_aloqa_bank_auto_payment_turned_on,
        public ?int $total_debt,
        public ?bool $is_deleted,
        public ?IcanPersonMainDTO $person_main,
        public ?IcanTariffDTO $tariff,
    ) {}
}
