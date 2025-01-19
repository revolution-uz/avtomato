<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredToggleClientAutoRequestDTO
 *
 * @property string $loan_id
 * @property bool $auto
 */
class UniredToggleClientAutoRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $loan_id,
        public bool $auto,
    ) {}
}
