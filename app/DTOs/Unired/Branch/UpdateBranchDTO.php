<?php

namespace App\DTOs\Unired\Branch;

use App\DTOs\Scoring\Base\BaseScoringRequest;

/**
 * Class CardCreateRequestDTO
 *
 * @property int $id
 * @property string $number
 * @property string $merchant_uzcard
 * @property string $terminal_uzcard
 * @property string $merchant_humo
 * @property string $terminal_humo
 * @property string $phone
 */
class UpdateBranchDTO extends BaseScoringRequest
{
    public function __construct(
        public int $id,
        public string $number,
        public string $merchant_uzcard,
        public string $terminal_uzcard,
        public string $merchant_humo,
        public string $terminal_humo,
        public string $phone,
    ) {}
}
