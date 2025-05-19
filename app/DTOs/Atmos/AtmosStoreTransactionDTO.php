<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class AtmosStoreTransactionDTO extends BaseScoringRequest
{
    public function __construct(
        public ?int $success_trans_id,
        public ?int $trans_id,
        public AtmosStoreDTO $store,
        public ?string $terminal_id,
        public ?string $account,
        public ?int $amount,
        public ?bool $confirmed,
        public ?int $prepay_time,
        public ?int $confirm_time,
        public AtmosLabelDTO $label,
        public ?string $details,
        public $commission_value = null,
        public $commission_type = null,
        public $total = null,
        public $card_id = null,
        public $status_code = null,
        public $status_message = null,
    ) {}
}
