<?php

namespace App\DTOs\Unired\Contract;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredUpdateContractDataRequestDTO
 *
 * @property bool $auto
 * @property string $ext
 * @property float $debt
 */
class UniredUpdateContractDataRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public bool $auto,
        public string $ext,
        public float $debt,
    ) {}
}
