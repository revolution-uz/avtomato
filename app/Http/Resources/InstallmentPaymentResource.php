<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentPaymentResource',
    title: 'InstallmentPaymentResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'date', type: 'string', example: '01-01-2021'),
        new OA\Property(property: 'amount', type: 'float', example: '120000'),
        new OA\Property(property: 'portion', type: 'float', example: '4800'),
        new OA\Property(property: 'monthly', type: 'float', example: '14800'),
    ],
)]
/**
 * @param int|null $id
 * @param string|null $date
 * @param float|null $amount
 * @param float|null $portion
 * @param float|null $monthly
 *
 */
class InstallmentPaymentResource extends JsonResource
{
    public function __construct(
        public readonly ?int    $id = null,
        public readonly ?string $date = null,
        public readonly ?float  $amount = null,
        public readonly ?float  $portion = null,
        public readonly ?float  $monthly = null,
    )
    {
        parent::__construct([]);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'amount' => $this->amount,
            'portion' => $this->portion,
            'monthly' => $this->monthly,
        ];
    }
}
