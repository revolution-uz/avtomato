<?php

namespace App\DTOs\Unired\Branch;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredToggleBranchAutoRequestDTO
 *
 * @property int $filial_id
 * @property bool $auto
 */
class UniredToggleBranchAutoRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public int $filial_id,
        public bool $auto,
    ) {}
}
