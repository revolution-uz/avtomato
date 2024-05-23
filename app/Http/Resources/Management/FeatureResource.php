<?php

namespace App\Http\Resources\Management;

use App\Models\Company\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FeatureResource',
    title: 'FeatureResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'key', type: 'string', example: 'installment_create'),
        new OA\Property(property: 'name', type: 'string', example: 'Rassrochka yaratish'),
        new OA\Property(property: 'description', type: 'string', example: 'Rassrochka yaratish qiymati X so`m bu miqdor rassrochka aktivlashgan yechiladi'),
        new OA\Property(property: 'amount', type: 'int', example: '25000'),
        new OA\Property(property: 'is_active', type: 'bool', example: 'true'),
        new OA\Property(property: 'created_at', type: 'integer', example: 1695643479),
        new OA\Property(property: 'updated_at', type: 'integer', example: 1695643479),
    ],
)]
/**
 * @property Feature $resource
 */
class FeatureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'key' => $this->resource->name,
            'name' => __('feature.' . $this->resource->name),
            'description' => $this->resource->description,
            'amount' => $this->resource->amount,
            'is_active' => $this->resource->is_active,
            'created_at' => $this->resource->created_at ? (int) $this->resource->created_at->format('U') : null,
            'updated_at' => $this->resource->updated_at ? (int) $this->resource->updated_at->format('U') : null,
        ];
    }
}
