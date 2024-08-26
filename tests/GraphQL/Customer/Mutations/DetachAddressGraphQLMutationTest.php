<?php

namespace Tests\GraphQL\Customer\Mutations;


use App\Models\Company\Address;
use App\Models\Company\Customer;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DetachAddressGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    private array $_idsInput;

    protected function setUp(): void
    {
        parent::setUp();

        Customer::query()->forceDelete();
        $this->_customer = Customer::factory()->create();
        Address::query()->forceDelete();
        Address::factory()->count(5)->create(['addressable_type' => Customer::class, 'addressable_id' => $this->_customer->id]);
    }

    #[Test]
    public function successDetachAddress()
    {
        $this->_idsInput = $this->_customer->addresses()->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => $this->_customer->id,
                    'ids' => $this->_idsInput,
                ])
            );

        $this->assertSuccessDetachAddressResponse($response);
    }

    #[Test]
    public function errorValidations()
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

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => Customer::query()->orderByDesc('id')->first()->id + 1,
                    'ids' => [Address::query()->orderByDesc('id')->first()->id + 1],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'customer id']), $response->json('errors.0.extensions.validation.customer_id.0'));
        $ids = json_decode($response->json('errors.0.extensions.validation.ids.0'), true);
        $this->assertNotNull($ids);
        $this->assertArrayHasKey('ids.0', $ids);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.0']), $ids['ids.0'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DetachAddress($customer_id: Int!, $ids: [Int!]!) {
                detachAddress(customer_id: $customer_id, ids: $ids)
            }
        ';

        return $query;
    }

    private function assertSuccessDetachAddressResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'detachAddress',
            ],
        ]);
        $this->assertTrue($response->json('data.detachAddress'));
        $this->assertTrue($this->_customer->addresses()->whereIn('id', $this->_idsInput)->doesntExist());
    }
}
