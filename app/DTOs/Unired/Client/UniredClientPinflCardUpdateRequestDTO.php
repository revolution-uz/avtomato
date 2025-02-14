<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredClientPinflCardUpdateRequestDTO
 *
 * @property array $pinfl
 */
class UniredClientPinflCardUpdateRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public array $pinfl,
    ) {}
}
