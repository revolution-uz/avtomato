<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class CreateCreditRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $credit_id, //Order ID by tenancy
        public int    $amount, //in tiyin
        public int    $duration, // period
        public string $name, //FullName
        public string $passport,
        public ?int   $tin,
        public string $credit_status, //Статус кредита: (активный, закрытый, проблемный) Свойства: active, problem, closed
        public string $contract_date, //date (format:Y-m-d)
        public int    $overdue_days,
        public string $birth_date,
        public ?int   $pinfl,

    )
    {
    }
}
