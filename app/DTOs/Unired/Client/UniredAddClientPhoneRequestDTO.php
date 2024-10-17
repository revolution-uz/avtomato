<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredAddClientPhoneRequestDTO
 *
 * @property string $pinfl
 * @property string $phone
 */
class UniredAddClientPhoneRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
        public string $phone,
    ) {}
}
