<?php

namespace Tests\GraphQL\Ecommerce\Admin\Queries;

use App\Models\Company\Ecommerce\User;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UsersGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalUsersCount = 29;

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

    protected function setUp(): void
    {
        parent::setUp();

        User::query()->forceDelete();
        User::factory()->count($this->_totalUsersCount)->create();
    }

    #[Test]
    public function successWithoutArguments(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery());

        $this->assertSuccessUsersResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['page' => 2]));

        $this->assertSuccessUsersResponse($response, page: 2);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['per_page' => 25]));

        $this->assertSuccessUsersResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => 'id']));

        $this->assertSuccessUsersResponse($response);
        $userIds = User::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($userIds, $response->json('data.users.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => '-id']));

        $this->assertSuccessUsersResponse($response);
        $userIds = User::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($userIds, $response->json('data.users.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        /** @var User $user */
        $user = User::factory()->create(['phone' => '+998901234567']);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['search' => $user->phone]));

        $this->assertSuccessUsersResponse($response, total: 1);
        $this->assertEquals('+998901234567', $response->json('data.users.data.0.phone'));
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
                query Users(
                    \$sort: String
                    \$search: String
                    \$page: Int
                    \$per_page: Int
                ) {
                    users(
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

    private function assertSuccessUsersResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'users' => [
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
        $totalCount = $total ?? $this->_totalUsersCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.users.total'));
        $this->assertEquals($currentPerPage, $response->json('data.users.per_page'));
        $this->assertEquals($currentPage, $response->json('data.users.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.users.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.users.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.users.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.users.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.users.data'));
    }
}
