<?php

namespace App\Http\Resources\Common;

use App\Models\Company\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AddressResource',
    title: 'AddressResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'type', type: 'int', example: '1'),
        new OA\Property(property: 'address', type: 'string', example: 'Al-Xorazmiy ko\'chasi 1-uy'),
        new OA\Property(property: 'region_id', type: 'int', example: '1'),
        new OA\Property(property: 'district_id', type: 'int', example: '1'),
        new OA\Property(property: 'mahalla_id', type: 'int', example: '1'),
        new OA\Property(property: 'region', ref: '#/components/schemas/RegionResource'),
        new OA\Property(property: 'district', ref: '#/components/schemas/DistrictResource'),
        new OA\Property(property: 'mahalla', ref: '#/components/schemas/MahallaResource'),
    ],
)]
/**
 * @property Address $resource
 */
class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'type' => $this->resource->type,
            'address' => $this->resource->address,
            'region_id' => $this->resource->region_id,
            'district_id' => $this->resource->district_id,
            'mahalla_id' => $this->resource->mahalla_id,
            'region' => new RegionResource($this->resource->region),
            'district' => new RegionResource($this->resource->district),
            'mahalla' => new RegionResource($this->resource->mahalla),
        ];
    }
}
