<?php

namespace Tests\GraphQL\Ecommerce\Common\Queries;

use App\Models\Company\Ecommerce\DeliveryPoint;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeliveryPointsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalDeliveryPointsCount = 29;

    private array $_structure = [
        'id',
        'name',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'description',
        'description_uz',
        'description_ru',
        'description_en',
        'description_oz',
        'is_prepaid',
        'phone',
        'address',
        'address_uz',
        'address_ru',
        'address_en',
        'address_oz',
        'landmark',
        'landmark_uz',
        'landmark_ru',
        'landmark_en',
        'landmark_oz',
        'location_url',
        'lat',
        'lng',
        'region_id',
        'district_id',
        'mahalla_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'region' => [
            'id',
            'name',
            'name_uz',
            'name_oz',
            'name_ru',
            'name_en',
        ],
        'district' => [
            'id',
            'name',
            'name_uz',
            'name_oz',
            'name_ru',
            'name_en',
            'region_id',
        ],
        'mahalla' => [
            'id',
            'name',
            'name_uz',
            'name_oz',
            'name_ru',
            'name_en',
            'district_id',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        DeliveryPoint::query()->forceDelete();
        DeliveryPoint::factory()->count($this->_totalDeliveryPointsCount)->create();
    }

    #[Test]
    public function successWithoutArguments(): void
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery()
        );

        $this->assertSuccessDeliveryPointsResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['page' => 2])
        );

        $this->assertSuccessDeliveryPointsResponse($response, page: 2);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['per_page' => 25])
        );

        $this->assertSuccessDeliveryPointsResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['sort' => 'id'])
        );

        $this->assertSuccessDeliveryPointsResponse($response);
        $deliveryPointIds = DeliveryPoint::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($deliveryPointIds, $response->json('data.deliveryPoints.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['sort' => '-id'])
        );

        $this->assertSuccessDeliveryPointsResponse($response);
        $deliveryPointIds = DeliveryPoint::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($deliveryPointIds, $response->json('data.deliveryPoints.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        /** @var DeliveryPoint $deliveryPoint */
        $deliveryPoint = DeliveryPoint::factory()->create(['name_uz' => 'test deliveryPoint']);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['search' => $deliveryPoint->name_uz])
        );

        $this->assertSuccessDeliveryPointsResponse($response, total: 1);
        $this->assertEquals('test deliveryPoint', $response->json('data.deliveryPoints.data.0.name_uz'));
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

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query DeliveryPoints(
                    \$sort: String
                    \$search: String
                    \$page: Int
                    \$per_page: Int
                ) {
                    deliveryPoints(
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

    private function assertSuccessDeliveryPointsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'deliveryPoints' => [
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
        $totalCount = $total ?? $this->_totalDeliveryPointsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.deliveryPoints.total'));
        $this->assertEquals($currentPerPage, $response->json('data.deliveryPoints.per_page'));
        $this->assertEquals($currentPage, $response->json('data.deliveryPoints.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.deliveryPoints.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.deliveryPoints.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.deliveryPoints.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.deliveryPoints.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.deliveryPoints.data'));
    }
}
