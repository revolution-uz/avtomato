<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredContractLoanIdDTO
 *
 * @property string $pinfl
 * @property string $loan_id
 */
class UniredContractLoanIdDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
        public string $loan_id,
    ) {}
}
