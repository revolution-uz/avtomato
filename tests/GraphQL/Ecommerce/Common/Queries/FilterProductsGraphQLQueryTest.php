<?php

namespace Tests\GraphQL\Ecommerce\Common\Queries;

use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductBrand;
use App\Models\Company\Ecommerce\ProductCategory;
use App\Models\Company\Ecommerce\ProductJoinOption;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class FilterProductsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalProductsCount = 29;

    private array $_structure = [
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
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Product::query()->forceDelete();
        Product::factory()->count($this->_totalProductsCount)->create();
    }

    #[Test]
    public function successWithoutArguments(): void
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery()
        );

        $this->assertSuccessFilterProductsResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['page' => 2])
        );

        $this->assertSuccessFilterProductsResponse($response, page: 2);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['per_page' => 25])
        );

        $this->assertSuccessFilterProductsResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['sort' => 'id'])
        );

        $this->assertSuccessFilterProductsResponse($response);
        $productIds = Product::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($productIds, $response->json('data.filterProducts.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['sort' => '-id'])
        );

        $this->assertSuccessFilterProductsResponse($response);
        $productIds = Product::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($productIds, $response->json('data.filterProducts.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        /** @var Product $product */
        $product = Product::factory()->create(['name_uz' => 'test product']);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['search' => $product->name_uz])
        );

        $this->assertSuccessFilterProductsResponse($response, total: 1);
        $this->assertEquals('test product', $response->json('data.filterProducts.data.0.name_uz'));
    }

    #[Test]
    public function successFilterWithCategoryId()
    {
        $count = random_int(1, 10);
        $category = ProductCategory::factory()->create();
        Product::factory()->count($count)->create(['category_id' => $category->id]);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['category_id' => $category->id])
        );

        $this->assertSuccessFilterProductsResponse($response, $count);
        $productIds = $category->products()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($productIds, $response->json('data.filterProducts.data.*.id'));
    }

    #[Test]
    public function successFilterWithBrandId()
    {
        $count = random_int(1, 10);
        $brand = ProductBrand::factory()->create();
        Product::factory()->count($count)->create(['brand_id' => $brand->id]);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['brand_id' => $brand->id])
        );

        $this->assertSuccessFilterProductsResponse($response, $count);
        $productIds = $brand->products()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($productIds, $response->json('data.filterProducts.data.*.id'));
    }

    #[Test]
    public function successFilterWithMinPrice()
    {
        $min_price = 100000;
        Product::factory()->count(random_int(1, 10))->create(['actual_price' => $min_price]);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['min_price' => $min_price])
        );

        $productIds = Product::query()->where('actual_price', '>=', $min_price)->orderByDesc('id')->pluck('id');
        $this->assertSuccessFilterProductsResponse($response, $productIds->count());
        $this->assertEquals($productIds->take(10)->toArray(), $response->json('data.filterProducts.data.*.id'));
    }

    #[Test]
    public function successFilterWithMaxPrice()
    {
        $max_price = 100000;
        Product::factory()->count(random_int(1, 10))->create(['actual_price' => $max_price]);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['max_price' => $max_price])
        );

        $productIds = Product::query()->where('actual_price', '<=', $max_price)->orderByDesc('id')->pluck('id');
        $this->assertSuccessFilterProductsResponse($response, $productIds->count());
        $this->assertEquals($productIds->take(10)->toArray(), $response->json('data.filterProducts.data.*.id'));
    }

    #[Test]
    public function successFilterWithOptionValues()
    {
        $count = random_int(1, 10);
        $productJoinOptions = ProductJoinOption::factory()->count($count)->create();

        $response = $this->postJson(
            route('graphql.ecommerce-common'),
            $this->buildGraphQLQuery(['option_values' => $productJoinOptions->pluck('product_variation_id')])
        );

        $this->assertSuccessFilterProductsResponse($response, $count);
        $productIds = $productJoinOptions->sortByDesc('product_id')->pluck('product_id')->toArray();
        $this->assertEquals($productIds, $response->json('data.filterProducts.data.*.id'));
    }

    #[Test]
    public function errorPage()
    {
        // GraphQL type errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['page' => 'abc'])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$page" got invalid value "abc"; Int cannot represent non-integer value: "abc"'));

        // Rule errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['page' => 0])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    #[Test]
    public function errorPerPage()
    {
        // GraphQL type errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['per_page' => 'abc'])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$per_page" got invalid value "abc"; Int cannot represent non-integer value: "abc"'));

        // Rule errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['per_page' => 0])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    #[Test]
    public function errorSort()
    {
        // GraphQL type errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['sort' => 111])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$sort" got invalid value 111; String cannot represent a non string value: 111'));

        // Rule errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['sort' => 'column'])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('Invalid sort parameter', $response->json('errors.0.extensions.validation.sort.0'));
    }

    #[Test]
    public function errorFilter()
    {
        // GraphQL type errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'),
            $this->buildGraphQLQuery(['category_id' => 'abc', 'brand_id' => 'abc', 'min_price' => 'abc', 'max_price' => 'abc', 'option_values' => ['abc']])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$category_id" got invalid value "abc"; Int cannot represent non-integer value: "abc"'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$brand_id" got invalid value "abc"; Int cannot represent non-integer value: "abc"'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$min_price" got invalid value "abc"; Float cannot represent non numeric value: "abc"'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$max_price" got invalid value "abc"; Float cannot represent non numeric value: "abc"'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$option_values" got invalid value "abc" at "option_values[0]"; Int cannot represent non-integer value: "abc"'));

        // Rule errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'),
            $this->buildGraphQLQuery(['category_id' => 999999999, 'brand_id' => 999999999, 'option_values' => [999999999]])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'filter.category id']), $response->json('errors.0.extensions.validation')['filter.category_id'][0]);
        $this->assertEquals(__('validation.exists', ['attribute' => 'filter.brand id']), $response->json('errors.0.extensions.validation')['filter.brand_id'][0]);
        $this->assertEquals(__('validation.exists', ['attribute' => 'filter.option_values.0']), $response->json('errors.0.extensions.validation')['filter.option_values.0'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query FilterProducts(
                    \$sort: String
                    \$search: String
                    \$page: Int
                    \$per_page: Int
                    \$category_id: Int
                    \$brand_id: Int
                    \$min_price: Float
                    \$max_price: Float
                    \$option_values: [Int!]
                ) {
                    filterProducts(
                        sort: \$sort
                        search: \$search
                        page: \$page
                        per_page: \$per_page
                        filter: {
                            category_id: \$category_id
                            brand_id: \$brand_id
                            min_price: \$min_price
                            max_price: \$max_price
                            option_values: \$option_values
                        }
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

    private function assertSuccessFilterProductsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'filterProducts' => [
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
        $totalCount = $total ?? $this->_totalProductsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.filterProducts.total'));
        $this->assertEquals($currentPerPage, $response->json('data.filterProducts.per_page'));
        $this->assertEquals($currentPage, $response->json('data.filterProducts.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.filterProducts.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.filterProducts.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.filterProducts.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.filterProducts.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.filterProducts.data'));
    }
}
