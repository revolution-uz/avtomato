<?php

namespace Installment\Mutations;

use App\Models\Company\Tariff;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateTariffGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Tariff $_tariff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_tariff = Tariff::factory()->create();
    }

    #[Test]
    public function successUpdateTariff()
    {
        $tariffInput = [
            'name' => 'Tariff',
            'is_annual' => true,
            'percent' => 5.0,
            'min_period' => 1,
            'max_period' => 10,
            'min_amount' => 1000000,
            'max_amount' => 10000000,
            'min_initial_fee' => 1000000,
            'max_initial_fee' => 10000000,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => $this->_tariff->id,
                    'tariff' => $tariffInput,
                ])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateTariff' => [
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
        ]);

        $this->_tariff->refresh();
        $this->assertEquals($this->_tariff->name, $tariffInput['name']);
        $this->assertEquals($this->_tariff->is_annual, $tariffInput['is_annual']);
        $this->assertEquals($this->_tariff->percent, $tariffInput['percent']);
        $this->assertEquals($this->_tariff->min_period, $tariffInput['min_period']);
        $this->assertEquals($this->_tariff->max_period, $tariffInput['max_period']);
        $this->assertEquals($this->_tariff->min_amount, $tariffInput['min_amount']);
        $this->assertEquals($this->_tariff->max_amount, $tariffInput['max_amount']);
        $this->assertEquals($this->_tariff->min_initial_fee, $tariffInput['min_initial_fee']);
        $this->assertEquals($this->_tariff->max_initial_fee, $tariffInput['max_initial_fee']);

        $this->assertEquals($this->_tariff->id, $response->json('data.updateTariff.id'));
        $this->assertEquals($this->_tariff->name, $response->json('data.updateTariff.name'));
        $this->assertEquals($this->_tariff->is_annual, $response->json('data.updateTariff.is_annual'));
        $this->assertEquals($this->_tariff->percent, $response->json('data.updateTariff.percent'));
        $this->assertEquals($this->_tariff->min_period, $response->json('data.updateTariff.min_period'));
        $this->assertEquals($this->_tariff->max_period, $response->json('data.updateTariff.max_period'));
        $this->assertEquals($this->_tariff->min_amount, $response->json('data.updateTariff.min_amount'));
        $this->assertEquals($this->_tariff->max_amount, $response->json('data.updateTariff.max_amount'));
        $this->assertEquals($this->_tariff->min_initial_fee, $response->json('data.updateTariff.min_initial_fee'));
        $this->assertEquals($this->_tariff->max_initial_fee, $response->json('data.updateTariff.max_initial_fee'));
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => null,
                    'tariff' => [
                        'name' => null,
                        'is_annual' => null,
                        'percent' => null,
                        'min_period' => null,
                        'max_period' => null,
                        'min_amount' => null,
                        'max_amount' => null,
                        'min_initial_fee' => null,
                        'max_initial_fee' => null,
                    ],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$tariff" got invalid value null at "tariff.name"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$tariff" got invalid value null at "tariff.is_annual"; Expected non-nullable type "Boolean!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$tariff" got invalid value null at "tariff.percent"; Expected non-nullable type "Float!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$tariff" got invalid value null at "tariff.min_period"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$tariff" got invalid value null at "tariff.max_period"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$tariff" got invalid value null at "tariff.min_amount"; Expected non-nullable type "BigInteger!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$tariff" got invalid value null at "tariff.max_amount"; Expected non-nullable type "BigInteger!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$tariff" got invalid value null at "tariff.min_initial_fee"; Expected non-nullable type "BigInteger!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$tariff" got invalid value null at "tariff.max_initial_fee"; Expected non-nullable type "BigInteger!" not to be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => 999999999,
                    'tariff' => [
                        'name' => Str::random(41),
                        'is_annual' => true,
                        'percent' => 101.0,
                        'min_period' => 25,
                        'max_period' => 25,
                        'min_amount' => 1000000001,
                        'max_amount' => 1000000001,
                        'min_initial_fee' => 1000000001,
                        'max_initial_fee' => 1000000001,
                    ],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'tariff.name', 'max' => 40]), $response->json('errors.0.extensions.validation')['tariff.name'][0]);
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'tariff.percent', 'max' => 100]), $response->json('errors.0.extensions.validation')['tariff.percent'][0]);
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'tariff.min period', 'max' => 24]), $response->json('errors.0.extensions.validation')['tariff.min_period'][0]);
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'tariff.max period', 'max' => 24]), $response->json('errors.0.extensions.validation')['tariff.max_period'][0]);
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'tariff.min amount', 'max' => 1000000000]), $response->json('errors.0.extensions.validation')['tariff.min_amount'][0]);
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'tariff.max amount', 'max' => 1000000000]), $response->json('errors.0.extensions.validation')['tariff.max_amount'][0]);
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'tariff.min initial fee', 'max' => 1000000000]), $response->json('errors.0.extensions.validation')['tariff.min_initial_fee'][0]);
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'tariff.max initial fee', 'max' => 1000000000]), $response->json('errors.0.extensions.validation')['tariff.max_initial_fee'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateTariff($id: Int!, $tariff: TariffInputGraphQLType!) {
                updateTariff(id: $id, tariff: $tariff) {
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
        ';

        return $query;
    }
}
