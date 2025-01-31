<?php

namespace Tests\GraphQL\Ecommerce\Client\Queries;

use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserCart;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UserCartsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    private int $_totalUserCartsCount = 5;

    private array $_structure = [
        'id',
        'quantity',
        'user_id',
        'product_id',
        'variation_id',
        'user' => [
            'id',
            'phone',
            'customer_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'product' => [
            'id',
            'name',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'slug',
            'cost',
            'actual_price',
            'old_price',
            'is_active',
            'category_id',
            'brand_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'variation' => [
            'id',
            'is_active',
            'cost',
            'actual_price',
            'old_price',
            'product_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createEcommerceClientUser();

        UserCart::query()->forceDelete();
        UserCart::factory()->count($this->_totalUserCartsCount)->create(['user_id' => $this->_user->id]);
    }

    #[Test]
    public function successUserCarts(): void
    {
        $response = $this
            ->actingAs($this->_user)
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery());

        $this->assertSuccessUserCartsResponse($response);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query UserCarts {
                    userCarts {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessUserCartsResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['userCarts' => ['*' => $this->_structure]]]);

        $this->assertCount($this->_totalUserCartsCount, $response->json('data.userCarts'));

        $userCart = $this->_user->carts()->orderBy('id', 'desc')->first();

        $this->assertEquals($userCart->id, $response->json('data.userCarts.0.id'));
        $this->assertEquals($userCart->quantity, $response->json('data.userCarts.0.quantity'));
        $this->assertEquals($userCart->user_id, $response->json('data.userCarts.0.user_id'));
        $this->assertEquals($userCart->product_id, $response->json('data.userCarts.0.product_id'));
        $this->assertEquals($userCart->variation_id, $response->json('data.userCarts.0.variation_id'));

        // user
        $this->assertEquals($userCart->user->id, $response->json('data.userCarts.0.user.id'));
        $this->assertEquals($userCart->user->phone, $response->json('data.userCarts.0.user.phone'));
        $this->assertEquals($userCart->user->customer_id, $response->json('data.userCarts.0.user.customer_id'));
        $this->assertEquals($userCart->user->created_at->timestamp, $response->json('data.userCarts.0.user.created_at'));
        $this->assertEquals($userCart->user->updated_at->timestamp, $response->json('data.userCarts.0.user.updated_at'));
        $this->assertEquals($userCart->user->deleted_at?->timestamp, $response->json('data.userCarts.0.user.deleted_at'));

        // product
        $this->assertEquals($userCart->product->id, $response->json('data.userCarts.0.product.id'));
        $this->assertEquals($userCart->product->name, $response->json('data.userCarts.0.product.name'));
        $this->assertEquals($userCart->product->name_uz, $response->json('data.userCarts.0.product.name_uz'));
        $this->assertEquals($userCart->product->name_ru, $response->json('data.userCarts.0.product.name_ru'));
        $this->assertEquals($userCart->product->name_en, $response->json('data.userCarts.0.product.name_en'));
        $this->assertEquals($userCart->product->name_oz, $response->json('data.userCarts.0.product.name_oz'));
        $this->assertEquals($userCart->product->slug, $response->json('data.userCarts.0.product.slug'));
        $this->assertEquals($userCart->product->cost, $response->json('data.userCarts.0.product.cost'));
        $this->assertEquals($userCart->product->actual_price, $response->json('data.userCarts.0.product.actual_price'));
        $this->assertEquals($userCart->product->old_price, $response->json('data.userCarts.0.product.old_price'));
        $this->assertEquals($userCart->product->is_active, $response->json('data.userCarts.0.product.is_active'));
        $this->assertEquals($userCart->product->category_id, $response->json('data.userCarts.0.product.category_id'));
        $this->assertEquals($userCart->product->brand_id, $response->json('data.userCarts.0.product.brand_id'));
        $this->assertEquals($userCart->product->created_at->timestamp, $response->json('data.userCarts.0.product.created_at'));
        $this->assertEquals($userCart->product->updated_at->timestamp, $response->json('data.userCarts.0.product.updated_at'));
        $this->assertEquals($userCart->product->deleted_at?->timestamp, $response->json('data.userCarts.0.product.deleted_at'));

        // variation
        $this->assertEquals($userCart->variation->id, $response->json('data.userCarts.0.variation.id'));
        $this->assertEquals($userCart->variation->is_active, $response->json('data.userCarts.0.variation.is_active'));
        $this->assertEquals($userCart->variation->cost, $response->json('data.userCarts.0.variation.cost'));
        $this->assertEquals($userCart->variation->actual_price, $response->json('data.userCarts.0.variation.actual_price'));
        $this->assertEquals($userCart->variation->old_price, $response->json('data.userCarts.0.variation.old_price'));
        $this->assertEquals($userCart->variation->product_id, $response->json('data.userCarts.0.variation.product_id'));
        $this->assertEquals($userCart->variation->created_at->timestamp, $response->json('data.userCarts.0.variation.created_at'));
        $this->assertEquals($userCart->variation->updated_at->timestamp, $response->json('data.userCarts.0.variation.updated_at'));
        $this->assertEquals($userCart->variation->deleted_at?->timestamp, $response->json('data.userCarts.0.variation.deleted_at'));
    }
}
