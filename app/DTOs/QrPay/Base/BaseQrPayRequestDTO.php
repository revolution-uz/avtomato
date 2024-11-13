<?php

namespace App\DTOs\QrPay\Base;

/**
 * Class BaseQrPayRequestDTO
 *
 * @property string $method
 * @property array $params
 * @property string $jsonrpc
 * @property int $id
 */
class BaseQrPayRequestDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $method,
        public BaseQrPayDTO $params,
        public string $jsonrpc,
        public int $id,
    ) {}
}
