<?php

namespace App\DTOs\Scoring\Katm\PensionInfo;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmPensionDataDTO extends BaseScoringResponse
{
    public function __construct(
        public string $namelatin,
        public string $address,
        public string $summa,
        public string $patronymlatin,
        public string $document,
        public string $apptype,
        public string $surnamelatin,
        public string $experience,
        public string $type,
        public string $datepay,
        public string $pinpp,
        public string $datepens,
    ) {}
}
