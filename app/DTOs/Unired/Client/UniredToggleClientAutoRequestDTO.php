<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredToggleClientAutoRequestDTO
 *
 * @property string $ext_id
 * @property bool $auto
 */
class UniredToggleClientAutoRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $ext_id,
        // public ?string $ext_id = null, // required without loan_id
        public bool $auto,
    ) {}
}
