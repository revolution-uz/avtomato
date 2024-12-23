<?php

namespace App\DTOs\Unired\Contract;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredCreateContractRequestDTO
 *
 * @property string $pinfl
 * @property string $loan_id
 * @property string $ext_id
 * @property int $merchant_id
 * @property float $debt
 */
class UniredCreateContractRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
        public string $loan_id,
        public string $ext_id,
        public string $merchant_id,
        public float $debt,
    ) {}
}
