<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\InstallmentCard;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentCardResource',
    title: 'InstallmentCard object',
    properties: [
        new OA\Property(property: 'installment_id', type: 'int', example: '1'),
        new OA\Property(property: 'card_id', type: 'int', example: '1'),
    ],
)]
/**
 * @property InstallmentCard $resource
 */
class InstallmentCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'installment_id' => $this->resource->installment_id,
            'card_id' => $this->resource->card_id,
        ];
    }
}
