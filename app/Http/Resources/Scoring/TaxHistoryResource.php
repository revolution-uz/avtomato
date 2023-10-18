<?php

namespace App\Http\Resources\Scoring;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TaxHistoryResource',
    title: 'TaxHistoryResource',
    properties: [
        new OA\Property(property: 'ball', type: 'int', example: '0'),
    ],
)]
class TaxHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ball' => $this->resource->debtBal,
        ];
    }
}
