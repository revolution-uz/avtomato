<?php

namespace Installment\Queries;

use App\Models\Company\Tariff;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CalculateGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Tariff $_tariff;

    private array $_input;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_tariff = Tariff::factory()->create(['percent' => 15]);
    }

    #[Test]
    public function successWithoutArguments()
    {
        $this->_input = [
            'tariff_id' => $this->_tariff->id,
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

        $this->assertSuccessCalculateResponse($response);
    }

    #[Test]
    public function successWithStartsIn()
    {
        $this->_input = [
            'tariff_id' => $this->_tariff->id,
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

        $this->assertSuccessCalculateResponse($response);
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$tariff_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$amount" of non-null type "BigInteger!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$initial_fee" of non-null type "BigInteger!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$period" of non-null type "Int!" must not be null.'));

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

    private function assertSuccessCalculateResponse(TestResponse $response): void
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
        $this->assertEquals(1, $response->json('data.calculate.0.id'));
        $this->assertEquals($this->_input['starts_in'] ?? date('Y-m-d'), $response->json('data.calculate.0.date'));
        $this->assertEquals(99990, $response->json('data.calculate.0.amount'));
        $this->assertEquals(14998.5, $response->json('data.calculate.0.portion'));
        $this->assertEquals(114988.5, $response->json('data.calculate.0.monthly'));
    }
}
