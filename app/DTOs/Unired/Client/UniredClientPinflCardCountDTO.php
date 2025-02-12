<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredClientPinflCardCountDTO
 *
 * @property int $uzcard
 * @property int $humo
 */
class UniredClientPinflCardCountDTO extends BaseUniredDTO
{
    public function __construct(
        public int $uzcard,
        public int $humo,
    ) {}
}
