<?php

namespace App\DTOs\Unired\System;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredSetWebhookDTO
 *
 * @property string $webhook
 */
class UniredSetWebhookDTO extends BaseUniredDTO
{
    public function __construct(
        public string $webhook,
    ) {}
}
