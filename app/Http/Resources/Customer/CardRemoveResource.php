<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CardRemoveResource',
    title: 'Card remove response object',
    properties: [
        new OA\Property(property: 'status', type: 'bool', example: 'true'),
        new OA\Property(property: 'message', type: 'string', example: 'The card was successfully removed'),
    ],
)]
class CardRemoveResource extends JsonResource
{
    public function __construct(
        public bool $card,
        public string $message,
    ) {}
}
