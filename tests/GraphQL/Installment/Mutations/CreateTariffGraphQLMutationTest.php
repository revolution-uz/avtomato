<?php

namespace GraphQL\Installment\Mutations;

use App\Models\Company\Installment;
use App\Models\Company\InstallmentComment;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateTariffGraphQLMutationTest extends GraphQLTestCase
{
    private const GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private array $_input = [
        'name' => 'test',
        'is_annual' => true,
        'percent' => 10.5,
        'min_period' => 1,
        'max_period' => 12,
        'min_amount' => 1000,
        'max_amount' => 100000,
        'min_initial_fee' => 18,
        'max_initial_fee' => 100,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Installment::query()->forceDelete();
        InstallmentComment::query()->forceDelete();
    }

    #[Test]
    public function successTariff()
    {
        $response = $this->sendGraphQLRequest(
            tariff: $this->_input,
        );
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(
            [
                'data' => [
                    'createTariff' => [
                        'id',
                        'name',
                        'is_annual',
                        'percent',
                        'min_period',
                        'max_period',
                        'min_amount',
                        'max_amount',
                        'min_initial_fee',
                        'max_initial_fee',
                    ],
                ],
            ]
        );
    }

    #[Test]
    public function errorValidation(): void
    {
        $response = $this->sendGraphQLRequest(
            [
                'name' => '2',
                'is_annual' => true,
                'percent' => 123,
                'min_period' => 123,
                'max_period' => 12,
                'min_amount' => 1000,
                'max_amount' => 100000,
                'min_initial_fee' => 18,
                'max_initial_fee' => 100,
            ]
        );
        $response->assertOk();

        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.min.string', ['min' => '3', 'attribute' => 'tariff.name']),
            $response->json('errors.0.extensions.validation')['tariff.name'][0]
        );

        $this->assertEquals(
            trans('validation.max.numeric', ['max' => '100', 'attribute' => 'tariff.percent']),
            $response->json('errors.0.extensions.validation')['tariff.percent'][0]
        );
        $this->assertEquals(
            trans('validation.max.numeric', ['max' => '24', 'attribute' => 'tariff.min period']),
            $response->json('errors.0.extensions.validation')['tariff.min_period'][0]
        );
    }

    private function sendGraphQLRequest(array $tariff): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($tariff)
            );
    }

    private function buildGraphQLQuery(array $tariff): array
    {
        return [
            'variables' => ['tariff' => $tariff],
            'query' => '
            mutation ($tariff: TariffInputGraphQLType!) {
                createTariff(tariff: $tariff){
                    id
                    name
                    is_annual
                    percent
                    min_period
                    max_period
                    min_amount
                    max_amount
                    min_initial_fee
                    max_initial_fee
                }
            }
        ',
        ];
    }
}
