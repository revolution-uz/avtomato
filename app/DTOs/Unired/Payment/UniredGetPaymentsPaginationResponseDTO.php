<?php

namespace App\DTOs\Unired\Payment;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

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
class UniredGetPaymentsPaginationResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public int $page_size,
        public int $page_number,
        public int $total_elements,
        public int $total_pages,
        #[DataCollectionOf(UniredPaymentDTO::class)]
        public ?DataCollection $payments,
    ) {}
}
