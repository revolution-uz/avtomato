<?php

namespace Tests\GraphQL\Ecommerce\Admin\Queries;

use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\OrderItem;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\User;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class OrdersGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalOrdersCount = 29;

    private array $_structure = [
        'id',
        'user_id',
        'address_id',
        'status_id',
        'installment_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'user' => [
            'id',
            'phone',
            'customer_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'address' => [
            'id',
            'address',
            'type',
            'region_id',
            'district_id',
            'mahalla_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'status' => [
            'id',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'status_histories' => [
            '*' => [
                'id',
                'from_status_id',
                'to_status_id',
                'admin_id',
                'order_id',
                'created_at',
                'updated_at',
                'deleted_at',
                'from_status' => [
                    'id',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'name_oz',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'to_status' => [
                    'id',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'name_oz',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'admin' => [
                    'id',
                    'username',
                    'first_name',
                    'last_name',
                    'phone',
                    'status',
                    'language',
                    'role_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ],
        'order_items' => [
            '*' => [
                'id',
                'product_id',
                'price',
                'quantity',
                'created_at',
                'updated_at',
                'deleted_at',
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
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Order::query()->forceDelete();
        Order::factory()->count($this->_totalOrdersCount)->create();
    }

    #[Test]
    public function successWithoutArguments(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery());

        $this->assertSuccessOrdersResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['page' => 2]));

        $this->assertSuccessOrdersResponse($response, page: 2);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['per_page' => 25]));

        $this->assertSuccessOrdersResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => 'id']));

        $this->assertSuccessOrdersResponse($response);
        $orderIds = Order::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($orderIds, $response->json('data.orders.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => '-id']));

        $this->assertSuccessOrdersResponse($response);
        $orderIds = Order::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($orderIds, $response->json('data.orders.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        // Search with product name
        /** @var Order $order */
        $order = Order::factory()->create();
        /** @var Product $product */
        $product = Product::factory()->create(['name_en' => 'test product']);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['search' => $product->name_en]));
        $this->assertSuccessOrdersResponse($response, total: 1);
        $this->assertEquals('test product', $response->json('data.orders.data.0.order_items.0.product.name_en'));

        // Search with user phone
        /** @var User $user */
        $user = User::factory()->create(['phone' => '+998901234567']);
        /** @var Order $order */
        $order = Order::factory()->create(['user_id' => $user->id]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['search' => $order->user->phone]));
        $this->assertSuccessOrdersResponse($response, total: 1);
        $this->assertEquals('+998901234567', $response->json('data.orders.data.0.user.phone'));
    }

    #[Test]
    public function errorPage()
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['page' => 'abc']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$page" got invalid value "abc"; Int cannot represent non-integer value: "abc"'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['page' => 0]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    #[Test]
    public function errorPerPage()
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['per_page' => 'abc']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$per_page" got invalid value "abc"; Int cannot represent non-integer value: "abc"'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['per_page' => 0]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    #[Test]
    public function errorSort()
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => 111]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$sort" got invalid value 111; String cannot represent a non string value: 111'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => 'column']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('Invalid sort parameter', $response->json('errors.0.extensions.validation.sort.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query Orders(
                    \$sort: String
                    \$search: String
                    \$page: Int
                    \$per_page: Int
                ) {
                    orders(
                        sort: \$sort
                        search: \$search
                        page: \$page
                        per_page: \$per_page
                    ) {
                        total
                        per_page
                        current_page
                        from
                        to
                        last_page
                        has_more_pages
                        data {
                            {$this->convertIntoQuery($this->_structure)}
                        }
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessOrdersResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'orders' => [
                    'total',
                    'per_page',
                    'current_page',
                    'from',
                    'to',
                    'last_page',
                    'has_more_pages',
                    'data' => [
                        '*' => $this->_structure,
                    ],
                ],
            ],
        ]);
        $totalCount = $total ?? $this->_totalOrdersCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.orders.total'));
        $this->assertEquals($currentPerPage, $response->json('data.orders.per_page'));
        $this->assertEquals($currentPage, $response->json('data.orders.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.orders.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.orders.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.orders.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.orders.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.orders.data'));
    }
}
