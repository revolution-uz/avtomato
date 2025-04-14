<?php

namespace App\DTOs\Ican;

use Spatie\LaravelData\Data;

class IcanCompanyDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $katm_access_id,
        public ?int $parent_id,
        public ?string $name,
        public ?string $company_name,
        public ?string $logo,
        public ?string $inn,
        public ?string $mfo,
        public ?string $address,
        public ?string $comment,
        public ?string $telegram_phone,
        public ?string $director_full_name,
        public ?string $bank,
        public ?string $payment_account,
        public ?string $agreement_number,
        public ?int $atmos_terminal_id,
        public ?int $my_uzcard_auto_payment_credit_overdue_days,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $field_of_activity,
        public ?int $ican_tariff_id,
        public ?int $ican_balance_limit_id,
        public ?int $partner_id,
        public ?int $sms_template_id,
        public ?string $credit_currency,
        public ?int $district_id,
        public ?string $vat_percent,
        public ?string $agreement_html,
        public ?bool $is_using_new_my_uzcard_auto_payment_by_pinfl,
        public ?int $emit_repayment_branch_id,
        public ?int $aloqa_bank_branch_id,
        public ?int $uniaccess_branch_id,
        public ?string $is_deleted,
        public ?IcanDirectorDTO $director,
    ) {}
}
