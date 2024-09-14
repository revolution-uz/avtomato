<?php

namespace App\DTOs\Unired\Payment;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredPaginationRequestDTO
 *
 * @property int $page_size,
 * @property int $page_number,
 * @property string $date_from,
 * @property string $date_to,
 * @property string $type,
 * @property string $ok,
 * @property int|null $filial_id
 */
class UniredGetPaymentsPagitionRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public int $page_size,
        public int $page_number,
        public string $date_from,
        public string $date_to,
        public string $type,
        public string $status,
        public ?int $filial_id,
    ) {}
}
