<?php

namespace App\DTOs\Unired\Branch;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Carbon\Carbon;

/**
 * Class UniredBranchResponseDTO
 *
 * @property int $id
 * @property string $name
 * @property string $merchant_uzcard
 * @property string $terminal_uzcard
 * @property string $merchant_humo
 * @property string $terminal_humo
 * @property string $phone
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UniredBranchResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $merchant_uzcard,
        public string $terminal_uzcard,
        public string $merchant_humo,
        public string $terminal_humo,
        public string $phone,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}
}
