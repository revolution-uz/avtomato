<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredUpdateClientDebtRequestDTO
 *
 * @property string $loan_id
 * @property int $remaining_amount
 */
class UniredUpdateClientDebitRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $loan_id,
        public int $remaining_amount,
    ) {}
}
