<?php

namespace Tests\GraphQL\Ecommerce\Client\Mutations;

use App\Models\Company\Address;
use App\Models\Company\Ecommerce\User;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DetachAddressGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createEcommerceClientUser();
        Address::query()->forceDelete();
    }

    #[Test]
    public function successDetachAddress()
    {
        /** @var Address $address */
        $address = Address::factory()->create(['addressable_type' => User::class, 'addressable_id' => $this->_user->id]);

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['address_id' => $address->id]));

        $this->assertSuccessDetachAddressResponse($response);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery());
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$address_id" of required type "Int!" was not provided.'));

        // Rule errors
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['address_id' => 999999999]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'address id']), $response->json('errors.0.extensions.validation.address_id.0'));

        $address = Address::factory()->create();
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['address_id' => $address->id]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'address id']), $response->json('errors.0.extensions.validation.address_id.0'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation DetachAddress($address_id: Int!) {
                    detachAddress(address_id: $address_id)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessDetachAddressResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['detachAddress']]);
        $this->assertTrue($response->json('data.detachAddress'));

        $this->assertDatabaseMissing(Address::class, [
            'addressable_type' => User::class,
            'addressable_id' => $this->_user->id,
            'deleted_at' => null,
        ]);
    }
}
