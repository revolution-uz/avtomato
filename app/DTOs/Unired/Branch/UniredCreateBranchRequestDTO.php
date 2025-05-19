<?php

namespace App\DTOs\Unired\Branch;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredCreateBranchRequestDTO
 *
 * @property string $name
 * @property string $merchant_uzcard
 * @property string $terminal_uzcard
 * @property string $merchant_humo
 * @property string $terminal_humo
 * @property string $phone
 */
class UniredCreateBranchRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $name,
        public string $merchant_uzcard,
        public string $terminal_uzcard,
        public string $merchant_humo,
        public string $terminal_humo,
        public string $phone,
    ) {}
}
