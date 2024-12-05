<?php

namespace Database\Factories\Company;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoCreateRequestDTO;
use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryBorrowerDTO;
use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryLoanDTO;
use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryRequestDTO;
use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryResponseDTO;
use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryResultDTO;
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
        return [
            'ext_id' => $this->faker->numberBetween(100000, 9999999),
            'status' => $this->faker->randomElement(ScoringRequestStatusEnum::cases()),
            'client_code' => $this->faker->randomElement(ClientCodeEnum::cases()),
            'customer_id' => $this->faker->numberBetween(1, 10000),
            'card_id' => $this->faker->numberBetween(1, 10000),
            'type' => $this->faker->randomElement(ScoringRequestTypeEnum::cases()),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (ScoringRequest $scoringRequest) {
            if (! $scoringRequest->request) {
                $scoringRequest->request = $this->requestPayload($scoringRequest->type);
            }
            if (! $scoringRequest->response) {
                $scoringRequest->response = $this->responsePayload($scoringRequest->type);
            }
        });
    }

    private function requestPayload($type): BaseScoringRequest
    {
        return match ($type) {
            ScoringRequestTypeEnum::KATM_CREDIT_HISTORY,
            ScoringRequestTypeEnum::KATM_INPS_HISTORY,
            ScoringRequestTypeEnum::KATM_PENSION_INFO => new KatmCreateRequestDTO($this->faker->unique()->randomNumber(14), 'AB', '4143286', Carbon::createFromDate('15-06-2016'), 'Akmal', 'Baxodirov', 'Azamat', '998882019909', '998882019909', Carbon::createFromDate('09-06-1999'), 13, 114),

            ScoringRequestTypeEnum::PLUM_MIB_INFO => new MibInfoCreateRequestDTO($this->faker->unique()->randomNumber(14), '+998938651719'),

            ScoringRequestTypeEnum::PLUM_TAX_INFO => new TaxInfoCreateRequestDTO($this->faker->unique()->randomNumber(14)),

            ScoringRequestTypeEnum::UZCARD,
            ScoringRequestTypeEnum::HUMO => new CardCreateRequestDTO($this->faker->creditCardNumber(), '2803'),

            ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY => new RoyxatCreditHistoryRequestDTO($this->faker->regexify('[A-Z]{2}[0-9]{7}'), $this->faker->numerify(str_repeat('#', 14))),
        };
    }

    private function responsePayload($type): BaseScoringResponse
    {
        return match ($type) {
            ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY => RoyxatCreditHistoryResponseDTO::from([
                'ok' => true,
                'result' => RoyxatCreditHistoryResultDTO::from([
                    'borrower' => RoyxatCreditHistoryBorrowerDTO::from([
                        'full_name' => $this->faker->name,
                        'passport_id' => $this->faker->regexify('[A-Z]{2}[0-9]{7}'),
                        'pinfl' => $this->faker->numerify(str_repeat('#', 14)),
                        'birth_date' => $this->faker->date,
                    ]),
                    'loans' => new DataCollection(
                        RoyxatCreditHistoryLoanDTO::class,
                        [
                            RoyxatCreditHistoryLoanDTO::from([
                                'id' => $this->faker->numberBetween(1, 10000),
                                'amount' => $this->faker->numberBetween(1, 10000),
                                'contract_date' => $this->faker->date,
                                'duration' => $this->faker->numberBetween(1, 10000),
                                'max_overdue_days' => $this->faker->numberBetween(1, 10000),
                                'total_overdue_days' => $this->faker->numberBetween(1, 10000),
                                'total_overdue_amount' => $this->faker->numberBetween(1, 10000),
                                'status' => 'active',
                            ]),
                        ],
                    ),
                ]),
                'error_code' => null,
                'description' => null,
            ]),
        };

        // $details = new DataCollection(
        //     DetailedAnalyticsInMonthDTO::class,
        //     [
        //         new DetailedAnalyticsInMonthDTO('This is description', '206', 10101010),
        //         new DetailedAnalyticsInMonthDTO('This is not description', '208', 20202020),
        //     ]
        // );
        //  // return match ($type) {
        //     default => new CardScoringResponseDTO(new DataCollection(
        //         MonthlyAnalyticsDTO::class,
        //         [
        //             new MonthlyAnalyticsDTO(
        //                 Carbon::now(),
        //                 30303030,
        //                 $details
        //             ),
        //         ]
        //     ))
        // };
    }
}
