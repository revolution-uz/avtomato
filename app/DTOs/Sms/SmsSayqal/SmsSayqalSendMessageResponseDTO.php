<?php

namespace App\DTOs\Sms\SmsSayqal;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class SmsSayqalSendMessageResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public int $transactionid,
        public int $smsid,
        public int $parts
    ) {}
}
