<?php

namespace App\DTOs\QrPay;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class QrPayPaginationRequestDTO
 *
 * @property int $pageSize
 * @property int $page
 * @property int|null $filial_id
 */
class QrPayPaginationRequestDTO extends BaseQrPayDTO
{
    public function __construct(
        public int $pageSize,
        public int $page,
        public ?int $filial_id,
    ) {}
}
