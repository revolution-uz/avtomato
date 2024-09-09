<?php

namespace App\DTOs\Unired;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredErrorResponseDTO
 *
 * @property string $message
 */
class UniredErrorResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public string $message,
    ) {}
}
