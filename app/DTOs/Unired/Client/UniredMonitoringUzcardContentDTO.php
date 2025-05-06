<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class UniredMonitoringUzcardContentDTO extends BaseUniredDTO
{
    public function __construct(
        public ?int $utrnno,
        public string $transType,
        public ?string $hpan,
        public ?int $utime,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Ymd')]
        public Carbon $udate,
        public ?string $bankDate,
        public ?int $reqamt,
        public ?int $resp,
        public ?bool $reversal,
        public ?int $orgdev,
        public ?string $merchant,
        public ?string $terminal,
        public ?string $merchantName,
        public ?string $street,
        public ?string $city,
        public ?bool $isCredit,
        public ?bool $credit,
        public ?int $mcc,
        public ?int $currency,
        public ?int $actamt,
        public ?int $acctbal,
        public ?int $conamt,
    ) {}
}
