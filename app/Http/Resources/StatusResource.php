<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StatusResource',
    title: 'StatusResource',
    properties: [
        new OA\Property(property: 'success', type: 'bool', example: 'true'),
    ],
)]
/**
 * @property bool $resource
 */
class StatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->resource,
        ];
    }
}
