<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Tariff;
use App\Services\Installment\InstallmentService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CalculateGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input;

    private Tariff $_annualTariff;

    private array $_annualTestData = [
        [
            'amount' => 12498.75,
            'portion' => 94493.62,
            'monthly' => 106992.37,
        ],
        [
            'amount' => 11317.58,
            'portion' => 95674.79,
            'monthly' => 106992.37,
        ],
        [
            'amount' => 10121.64,
            'portion' => 96870.73,
            'monthly' => 106992.37,
        ],
        [
            'amount' => 8910.76,
            'portion' => 98081.61,
            'monthly' => 106992.37,
        ],
        [
            'amount' => 7684.74,
            'portion' => 99307.63,
            'monthly' => 106992.37,
        ],
        [
            'amount' => 6443.4,
            'portion' => 100548.98,
            'monthly' => 106992.37,
        ],
        [
            'amount' => 5186.53,
            'portion' => 101805.84,
            'monthly' => 106992.37,
        ],
        [
            'amount' => 3913.96,
            'portion' => 103078.41,
            'monthly' => 106992.37,
        ],
        [
            'amount' => 2625.48,
            'portion' => 104366.89,
            'monthly' => 106992.37,
        ],
        [
            'amount' => 1320.89,
            'portion' => 105671.48,
            'monthly' => 106992.37,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_annualTariff = Tariff::factory()->create(['percent' => 15]);
    }

    #[Test]
    public function successAnnualAndWithoutStartsIn()
    {
        $this->_input = [
            'tariff_id' => $this->_annualTariff->id,
            'amount' => 1000000,
            'initial_fee' => 100,
            'period' => 10,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessCalculateResponse($response, $this->_annualTariff);
    }

    #[Test]
    public function successAnnualAndWithStartsIn()
    {
        $this->_input = [
            'tariff_id' => $this->_annualTariff->id,
            'amount' => 1000000,
            'initial_fee' => 100,
            'period' => 10,
            'starts_in' => '2007-07-07',
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessCalculateResponse($response, $this->_annualTariff);
    }

    #[Test]
    public function errorValidation()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'tariff_id' => null,
                    'amount' => null,
                    'initial_fee' => null,
                    'period' => null,
                    'starts_in' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$tariff_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$amount" of non-null type "BigInteger!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$initial_fee" of non-null type "BigInteger!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$period" of non-null type "Int!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'tariff_id' => 999999999,
                    'amount' => 1000000,
                    'initial_fee' => 1000000,
                    'period' => 10,
                    'starts_in' => 'ewkjfew',
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'tariff id']), $response->json('errors.0.extensions.validation.tariff_id.0'));
        $this->assertEquals(trans('validation.date', ['attribute' => 'starts in']), $response->json('errors.0.extensions.validation.starts_in.0'));
    }

    #[Test]
    public function errorCalculateWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'tariff_id' => $this->_annualTariff->id,
                    'amount' => 1000000,
                    'initial_fee' => 100,
                    'period' => 10,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query Calculate(
                $tariff_id: Int!
                $amount: BigInteger!
                $initial_fee: BigInteger!
                $period: Int!
                $starts_in: String
            ) {
                calculate(
                    tariff_id: $tariff_id
                    amount: $amount
                    initial_fee: $initial_fee
                    period: $period
                    starts_in: $starts_in
                ) {
                    id
                    date
                    amount
                    portion
                    monthly
                }
            }
        ';

        return $query;
    }

    private function assertSuccessCalculateResponse(TestResponse $response, Tariff $tariff): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'calculate' => [
                    '*' => [
                        'id',
                        'date',
                        'amount',
                        'portion',
                        'monthly',
                    ],
                ],
            ],
        ]);

        $this->assertEquals($this->_input['period'], count($response->json('data.calculate')));

        $data = $this->_annualTestData;
        $periods = InstallmentService::generatePeriods(Carbon::parse($this->_input['starts_in'] ?? now()->format('Y-m-d')), $this->_input['period']);

        for ($i = 0; $i < $this->_input['period']; $i++) {
            $this->assertEquals($i + 1, $response->json("data.calculate.{$i}.id"));
            $this->assertEquals($periods[$i]?->format('Y-m-d'), $response->json("data.calculate.{$i}.date"));
            $this->assertEquals($data[$i]['amount'], $response->json("data.calculate.{$i}.amount"));
            $this->assertEquals($data[$i]['portion'], $response->json("data.calculate.{$i}.portion"));
            $this->assertEquals($data[$i]['monthly'], $response->json("data.calculate.{$i}.monthly"));
        }
    }
}
