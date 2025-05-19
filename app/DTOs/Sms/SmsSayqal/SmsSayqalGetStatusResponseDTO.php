<?php

namespace App\DTOs\Sms\SmsSayqal;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class SmsSayqalGetStatusResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public int $smsid,
        public int $status,
        public string $statusdate,
        public int $parts,
    ) {}
}
