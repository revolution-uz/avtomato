<?php

namespace GraphQL\Ecommerce\Client\Mutations;

use App\Models\Company\Ecommerce\ProductVariation;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserCart;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class RemoveCartGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createEcommerceClientUser();
    }

    #[Test]
    public function successRemoveCart()
    {
        $variation = ProductVariation::factory()->create();
        $cart = UserCart::factory()->create(['user_id' => $this->_user->id, 'variation_id' => $variation->id]);

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['variation_id' => $cart->variation_id]));

        $this->assertSuccessRemoveCartResponse($response, $variation);
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$variation_id" of required type "Int!" was not provided.'));

        // Rule errors
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['variation_id' => 999999999]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'variation id']), $response->json('errors.0.extensions.validation.variation_id.0'));

        $variation = ProductVariation::factory()->create();
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['variation_id' => $variation->id]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'variation id']), $response->json('errors.0.extensions.validation.variation_id.0'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation RemoveCart($variation_id: Int!) {
                    removeCart(variation_id: $variation_id)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessRemoveCartResponse(TestResponse $response, ProductVariation $variation): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['removeCart']]);
        $this->assertTrue($response->json('data.removeCart'));

        $this->assertDatabaseMissing(UserCart::class, ['user_id' => $this->_user->id, 'variation_id' => $variation->id]);
    }
}
