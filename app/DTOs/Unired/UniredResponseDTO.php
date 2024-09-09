<?php

namespace App\DTOs\Unired;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredResponseDTO
 *
 * @property bool $status
 * @property UniredErrorResponseDTO $error
 * @property array $result
 */
class UniredResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public ?bool $status,
        public ?UniredErrorResponseDTO $error,
        public ?array $result,
    ) {}
}
