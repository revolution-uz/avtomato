<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredCreateClientRequestDTO
 *
 * @property string $pinfl
 * @property string $passport
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 */
class UniredCreateClientRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
        public string $passport,
        public string $first_name,
        public string $last_name,
        public string $middle_name,
    ) {}
}
