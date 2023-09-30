<?php

namespace App\Http\Resources\Common;

use App\Enums\Company\PhoneTypeEnum;
use App\Models\Company\Phone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PhoneResource',
    title: 'PhoneResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'phone', type: 'int', example: '998933993300'),
        new OA\Property(property: 'type', type: 'int', enum: [PhoneTypeEnum::PRIVATE, PhoneTypeEnum::WORK, PhoneTypeEnum::RELATIVE], example: '1'),
        new OA\Property(property: 'comment', type: 'string', example: 'comment'),
    ],
)]
/**
 * use @mixin Phone
 */
class PhoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'phone' => $this->phone,
            'id' => $this->pivot->id,
            'type' => $this->pivot->type,
            'comment' => $this->pivot->comment,
        ];
    }
}
