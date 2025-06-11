<?php

namespace App\DTOs\LoyaltyProgram;

use Exception;
use Spatie\LaravelData\Data;

class QrPayloadDTO extends Data
{
    public function __construct(
        public int $user_id,
        public int $timestamp,
        public int $tenant_id,
        public bool $is_distributed,
    ) {}

    public static function fromQr(string $qr): self
    {
        $qrDto = explode('$$', $qr);

        if (count($qrDto) !== 4 || ($timestamp = (int) $qrDto[1]) < now()->timestamp) {
            throw new Exception('Invalid QR code');
        }

        $attributeXorSum = (int) $qrDto[0];
        $userId = $attributeXorSum ^ $timestamp ^ tenant('id');

        return new self(
            user_id: $userId,
            timestamp: $timestamp,
            tenant_id: tenant('id'),
            is_distributed: (bool) (int) $qrDto[2]
        );
    }

    public function generateQr(): string
    {
        $attributeXorSum = $this->user_id ^ $this->timestamp ^ $this->tenant_id;

        $qr = $attributeXorSum . '$$'
            . $this->timestamp . '$$'
            . $this->is_distributed . '$$'
            . md5(config('loyalty-program.security.qr_salt') . '$' . $this->user_id . '$' . $this->tenant_id . '$' . $this->timestamp);

        return substr($qr, 0, 36);
    }
}
