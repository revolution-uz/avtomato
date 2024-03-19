<?php

namespace App\DTOs\Sms;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SendMessageDTO',
    title: 'Send message request',
    properties: [
        new OA\Property(property: 'phone', type: 'string', example: '998901234567'),
        new OA\Property(property: 'message', type: 'string', example: 'Xabar yuborishda + dan foydalanmang'),
    ],
)]
class SendMessageDTO extends BaseScoringRequest
{
    public function __construct(
        public int $phone,
        public string $message,
    ) {
    }
}
