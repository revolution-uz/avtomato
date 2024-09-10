<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredFindClientsRequestDTO
 *
 * @property array $loan_ids
 */
class UniredFindClientsRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public array $loan_ids,
    ) {}
}
