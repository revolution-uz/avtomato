<?php

namespace App\Http\Resources\Common;

use App\Models\Company\District;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DistrictResource',
    title: 'DistrictResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '12'),
        new OA\Property(property: 'region_id', type: 'int', example: '1'),
        new OA\Property(property: 'name', type: 'string', example: 'Urganch shaxri'),
    ],
)]
/**
 * @property District $resource
 */
class DistrictResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'region_id' => $this->resource->region_id,
            'name' => $this->resource->{'name_' . app()->getLocale()},
        ];
    }
}
