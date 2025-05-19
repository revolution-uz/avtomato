<?php

namespace Tests\GraphQL\Customer\Mutations;

use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DetachCardGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    protected function setUp(): void
    {
        parent::setUp();

        Customer::query()->forceDelete();
        $this->_customer = Customer::factory()->create();
        Card::query()->forceDelete();
        Card::factory()->count(5)->create(['customer_id' => $this->_customer->id]);
    }

    #[Test]
    public function successDetachCard()
    {
        $idsInput = $this->_customer->cards()->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
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
        $this->assertTrue($this->_customer->cards()->whereIn('id', $idsInput)->doesntExist());
    }

    #[Test]
    public function errorValidation()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => null,
                    'ids' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$ids" of non-null type "[Int!]!" must not be null.'));

        // error with ids which attached to installments
        $card = Card::factory()->create(['customer_id' => $this->_customer->id]);
        $installment = Installment::factory()->create(['customer_id' => $this->_customer->id]);
        $installment->cards()->attach($card);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
                    'ids' => [$card->id],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.cannot_delete', ['item' => __('api.Card'), 'element' => __('api.Installment')]), $response->json('errors.0.extensions.validation.ids.0'));

        // error with unavailable ids
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
                    'ids' => [999999999],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.not found'), $response->json('errors.0.extensions.validation.ids.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DetachCard($customer_id: Int!, $ids: [Int!]!) {
                detachCard(customer_id: $customer_id, ids: $ids)
            }
        ';

        return $query;
    }
}
