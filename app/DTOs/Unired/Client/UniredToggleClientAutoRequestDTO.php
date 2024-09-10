<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredToggleBranchAutoRequestDTO
 *
 * @property int $loan_id
 * @property bool $auto
 */
class UniredToggleClientAutoRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public int $loan_id,
        public bool $auto,
    ) {}
}
