<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredCreateClientResponseErrorDataDTO
 *
 * @property int $loan_id
 */
class UniredCreateClientResponseErrorDataDTO extends BaseUniredDTO
{
    public function __construct(
        public string $loan_id,
    ) {}
}
