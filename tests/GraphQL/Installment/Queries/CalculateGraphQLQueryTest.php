<?php

namespace Installment\Queries;

use App\Models\Company\Tariff;
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
    private Tariff $_differentialTariff;

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

    private array $_differentialTestData = [
        [
            'amount' => 99990,
            'portion' => 12498.75,
            'monthly' => 112488.75,
        ],
        [
            'amount' => 99990,
            'portion' => 11248.88,
            'monthly' => 111238.88,
        ],
        [
            'amount' => 99990,
            'portion' => 9999,
            'monthly' => 109989,
        ],
        [
            'amount' => 99990,
            'portion' => 8749.13,
            'monthly' => 108739.13,
        ],
        [
            'amount' => 99990,
            'portion' => 7499.25,
            'monthly' => 107489.25,
        ],
        [
            'amount' => 99990,
            'portion' => 6249.38,
            'monthly' => 106239.38,
        ],
        [
            'amount' => 99990,
            'portion' => 4999.5,
            'monthly' => 104989.5,
        ],
        [
            'amount' => 99990,
            'portion' => 3749.63,
            'monthly' => 103739.63,
        ],
        [
            'amount' => 99990,
            'portion' => 2499.75,
            'monthly' => 102489.75,
        ],
        [
            'amount' => 99990,
            'portion' => 1249.88,
            'monthly' => 101239.88,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_annualTariff = Tariff::factory()->create(['is_annual' => true, 'percent' => 15]);
        $this->_differentialTariff = Tariff::factory()->create(['is_annual' => false, 'percent' => 15]);
    }

        //    #[Test]
        //    public function successAnnualAndWithoutStartsIn()
        //    {
        //        $this->_input = [
        //            'tariff_id' => $this->_annualTariff->id,
        //            'amount' => 1000000,
        //            'initial_fee' => 100,
        //            'period' => 10,
        //        ];
        //
        //        $response = $this
        //            ->actingAs($this->createAdminUser())
        //            ->postJson(
        //                route('graphql.installment'),
        //                $this->buildGraphQLQuery($this->_input)
        //            );
        //
        //        $this->assertSuccessCalculateResponse($response, $this->_annualTariff);
        //    }

    //    #[Test]
    //    public function successDifferentialAndWithoutStartsIn()
    //    {
    //        $this->_input = [
    //            'tariff_id' => $this->_differentialTariff->id,
    //            'amount' => 1000000,
    //            'initial_fee' => 100,
    //            'period' => 10,
    //        ];
    //
    //        $response = $this
    //            ->actingAs($this->createAdminUser())
    //            ->postJson(
    //                route('graphql.installment'),
    //                $this->buildGraphQLQuery($this->_input)
    //            );
    //
    //        $this->assertSuccessCalculateResponse($response, $this->_differentialTariff);
    //    }

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
    public function successDifferentialAndWithStartsIn()
    {
        $this->_input = [
            'tariff_id' => $this->_differentialTariff->id,
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

        $this->assertSuccessCalculateResponse($response, $this->_differentialTariff);
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

        $data = $tariff->is_annual ? $this->_annualTestData : $this->_differentialTestData;
        $date = Carbon::parse($this->_input['starts_in'] ?? Carbon::now()->format('Y-m-d'));

        for ($i = 0; $i < $this->_input['period']; $i++) {
            $this->assertEquals($i + 1, $response->json("data.calculate.{$i}.id"));
            $this->assertEquals($date->addMonths($i > 0 ? 1 : $i)->format('Y-m-d'), $response->json("data.calculate.{$i}.date"));
            $this->assertEquals($data[$i]['amount'], $response->json("data.calculate.{$i}.amount"));
            $this->assertEquals($data[$i]['portion'], $response->json("data.calculate.{$i}.portion"));
            $this->assertEquals($data[$i]['monthly'], $response->json("data.calculate.{$i}.monthly"));
        }
    }
}
