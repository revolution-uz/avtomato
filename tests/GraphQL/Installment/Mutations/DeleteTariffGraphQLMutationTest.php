<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Models\Company\Installment;
use App\Models\Company\Tariff;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteTariffGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::factory()->count(5)->create();
        Tariff::factory()->count(5)->create();
    }

    #[Test]
    public function successDeleteTariff()
    {
        $idsInput = Tariff::query()->whereDoesntHave('installments')
            ->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'deleteTariff',
            ],
        ]);

        $this->assertTrue($response->json('data.deleteTariff'));
        $this->assertTrue(Tariff::query()->whereIn('id', $idsInput)->doesntExist());
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['ids' => null])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$ids" of non-null type "[Int]!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'ids' => [
                        999999999,
                        ...Tariff::query()->whereHas('installments')->inRandomOrder()->limit(1)
                            ->pluck('id')->toArray(),
                    ],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.not found'), $response->json('errors.0.extensions.validation')['ids.0'][0]);
        $this->assertEquals(trans('api.cannot_delete', ['item' => __('api.Tariff'), 'element' => __('api.Installment')]), $response->json('errors.0.extensions.validation')['ids.1'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DeleteTariff($ids: [Int]!) {
                deleteTariff(ids: $ids)
            }
        ';

        return $query;
    }
}
