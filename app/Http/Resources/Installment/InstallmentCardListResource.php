<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentCardListResource',
    title: 'InstallmentCardList object',
    properties: [
        new OA\Property(property: 'type', type: 'string', enum: ['customer', 'guarantor'], example: 'customer'),
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'owner', type: 'string', example: 'Ali Valiyev'),
        new OA\Property(property: 'pan', type: 'string', example: '986011******4444'),
        new OA\Property(property: 'expiry', type: 'string', example: '2**9'),
        new OA\Property(property: 'is_attached', type: 'boolean', example: 'true'),
        new OA\Property(property: 'card_type', type: 'string', example: 'UZCARD'),
    ]
)]
/**
 * @property Installment $resource
 */
class InstallmentCardListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->resource->customer->cards->map(function ($card) {
            return $this->getCardData($card, $this->resource->cards, $this->resource->customer);
        })->toArray();

        $this->resource->guarantors->each(function ($guarantor) use (&$data) {
            $guarantor->cards->each(function ($card) use (&$data, $guarantor) {
                $data[] = $this->getCardData($card, $this->resource->cards, $guarantor, true);
            });
        });

        return $data;
    }

    private function getCardData(Card $card, $installmentCards, Customer $customer, $isGuarantor = false): array
    {
        $expiry = str_split($card->expiry, 2);

        return [
            'type' => $isGuarantor ? 'guarantor' : 'customer',
            'id' => $card->id,
            'customer_id' => $customer->id,
            'owner' => $card->owner ?? $customer->fullName(),
            'pan' => $card->pan,
            'expiry' => implode('/', [$expiry[1], $expiry[0]]),
            'is_attached' => $installmentCards->where('id', $card->id)->count() > 0,
            'card_type' => $card->card_type,
        ];
    }
}
