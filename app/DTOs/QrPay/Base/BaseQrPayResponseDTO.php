<?php

namespace App\DTOs\QrPay\Base;

use App\DTOs\QrPay\QrPayErrorResponseDTO;
use App\DTOs\QrPay\QrPayHostResponseDTO;
use App\Exceptions\QrPayException;

/**
 * Class BaseQrPayResponseDTO
 *
 * @property string|null $id
 * @property string|null $request_id
 * @property string|null $origin
 * @property bool|null $status
 * @property QrPayErrorResponseDTO|null $error
 * @property array|null $result
 * @property QrPayHostResponseDTO|null $host
 */
class BaseQrPayResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?string $id,
        public ?string $request_id,
        public ?string $origin,
        public ?QrPayHostResponseDTO $host,
        public ?bool $status,
        public ?QrPayErrorResponseDTO $error,
        public ?array $result,
    ) {}

    public function isSuccess(): bool
    {
        if (is_null($this->status)) {
            throw new QrPayException('Invalid response');
        } else {
            return $this->status;
        }
    }
}
