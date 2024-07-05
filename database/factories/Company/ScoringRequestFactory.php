<?php

namespace Database\Factories\Company;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\DTOs\Scoring\Card\CardScoringResponseDTO;
use App\DTOs\Scoring\Card\DetailedAnalyticsInMonthDTO;
use App\DTOs\Scoring\Card\MonthlyAnalyticsDTO;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoCreateRequestDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\DataCollection;

/**
 * @extends Factory<ScoringRequest>
 */
class ScoringRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ScoringRequest::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(ScoringRequestStatusEnum::cases());
        $clientCode = $this->faker->randomElement(ClientCodeEnum::cases());
        $type = 6;

        return [
            'ext_id' => rand(100000, 9999999),
            'status' => $status,
            'type' => $type,
            'client_code' => $clientCode,
            'customer_id' => $this->faker->numberBetween(1, 10000),
            'card_id' => $this->faker->numberBetween(1, 10000),
            'request' => $this->requestPayload($type),
            'response' => $this->responsePayload($type),
        ];
    }

    private function requestPayload($type): BaseScoringRequest
    {
        return match ($type) {
            ScoringRequestTypeEnum::KATM_CREDIT_HISTORY->value,
            ScoringRequestTypeEnum::KATM_INPS_HISTORY->value,
            ScoringRequestTypeEnum::KATM_PENSION_INFO->value => new KatmCreateRequestDTO('30906997180039', 'AB', '4143286', Carbon::createFromDate('15-06-2016'), 'Akmal', 'Baxodirov', 'Azamat', '998882019909', '998882019909', Carbon::createFromDate('09-06-1999'), 13, 114),
            ScoringRequestTypeEnum::PLUM_MIB_INFO->value => new MibInfoCreateRequestDTO('21710025140075', '+998938651719'),
            ScoringRequestTypeEnum::PLUM_TAX_INFO->value => new TaxInfoCreateRequestDTO('21710025140075'),
            ScoringRequestTypeEnum::UZCARD->value,
            ScoringRequestTypeEnum::HUMO->value => new CardCreateRequestDTO('986012011463' . rand(1000, 9999), '2803'),
        };
    }

    private function responsePayload($type): CardScoringResponseDTO
    {
        $details = new DataCollection(
            DetailedAnalyticsInMonthDTO::class,
            [
                new DetailedAnalyticsInMonthDTO('This is description', '206', 10101010),
                new DetailedAnalyticsInMonthDTO('This is not description', '208', 20202020),
            ]
        );

        return match ($type) {
            default => new CardScoringResponseDTO(new DataCollection(
                MonthlyAnalyticsDTO::class,
                [
                    new MonthlyAnalyticsDTO(
                        Carbon::now(),
                        30303030,
                        $details
                    ),
                ]
            ))
        };
    }
}
