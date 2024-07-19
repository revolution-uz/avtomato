<?php

namespace Installment\Mutations;

use App\Models\Company\Card;
use App\Models\Company\Installment;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DetachCardGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::factory()->count(5)->create();
        foreach (Installment::all() as $installment) {
            $installment->cards()->attach(Card::factory()->count(5)->create());
        }
    }

    #[Test]
    public function successDetachCard()
    {
        $installment = Installment::query()->inRandomOrder()->first();
        $idsInput = $installment->cards()->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'installment_id' => $installment->id,
                    'ids' => $idsInput,
                ])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'detachCard',
            ],
        ]);

        $this->assertTrue($response->json('data.detachCard'));
        $this->assertTrue($installment->cards()->whereIn('id', $idsInput)->doesntExist());
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
                    'installment_id' => null,
                    'ids' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$ids" of non-null type "[Int]!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'installment_id' => 999999999,
                    'ids' => [999999999],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
        $this->assertEquals(trans('api.not found'), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DetachCard($installment_id: Int!, $ids: [Int]!) {
                detachCard(installment_id: $installment_id, ids: $ids)
            }
        ';

        return $query;
    }
}
