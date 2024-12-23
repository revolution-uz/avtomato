<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredAddClientPhonesRequestDTO
 *
 * @property string $pinfl
 * @property array $phones
 */
class UniredAddClientPhonesRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
        public array $phones,
    ) {}
}
