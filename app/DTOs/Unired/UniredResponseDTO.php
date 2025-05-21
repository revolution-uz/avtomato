<?php

namespace App\DTOs\Unired;

use App\DTOs\Unired\Base\BaseUniredDTO;
use App\Exceptions\UniredException;

/**
 * Class UniredResponseDTO
 *
 * @property bool|null $status
 * @property UniredErrorResponseDTO|null $error
 * @property array|null $result
 */
class UniredResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public ?bool $status,
        public ?UniredErrorResponseDTO $error,
        public ?array $result,
    ) {}

    public function isSuccess(): bool
    {
        if (is_null($this->status)) {
            throw new UniredException('Invalid response');
        } else {
            return $this->status;
        }
    }
}
