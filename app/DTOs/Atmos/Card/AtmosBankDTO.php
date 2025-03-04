<?php

namespace App\DTOs\Atmos\Card;

use Spatie\LaravelData\Data;

class AtmosBankDTO extends Data
{
    public function __construct(
        public int $id,
        public string $code,
        public string $mfo,
        public string $name,
        public string $logo,
        public int $sum_max_onetime,
        public int $sum_max_month,
        public float $intrabank_pcnt,
        public float $interbank_pcnt,
        public bool $p2p_enable,
        public ?string $visa_code,
    ) {}
}
