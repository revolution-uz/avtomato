<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginResource',
    title: 'LoginResource',
    properties: [
        new OA\Property(property: 'token', type: 'string', example: '1|DoeKLT28Gj1fxDGC3zoIYH4ZVseZxXkfFSmYAnla'),
    ],
)]
/**
 * @property string $resource
 */
class LoginResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->resource,
        ];
    }
}
