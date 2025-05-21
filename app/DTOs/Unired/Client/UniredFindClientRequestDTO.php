<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredFindClientRequestDTO
 *
 * @property string $loan_id
 */
class UniredFindClientRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $loan_id,
    ) {}
}
