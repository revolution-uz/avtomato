<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AttachCustomerCardResource',
    title: 'AttachCustomerCardResource object',
    properties: [
        new OA\Property(property: 'session', type: 'int', example: '1'),
        new OA\Property(property: 'otpSentPhone', type: 'string', example: '99890*****01'),
    ],
)]
class AttachCustomerCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'session' => $this->resource->session,
            'otpSentPhone' => $this->resource->otpSentPhone,
        ];
    }
}
