<?php

namespace App\Http\Resources\Common;

use App\Models\Company\Region;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RegionResource',
    title: 'RegionResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'name', type: 'string', example: 'Xorazm viloyati'),
    ],
)]
/**
 * use @mixin Region
 */
class RegionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->{'name_' . app()->getLocale()},
        ];
    }
}
