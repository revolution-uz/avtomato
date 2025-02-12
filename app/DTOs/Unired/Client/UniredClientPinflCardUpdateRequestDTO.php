<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredClientPinflCardUpdateRequestDTO
 *
 * @property string $pinfl
 */
class UniredClientPinflCardUpdateRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
    ) {}
}
