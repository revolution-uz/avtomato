<?php

namespace App\DTOs\Unired\System;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredSetTokenRequestDTO
 *
 * @property string $token
 */
class UniredSetTokenRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $token,
    ) {}
}
