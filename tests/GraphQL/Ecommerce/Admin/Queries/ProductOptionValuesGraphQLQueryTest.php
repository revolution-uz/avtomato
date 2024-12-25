<?php

namespace Tests\GraphQL\Ecommerce\Admin\Queries;

use App\Models\Company\Ecommerce\ProductOptionValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ProductOptionValuesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalValuesCount = 29;

    private array $_structure = [
        'id',
        'value_uz',
        'value_ru',
        'value_en',
        'value_oz',
        'slug',
        'option_id',
        'image_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'option' => [
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
        'image' => [
            'id',
            'url',
            'original_name',
            'mime',
            'order',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        ProductOptionValue::query()->forceDelete();
        ProductOptionValue::factory()->count($this->_totalValuesCount)->create();
    }

    #[Test]
    public function successWithoutArguments(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery());

        $this->assertSuccessProductOptionValuesResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['page' => 2]));

        $this->assertSuccessProductOptionValuesResponse($response, page: 2);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['per_page' => 25]));

        $this->assertSuccessProductOptionValuesResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => 'id']));

        $this->assertSuccessProductOptionValuesResponse($response);
        $productOptionValueIds = ProductOptionValue::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($productOptionValueIds, $response->json('data.productOptionValues.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => '-id']));

        $this->assertSuccessProductOptionValuesResponse($response);
        $productOptionValueIds = ProductOptionValue::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($productOptionValueIds, $response->json('data.productOptionValues.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        /** @var ProductOptionValue $value */
        $value = ProductOptionValue::factory()->create(['value_uz' => Str::random(20)]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['search' => $value->value_uz]));

        $this->assertSuccessProductOptionValuesResponse($response, total: 1);
        $this->assertEquals($value->value_uz, $response->json('data.productOptionValues.data.0.value_uz'));
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
                query ProductOptionValues(
                    \$sort: String
                    \$search: String
                    \$page: Int
                    \$per_page: Int
                ) {
                    productOptionValues(
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

    private function assertSuccessProductOptionValuesResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'productOptionValues' => [
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
        $totalCount = $total ?? $this->_totalValuesCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.productOptionValues.total'));
        $this->assertEquals($currentPerPage, $response->json('data.productOptionValues.per_page'));
        $this->assertEquals($currentPage, $response->json('data.productOptionValues.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.productOptionValues.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.productOptionValues.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.productOptionValues.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.productOptionValues.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.productOptionValues.data'));
    }
}
