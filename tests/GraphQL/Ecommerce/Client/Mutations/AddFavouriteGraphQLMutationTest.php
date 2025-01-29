<?php

namespace GraphQL\Ecommerce\Client\Mutations;

use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserFavourite;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AddFavouriteGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createEcommerceClientUser();
        UserFavourite::query()->forceDelete();
    }

    #[Test]
    public function successAddFavourite()
    {
        $product = Product::factory()->create();

        $response = $this
            ->actingAs($this->_user)
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['product_id' => $product->id]));

        $this->assertSuccessAddFavouriteResponse($response, $product);
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

        $userFavourite = UserFavourite::factory()->create(['user_id' => $this->_user->id]);
        $response = $this
            ->actingAs($this->_user)
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['product_id' => $userFavourite->product_id]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('The product_id has already been taken.', $response->json('errors.0.extensions.validation.product_id.0'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation AddFavourite($product_id: Int!) {
                    addFavourite(product_id: $product_id)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessAddFavouriteResponse(TestResponse $response, Product $product): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['addFavourite']]);
        $this->assertTrue($response->json('data.addFavourite'));

        $this->assertDatabaseHas(UserFavourite::class, ['user_id' => $this->_user->id, 'product_id' => $product->id]);
    }
}
