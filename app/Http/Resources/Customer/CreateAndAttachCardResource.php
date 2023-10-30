<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateAndAttachCardResource',
    title: 'Card object',
    properties: [
        new OA\Property(property: 'card', ref: '#/components/schemas/CardResource'),
        new OA\Property(property: 'attach', ref: '#/components/schemas/AttachCustomerCardResource'),
    ],
)]
class CreateAndAttachCardResource extends JsonResource
{
    public function __construct(
        public CardResource $card,
        public AttachCustomerCardResource $attach,
    ) {
    }
}
