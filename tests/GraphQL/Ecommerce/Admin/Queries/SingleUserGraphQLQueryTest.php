<?php

namespace Tests\GraphQL\Ecommerce\Admin\Queries;

use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserCart;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleUserGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'phone',
        'customer_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'orders' => [
            '*' => [
                'id',
                'user_id',
                'address_id',
                'status_id',
                'installment_id',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
        'carts' => [
            '*' => [
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
                    'product_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ],
        'favourites' => [
            '*' => [
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
                'category' => [
                    'id',
                    'parent_id',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'name_oz',
                    'is_active',
                    'image_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'brand' => [
                    'id',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'name_oz',
                    'slug',
                    'is_active',
                    'image_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'images' => [
                    '*' => [
                        'id',
                        'url',
                        'original_name',
                        'mime',
                        'order',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
                'options' => [
                    '*' => [
                        'id',
                        'name_uz',
                        'name_ru',
                        'name_en',
                        'name_oz',
                        'slug',
                        'type',
                        'description',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
                'variations' => [
                    '*' => [
                        'id',
                        'is_active',
                        'product_id',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
            ],
        ],
    ];

    #[Test]
    public function successSingleUser()
    {
        /** @var User $user */
        $user = User::factory()->withCustomer()->create();

        Order::factory()->count(3)->create(['user_id' => $user->id]);
        UserCart::factory()->count(3)->create(['user_id' => $user->id]);
        $user->favourites()->attach(Product::factory()->count(3)->create());

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => $user->id]));

        $this->assertSuccessSingleUserResponse($response, $user);
    }

    #[Test]
    public function successSingleUserWithNullableFields()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => $user->id]));

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['singleUser' => $this->_structure]]);

        $this->assertEquals($user->id, $response->json('data.singleUser.id'));
        $this->assertEquals($user->phone, $response->json('data.singleUser.phone'));
        $this->assertNull($response->json('data.singleUser.customer_id'));
        $this->assertEquals($user->created_at->timestamp, $response->json('data.singleUser.created_at'));
        $this->assertEquals($user->updated_at->timestamp, $response->json('data.singleUser.updated_at'));
        $this->assertEquals($user->deleted_at?->timestamp, $response->json('data.singleUser.deleted_at'));
        $this->assertEquals(0, count($response->json('data.singleUser.orders')));
        $this->assertEquals(0, count($response->json('data.singleUser.carts')));
        $this->assertEquals(0, count($response->json('data.singleUser.favourites')));
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type error
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => null]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$id" of non-null type "Int!" must not be null.', $response->json('errors.0.message'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => 999999999]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query SingleUser(\$id: Int!) {
                    singleUser(id: \$id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSingleUserResponse(TestResponse $response, User $user): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['singleUser' => $this->_structure]]);

        $this->assertEquals($user->id, $response->json('data.singleUser.id'));
        $this->assertEquals($user->phone, $response->json('data.singleUser.phone'));
        $this->assertEquals($user->customer_id, $response->json('data.singleUser.customer_id'));
        $this->assertEquals($user->created_at->timestamp, $response->json('data.singleUser.created_at'));
        $this->assertEquals($user->updated_at->timestamp, $response->json('data.singleUser.updated_at'));
        $this->assertEquals($user->deleted_at?->timestamp, $response->json('data.singleUser.deleted_at'));

        // orders
        $this->assertEquals($user->orders()->count(), count($response->json('data.singleUser.orders')));
        /** @var Order $order */
        $order = $user->orders->first();
        $this->assertEquals($order->id, $response->json('data.singleUser.orders.0.id'));
        $this->assertEquals($order->user_id, $response->json('data.singleUser.orders.0.user_id'));
        $this->assertEquals($order->address_id, $response->json('data.singleUser.orders.0.address_id'));
        $this->assertEquals($order->status_id, $response->json('data.singleUser.orders.0.status_id'));
        $this->assertEquals($order->installment_id, $response->json('data.singleUser.orders.0.installment_id'));
        $this->assertEquals($order->created_at->timestamp, $response->json('data.singleUser.orders.0.created_at'));
        $this->assertEquals($order->updated_at->timestamp, $response->json('data.singleUser.orders.0.updated_at'));
        $this->assertEquals($order->deleted_at?->timestamp, $response->json('data.singleUser.orders.0.deleted_at'));

        // carts
        $this->assertEquals($user->carts()->count(), count($response->json('data.singleUser.carts')));
        /** @var UserCart $cart */
        $cart = $user->carts->first();
        $this->assertEquals($cart->id, $response->json('data.singleUser.carts.0.id'));
        $this->assertEquals($cart->quantity, $response->json('data.singleUser.carts.0.quantity'));
        $this->assertEquals($cart->user_id, $response->json('data.singleUser.carts.0.user_id'));
        $this->assertEquals($cart->product_id, $response->json('data.singleUser.carts.0.product_id'));
        $this->assertEquals($cart->variation_id, $response->json('data.singleUser.carts.0.variation_id'));

        // carts -> user
        $this->assertEquals($cart->user->id, $response->json('data.singleUser.carts.0.user.id'));
        $this->assertEquals($cart->user->phone, $response->json('data.singleUser.carts.0.user.phone'));
        $this->assertEquals($cart->user->customer_id, $response->json('data.singleUser.carts.0.user.customer_id'));
        $this->assertEquals($cart->user->created_at->timestamp, $response->json('data.singleUser.carts.0.user.created_at'));
        $this->assertEquals($cart->user->updated_at->timestamp, $response->json('data.singleUser.carts.0.user.updated_at'));
        $this->assertEquals($cart->user->deleted_at?->timestamp, $response->json('data.singleUser.carts.0.user.deleted_at'));

        // carts -> product
        $this->assertEquals($cart->product->id, $response->json('data.singleUser.carts.0.product.id'));
        $this->assertEquals($cart->product->name, $response->json('data.singleUser.carts.0.product.name'));
        $this->assertEquals($cart->product->name_uz, $response->json('data.singleUser.carts.0.product.name_uz'));
        $this->assertEquals($cart->product->name_ru, $response->json('data.singleUser.carts.0.product.name_ru'));
        $this->assertEquals($cart->product->name_en, $response->json('data.singleUser.carts.0.product.name_en'));
        $this->assertEquals($cart->product->name_oz, $response->json('data.singleUser.carts.0.product.name_oz'));
        $this->assertEquals($cart->product->slug, $response->json('data.singleUser.carts.0.product.slug'));
        $this->assertEquals($cart->product->cost, $response->json('data.singleUser.carts.0.product.cost'));
        $this->assertEquals($cart->product->actual_price, $response->json('data.singleUser.carts.0.product.actual_price'));
        $this->assertEquals($cart->product->old_price, $response->json('data.singleUser.carts.0.product.old_price'));
        $this->assertEquals($cart->product->is_active, $response->json('data.singleUser.carts.0.product.is_active'));
        $this->assertEquals($cart->product->category_id, $response->json('data.singleUser.carts.0.product.category_id'));
        $this->assertEquals($cart->product->brand_id, $response->json('data.singleUser.carts.0.product.brand_id'));
        $this->assertEquals($cart->product->created_at->timestamp, $response->json('data.singleUser.carts.0.product.created_at'));
        $this->assertEquals($cart->product->updated_at->timestamp, $response->json('data.singleUser.carts.0.product.updated_at'));
        $this->assertEquals($cart->product->deleted_at?->timestamp, $response->json('data.singleUser.carts.0.product.deleted_at'));

        // carts -> variation
        $this->assertEquals($cart->variation->id, $response->json('data.singleUser.carts.0.variation.id'));
        $this->assertEquals($cart->variation->is_active, $response->json('data.singleUser.carts.0.variation.is_active'));
        $this->assertEquals($cart->variation->product_id, $response->json('data.singleUser.carts.0.variation.product_id'));
        $this->assertEquals($cart->variation->created_at->timestamp, $response->json('data.singleUser.carts.0.variation.created_at'));
        $this->assertEquals($cart->variation->updated_at->timestamp, $response->json('data.singleUser.carts.0.variation.updated_at'));
        $this->assertEquals($cart->variation->deleted_at?->timestamp, $response->json('data.singleUser.carts.0.variation.deleted_at'));

        // favourites
        $this->assertEquals($user->favourites()->count(), count($response->json('data.singleUser.favourites')));
        /** @var Product $favouriteProduct */
        $favouriteProduct = $user->favourites->first();
        $this->assertEquals($favouriteProduct->id, $response->json('data.singleUser.favourites.0.id'));
        $this->assertEquals($favouriteProduct->name, $response->json('data.singleUser.favourites.0.name'));
        $this->assertEquals($favouriteProduct->name_uz, $response->json('data.singleUser.favourites.0.name_uz'));
        $this->assertEquals($favouriteProduct->name_ru, $response->json('data.singleUser.favourites.0.name_ru'));
        $this->assertEquals($favouriteProduct->name_en, $response->json('data.singleUser.favourites.0.name_en'));
        $this->assertEquals($favouriteProduct->name_oz, $response->json('data.singleUser.favourites.0.name_oz'));
        $this->assertEquals($favouriteProduct->slug, $response->json('data.singleUser.favourites.0.slug'));
        $this->assertEquals($favouriteProduct->cost, $response->json('data.singleUser.favourites.0.cost'));
        $this->assertEquals($favouriteProduct->actual_price, $response->json('data.singleUser.favourites.0.actual_price'));
        $this->assertEquals($favouriteProduct->old_price, $response->json('data.singleUser.favourites.0.old_price'));
        $this->assertEquals($favouriteProduct->is_active, $response->json('data.singleUser.favourites.0.is_active'));
        $this->assertEquals($favouriteProduct->category_id, $response->json('data.singleUser.favourites.0.category_id'));
        $this->assertEquals($favouriteProduct->brand_id, $response->json('data.singleUser.favourites.0.brand_id'));
        $this->assertEquals($favouriteProduct->created_at->timestamp, $response->json('data.singleUser.favourites.0.created_at'));
        $this->assertEquals($favouriteProduct->updated_at->timestamp, $response->json('data.singleUser.favourites.0.updated_at'));
        $this->assertEquals($favouriteProduct->deleted_at?->timestamp, $response->json('data.singleUser.favourites.0.deleted_at'));
    }
}
