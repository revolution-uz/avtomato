<?php

namespace App\Http\Resources\Common;

use App\Enums\Company\AddressTypeEnum;
use App\Models\Company\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AddressResource',
    title: 'AddressResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'type', type: 'int', example: '1', enum: AddressTypeEnum::class),
        new OA\Property(property: 'address', type: 'string', example: 'Al-Xorazmiy ko\'chasi 1-uy'),
        new OA\Property(property: 'region', type: 'string', example: 'Xorazm viloyati'),
        new OA\Property(property: 'district', type: 'string', example: 'Urganch tumani'),
        new OA\Property(property: 'mahalla', type: 'string', example: 'Olmazor mahallasi'),
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
            'region' => $this->resource->region->{'name_' . app()->getLocale()},
            'district' => $this->resource->district->{'name_' . app()->getLocale()},
            'mahalla' => $this->resource->mahalla->{'name_' . app()->getLocale()},
        ];
    }
}
