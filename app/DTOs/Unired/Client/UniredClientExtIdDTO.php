<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredClientExtIdDTO
 *
 * @property string $ext_id
 */
class UniredClientExtIdDTO extends BaseUniredDTO
{
    public function __construct(
        public string $ext_id,
        // public string $loan_id, // required without ext_id
    ) {}
}
