<?php

namespace Tests\GraphQL\Ecommerce\Admin\Queries;

use App\Models\Company\Ecommerce\ProductOption;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ProductOptionsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalOptionsCount = 29;

    private array $_structure = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
        'values' => [
            '*' => [
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
            ],
        ],
        'categories' => [
            '*' => [
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
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        ProductOption::query()->forceDelete();
        ProductOption::factory()->count($this->_totalOptionsCount)->create();
    }

    #[Test]
    public function successWithoutArguments(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery());

        $this->assertSuccessProductOptionsResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['page' => 2]));

        $this->assertSuccessProductOptionsResponse($response, page: 2);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['per_page' => 25]));

        $this->assertSuccessProductOptionsResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => 'id']));

        $this->assertSuccessProductOptionsResponse($response);
        $productOptionIds = ProductOption::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($productOptionIds, $response->json('data.productOptions.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => '-id']));

        $this->assertSuccessProductOptionsResponse($response);
        $productOptionIds = ProductOption::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($productOptionIds, $response->json('data.productOptions.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        /** @var ProductOption $productOption */
        $productOption = ProductOption::factory()->create(['name_uz' => 'Test Product Option']);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['search' => $productOption->name_uz]));

        $this->assertSuccessProductOptionsResponse($response, total: 1);
        $this->assertEquals('Test Product Option', $response->json('data.productOptions.data.0.name_uz'));
    }

    #[Test]
    public function errorPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['page' => 'asnjfasnjfasn']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(
            'Variable "$page" got invalid value "asnjfasnjfasn"; Int cannot represent non-integer value: "asnjfasnjfasn"',
            $response->json('errors.0.message')
        );

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['page' => 0]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]),
            $response->json('errors.0.extensions.validation.page.0')
        );
    }

    #[Test]
    public function errorPerPage()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['per_page' => 'asnjfasnjfasn']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(
            'Variable "$per_page" got invalid value "asnjfasnjfasn"; Int cannot represent non-integer value: "asnjfasnjfasn"',
            $response->json('errors.0.message')
        );

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['per_page' => 0]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]),
            $response->json('errors.0.extensions.validation.per_page.0')
        );

        // maximum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['per_page' => 101]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]),
            $response->json('errors.0.extensions.validation.per_page.0')
        );
    }

    #[Test]
    public function errorSort()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['sort' => 111]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(
            'Variable "$sort" got invalid value 111; String cannot represent a non string value: 111',
            $response->json('errors.0.message')
        );

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
                query ProductOptions(
                    \$sort: String
                    \$search: String
                    \$page: Int
                    \$per_page: Int
                ) {
                    productOptions(
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

    private function assertSuccessProductOptionsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'productOptions' => [
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
        $totalCount = $total ?? $this->_totalOptionsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.productOptions.total'));
        $this->assertEquals($currentPerPage, $response->json('data.productOptions.per_page'));
        $this->assertEquals($currentPage, $response->json('data.productOptions.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.productOptions.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.productOptions.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.productOptions.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.productOptions.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.productOptions.data'));
    }
}
