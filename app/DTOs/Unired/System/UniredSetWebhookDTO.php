<?php

namespace App\DTOs\Unired\System;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredSetWebhookDTO
 *
 * @property string $webhook
 * @property string $token
 * @property bool $status
 */
class UniredSetWebhookDTO extends BaseUniredDTO
{
    public function __construct(
        public string $webhook,
        public string $token,
        public bool $status
    ) {}
}
