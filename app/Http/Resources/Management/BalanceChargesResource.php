<?php

namespace App\Http\Resources\Management;

use App\Models\Company\BalanceCharge;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BalanceChargesResource',
    title: 'BalanceChargesResource object',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'feature_id', type: 'int', example: '1'),
        new OA\Property(property: 'amount', type: 'int', example: '100000'),
        new OA\Property(property: 'feature', ref: '#/components/schemas/FeatureResource'),
        new OA\Property(property: 'created_at', type: 'int', example: '121312312'),
    ],
)]
/**
 * @property BalanceCharge $resource
 */
class BalanceChargesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'feature_id' => $this->resource->feature_id,
            'amount' => $this->resource->amount,
            'feature' => FeatureResource::make($this->resource->feature),
            'created_at' => $this->resource->created_at->getTimestamp(),
        ];
    }
}
