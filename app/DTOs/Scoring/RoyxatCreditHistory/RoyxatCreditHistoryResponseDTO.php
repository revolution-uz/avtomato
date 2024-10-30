<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class RoyxatCreditHistoryResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public int     $id,
        public string  $credit_id,
        public int     $amount,
        public int     $duration,
        public string  $name,
        public string  $passport,
        public ?int    $tin,
        public ?string $pinfl,
        public string  $credit_status,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.u\Z')]
        public Carbon  $contract_date,
        public int     $overdue_days,
        public int     $merchant_id,
        public string  $merchant_name,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.u\Z')]
        public Carbon  $created_at,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.u\Z')]
        public Carbon  $updated_at,
    )
    {
    }
}
