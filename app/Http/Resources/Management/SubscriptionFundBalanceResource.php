<?php

namespace App\Http\Resources\Management;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SubscriptionFundBalanceResource',
    title: 'SubscriptionFundBalanceResource',
    properties: [
        new OA\Property(property: 'session_id', type: 'int', example: '178'),
        new OA\Property(property: 'otpSentPhone', type: 'string', example: '+998999661999'),
    ],
)]

class SubscriptionFundBalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'session_id' => $this->resource->session,
            'otpSentPhone' => $this->resource->otpSentPhone,
        ];
    }
}
