<?php

namespace App\DTOs\QrPay\Terminal;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class QrCreateAccountRequestDTO
 *
 * @property string $account
 * @property string $filial_code
 * @property string $tax
 * @property string $name
 */
class SbpCreateAccountRequestDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $number,
        public string $branch,
        public string $inn,
        public string $name,
    ) {}
}
