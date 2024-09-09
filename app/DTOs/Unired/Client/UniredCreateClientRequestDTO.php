<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredCreateClientRequestDTO
 *
 * @property string $pinfl
 * @property string $passport_id
 * @property string $loan_id
 * @property string $ext_id
 * @property string $firstname
 * @property string $lastname
 * @property string $middlename
 * @property int $filial_id
 * @property float $debit_amount
 * @property array $phones
 */
class UniredCreateClientRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
        public string $passport_id,
        public string $loan_id,
        public string $ext_id,
        public string $firstname,
        public string $lastname,
        public string $middlename,
        public int $filial_id,
        public float $debit_amount,
        public array $phones,
    ) {}
}
