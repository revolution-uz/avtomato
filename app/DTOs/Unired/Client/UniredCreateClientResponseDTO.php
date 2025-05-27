<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredCreateClientResponseDTO
 *
 * @property int $id
 */
class UniredCreateClientResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public int $id,
    ) {}
}
