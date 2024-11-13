<?php

namespace App\DTOs\QrPay;

use App\DTOs\QrPay\Base\BaseQrPayDTO;
use Carbon\Carbon;

/**
 * Class QrPayHostResponseDTO
 *
 * @property int|null $host
 * @property Carbon|null $timestamp
 */
class QrPayHostResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?int $host,
        public ?Carbon $timestamp,
    ) {}
}
