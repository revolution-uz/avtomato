<?php

namespace App\DTOs\Unired\Branch;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredFindBranchRequestDTO
 *
 * @property string $name
 */
class UniredFindBranchRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $name,
    ) {}
}
