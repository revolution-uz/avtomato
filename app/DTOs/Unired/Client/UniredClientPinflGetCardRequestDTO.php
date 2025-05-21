<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredClientPinflGetCardRequestDTO
 *
 * @property string $pinfl
 */
class UniredClientPinflGetCardRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
    ) {}
}
