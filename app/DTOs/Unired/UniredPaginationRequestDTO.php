<?php

namespace App\DTOs\Unired;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredPaginationRequestDTO
 *
 * @property int $pageSize
 * @property int $page
 * @property int|null $filial_id
 */
class UniredPaginationRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public int $pageSize,
        public int $page,
        public ?int $filial_id,
    ) {}
}
