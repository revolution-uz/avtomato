<?php

namespace GraphQL\Ecommerce\Client\Mutations;

use App\Models\Company\Ecommerce\ProductVariation;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserCart;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AddCartGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    private array $_structure = [
        'id',
        'quantity',
        'user_id',
        'product_id',
        'variation_id',
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
    }

    #[Test]
    public function successAddCart()
    {
        $variables = [
            'variation_id' => ProductVariation::factory()->create()->id,
            'quantity' => 1,
        ];

        $response = $this
            ->actingAs($this->_user)
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessAddCartResponse($response, $variables);
    }

    #[Test]
    public function successUpdateExistingCart()
    {
        $cart = UserCart::factory()->create(['user_id' => $this->_user->id]);

        $variables = array_merge($cart->toArray(), ['quantity' => 999]);

        $response = $this
            ->actingAs($this->_user)
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessAddCartResponse($response, $variables);
        $cart->refresh();
        $this->assertEquals($variables['quantity'], $cart->quantity);
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$variation_id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$quantity" of required type "Int!" was not provided.'));

        // Rule errors
        $response = $this
            ->actingAs($this->_user)
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery(['variation_id' => 999999999, 'quantity' => 0]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'variation id']), $response->json('errors.0.extensions.validation.variation_id.0'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'quantity', 'min' => 1]), $response->json('errors.0.extensions.validation.quantity.0'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation AddCart(\$variation_id: Int!, \$quantity: Int!) {
                    addCart(variation_id: \$variation_id, quantity: \$quantity) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessAddCartResponse(TestResponse $response, array $variables): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['addCart' => $this->_structure]]);

        $cart = $this->_user->carts()->first();
        $this->assertNotNull($cart);
        $this->assertEquals($variables['variation_id'], $cart->variation_id);
        $this->assertEquals($variables['quantity'], $cart->quantity);
        $this->assertEquals($cart->product_id, $cart->variation->product_id);

        $this->assertEquals($cart->id, $response->json('data.addCart.id'));
        $this->assertEquals($cart->quantity, $response->json('data.addCart.quantity'));
        $this->assertEquals($cart->user_id, $response->json('data.addCart.user_id'));
        $this->assertEquals($cart->product_id, $response->json('data.addCart.product_id'));
        $this->assertEquals($cart->variation_id, $response->json('data.addCart.variation_id'));

        // Product
        $this->assertEquals($cart->product->id, $response->json('data.addCart.product.id'));
        $this->assertEquals($cart->product->name, $response->json('data.addCart.product.name'));
        $this->assertEquals($cart->product->name_uz, $response->json('data.addCart.product.name_uz'));
        $this->assertEquals($cart->product->name_ru, $response->json('data.addCart.product.name_ru'));
        $this->assertEquals($cart->product->name_en, $response->json('data.addCart.product.name_en'));
        $this->assertEquals($cart->product->name_oz, $response->json('data.addCart.product.name_oz'));
        $this->assertEquals($cart->product->slug, $response->json('data.addCart.product.slug'));
        $this->assertEquals($cart->product->cost, $response->json('data.addCart.product.cost'));
        $this->assertEquals($cart->product->actual_price, $response->json('data.addCart.product.actual_price'));
        $this->assertEquals($cart->product->old_price, $response->json('data.addCart.product.old_price'));
        $this->assertEquals($cart->product->is_active, $response->json('data.addCart.product.is_active'));
        $this->assertEquals($cart->product->category_id, $response->json('data.addCart.product.category_id'));
        $this->assertEquals($cart->product->brand_id, $response->json('data.addCart.product.brand_id'));
        $this->assertEquals($cart->product->created_at->timestamp, $response->json('data.addCart.product.created_at'));
        $this->assertEquals($cart->product->updated_at->timestamp, $response->json('data.addCart.product.updated_at'));
        $this->assertEquals($cart->product->deleted_at?->timestamp, $response->json('data.addCart.product.deleted_at'));

        // Variation
        $this->assertEquals($cart->variation->id, $response->json('data.addCart.variation.id'));
        $this->assertEquals($cart->variation->is_active, $response->json('data.addCart.variation.is_active'));
        $this->assertEquals($cart->variation->cost, $response->json('data.addCart.variation.cost'));
        $this->assertEquals($cart->variation->actual_price, $response->json('data.addCart.variation.actual_price'));
        $this->assertEquals($cart->variation->old_price, $response->json('data.addCart.variation.old_price'));
        $this->assertEquals($cart->variation->product_id, $response->json('data.addCart.variation.product_id'));
        $this->assertEquals($cart->variation->created_at->timestamp, $response->json('data.addCart.variation.created_at'));
        $this->assertEquals($cart->variation->updated_at->timestamp, $response->json('data.addCart.variation.updated_at'));
        $this->assertEquals($cart->variation->deleted_at?->timestamp, $response->json('data.addCart.variation.deleted_at'));
    }
}
