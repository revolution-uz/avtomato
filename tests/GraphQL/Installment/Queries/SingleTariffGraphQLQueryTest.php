<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Models\Company\Tariff;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleTariffGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Tariff $_tariff;

    protected function setUp(): void
    {
        parent::setUp();

        Tariff::query()->forceDelete();
        $this->_tariff = Tariff::factory()->create();
    }

    #[Test]
    public function successSingleTariff()
    {
        $response = $this->sendToGraphQL($this->_tariff->id);
        $this->assertSuccessSingleTariffResponse($response);
    }

    #[Test]
    public function errorSingleTariff()
    {
        // not found
        $response = $this->sendToGraphQL(999);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        // deleted
        $this->_tariff->delete();
        $response = $this->sendToGraphQL($this->_tariff->id);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery($id): array
    {
        $query = [];
        $query['variables'] = ['id' => $id];
        $query['query'] = '
            query ($id: Int!) {
                singleTariff(id: $id) {
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

    private function assertSuccessSingleTariffResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleTariff' => [
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
        $tariff = Tariff::query()->find($response->json('data.singleTariff.id'));
        $this->assertNotNull($tariff);
        $this->assertEquals($tariff->id, $response->json('data.singleTariff.id'));
        $this->assertEquals($tariff->name, $response->json('data.singleTariff.name'));
        $this->assertEquals($tariff->is_annual, $response->json('data.singleTariff.is_annual'));
        $this->assertEquals($tariff->percent, $response->json('data.singleTariff.percent'));
        $this->assertEquals($tariff->min_period, $response->json('data.singleTariff.min_period'));
        $this->assertEquals($tariff->max_period, $response->json('data.singleTariff.max_period'));
        $this->assertEquals($tariff->min_amount, $response->json('data.singleTariff.min_amount'));
        $this->assertEquals($tariff->max_amount, $response->json('data.singleTariff.max_amount'));
        $this->assertEquals($tariff->min_initial_fee, $response->json('data.singleTariff.min_initial_fee'));
        $this->assertEquals($tariff->max_initial_fee, $response->json('data.singleTariff.max_initial_fee'));
    }

    private function sendToGraphQL($id): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($id));
    }
}
