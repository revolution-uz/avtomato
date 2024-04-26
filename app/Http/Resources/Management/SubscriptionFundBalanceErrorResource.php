<?php

namespace App\Http\Resources\Management;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SubscriptionFundBalanceErrorResource',
    title: 'SubscriptionFundBalanceErrorResource',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Something went wrong'),
    ],
)]
class SubscriptionFundBalanceErrorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => $this->resource->result->description,
        ];
    }
}
