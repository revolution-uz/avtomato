<?php

namespace App\DTOs\Unired\Contract;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredCreateContractRequestDTO
 *
 * @property string $loan_id
 * @property UniredUpdateContractDataRequestDTO $data
 */
class UniredUpdateContractRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $loan_id,
        public UniredUpdateContractDataRequestDTO $data,
    ) {}
}
