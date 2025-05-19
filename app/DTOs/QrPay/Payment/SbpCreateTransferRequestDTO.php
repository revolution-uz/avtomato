<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpCreateTransferRequestDTO
 *
 * @property string $ext_id
 * @property int $amount
 * @property int $account_id
 * @property string $purpose_code
 * @property string $redirect_url
 */
class SbpCreateTransferRequestDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $ext_id,
        public int $amount,
        public int $account_id,
        public string $redirect_url = 'avtomato.uz',
        public string $purpose_code = '00668',
    ) {}
}
