<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredPinflCardsResponseDTO
 *
 * @property UniredCardsResponseDTO|null $uzcard
 * @property UniredCardsResponseDTO|null $humo
 */
class UniredPinflCardsResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public ?UniredCardsResponseDTO $uzcard,
        public ?UniredCardsResponseDTO $humo,
    ) {}
}
