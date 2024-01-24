<?php

namespace App\Http\Resources\Management;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SubscriptionConfirmPaymentResource',
    title: 'SubscriptionConfirmPaymentResource',
    properties: [
        new OA\Property(property: 'terminalId', type: 'string', example: '92403966'),
        new OA\Property(property: 'merchantId', type: 'string', example: '90485446'),
        new OA\Property(property: 'cardNumber', type: 'string', example: '860049******6572'),
        new OA\Property(property: 'date', type: 'datetime', example: '2020-04-24T15:28:42.442126'),
    ],
)]

class SubscriptionConfirmPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'terminalId' => $this->resource->terminalId,
            'merchantId' => $this->resource->merchantId,
            'cardNumber' => $this->resource->cardNumber,
            'date' => $this->resource->date,
        ];
    }
}
