<?php

namespace App\DTOs\Unired\Payment;

use App\DTOs\Unired\Base\BaseUniredDTO;

/**
 * Class UniredPaginationRequestDTO
 *
 * @property int $page_size
 * @property int $page_number
 * @property string $date_from
 * @property string $date_to
 * @property string|null $type
 * @property string|null $status
 * @property bool|null $is_sent
 * @property string|null $pinfl
 * @property string|null $loan_id
 * @property int|null $filial_id
 */
class UniredGetPaymentsPaginationRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public int $page_size,
        public int $page_number,
        public string $date_from, // format: Y-m-d
        public string $date_to, // format: Y-m-d
        public ?string $type = null, // ['Online', 'OFFLINE']
        public ?string $status = null, // ['Cancelled', 'OK']
        public ?bool $is_sent = null,
        public ?bool $pinfl = null, // String(14)
        public ?bool $loan_id = null, // String(200)
        public ?int $filial_id = null,
    ) {}
}
