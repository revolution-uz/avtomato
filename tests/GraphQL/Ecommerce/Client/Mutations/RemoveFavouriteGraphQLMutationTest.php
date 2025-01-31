<?php

namespace Tests\GraphQL\Ecommerce\Client\Mutations;

use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserFavourite;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class RemoveFavouriteGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createEcommerceClientUser();
    }

    #[Test]
    public function successRemoveFavourite()
    {
        $product = Product::factory()->create();
        $userFavorite = UserFavourite::query()->create(['user_id' => $this->_user->id, 'product_id' => $product->id]);

        $response = $this
            ->actingAs($this->_user)
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['product_id' => $userFavorite->product_id]));

        $this->assertSuccessRemoveFavouriteResponse($response, $product);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->_user)
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery());
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$product_id" of required type "Int!" was not provided.'));

        // Rule errors
        $response = $this
            ->actingAs($this->_user)
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['product_id' => 999999999]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product id']), $response->json('errors.0.extensions.validation.product_id.0'));

        $product = Product::factory()->create();
        $response = $this
            ->actingAs($this->_user)
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['product_id' => $product->id]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('The product_id is not in your favourite list.', $response->json('errors.0.extensions.validation.product_id.0'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation RemoveFavourite($product_id: Int!) {
                    removeFavourite(product_id: $product_id)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessRemoveFavouriteResponse(TestResponse $response, Product $product): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['removeFavourite']]);
        $this->assertTrue($response->json('data.removeFavourite'));

        $this->assertDatabaseMissing(UserFavourite::class, ['user_id' => $this->_user->id, 'product_id' => $product->id]);
    }
}
