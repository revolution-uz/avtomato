<?php

namespace App\DTOs\Unired\Branch;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class ToggleBranchAutoRequestDTO
 *
 * @property int $filial_id
 * @property bool $auto
 */
class ToggleBranchAutoRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public int $filial_id,
        public bool $auto,
    ) {}
}
