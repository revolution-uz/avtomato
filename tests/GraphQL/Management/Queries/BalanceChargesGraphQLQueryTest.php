<?php

namespace Tests\GraphQL\Management\Queries;

use App\Models\Company\BalanceCharge;
use App\Models\Company\Feature;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class BalanceChargesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalBalanceChargesCount = 30;

    private $_feature;

    private array $_structure = [
        'id',
        'amount',
        'created_at',
        'description',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_feature = Feature::query()->first();

        BalanceCharge::query()->forceDelete();
        BalanceCharge::factory()->count($this->_totalBalanceChargesCount)->create([
            'feature_id' => $this->_feature->id,
        ]);
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery()
            );

        $this->assertSuccessBalanceChargesResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $page = 2;

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );

        $this->assertSuccessBalanceChargesResponse($response, page: $page);
    }

    #[Test]
    public function successPerPage()
    {
        $perPage = 25;

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $perPage])
            );

        $this->assertSuccessBalanceChargesResponse($response, perPage: $perPage);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => 'id'])
            );

        $this->assertSuccessBalanceChargesResponse($response);
        $balanceChargeIds = BalanceCharge::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($balanceChargeIds, $response->json('data.balanceCharges.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => '-id'])
            );

        $this->assertSuccessBalanceChargesResponse($response);
        $balanceChargeIds = BalanceCharge::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($balanceChargeIds, $response->json('data.balanceCharges.data.*.id'));
    }

    #[Test]
    public function successFeatureId()
    {
        $feature = Feature::query()->whereNot('id', $this->_feature->id)->first();

        $balanceCharge = BalanceCharge::factory()->create([
            'feature_id' => $feature->id,
        ]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['feature_id' => $balanceCharge->feature_id])
            );

        $this->assertSuccessBalanceChargesResponse($response, total: 1);

        $this->assertEquals($balanceCharge->id, $response->json('data.balanceCharges.data.0.id'));
        $this->assertEquals($balanceCharge->amount, $response->json('data.balanceCharges.data.0.amount'));
        $this->assertEquals($balanceCharge->created_at->timestamp, $response->json('data.balanceCharges.data.0.created_at'));
        $this->assertEquals(trans('feature.' . $balanceCharge->feature->name->value) . ', ID=' . $balanceCharge->chargeable_id, $response->json('data.balanceCharges.data.0.description'));
    }

    #[Test]
    public function errorPage()
    {
        $page = 'kjefnerw';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$page\" got invalid value \"{$page}\"; Int cannot represent non-integer value: \"{$page}\"", $response->json('errors.0.message'));

        $page = 0;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    #[Test]
    public function errorPerPage()
    {
        $per_page = 'kjefnerw';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $per_page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$per_page\" got invalid value \"{$per_page}\"; Int cannot represent non-integer value: \"{$per_page}\"", $response->json('errors.0.message'));

        $per_page = 0;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $per_page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));

        $per_page = 101;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $per_page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    #[Test]
    public function errorSort()
    {
        $sort = 111;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => $sort])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$sort\" got invalid value {$sort}; String cannot represent a non string value: {$sort}", $response->json('errors.0.message'));

        $sort = 'non-exist-column';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => $sort])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'sort']), $response->json('errors.0.extensions.validation.sort.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query BalanceCharges($feature_id: Int, $sort: String, $page: Int, $per_page: Int) {
                balanceCharges(feature_id: $feature_id, sort: $sort, page: $page, per_page: $per_page) {
                    total
                    per_page
                    current_page
                    from
                    to
                    last_page
                    has_more_pages
                    sortables
                    data {' . $this->convertIntoQuery($this->_structure) . '}
                }
            }
        ';

        return $query;
    }

    private function assertSuccessBalanceChargesResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'balanceCharges' => [
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
        $totalCount = $total ?? $this->_totalBalanceChargesCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($currentPage, $response->json('data.balanceCharges.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.balanceCharges.from'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.balanceCharges.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.balanceCharges.last_page'));
        $this->assertEquals($currentPerPage, $response->json('data.balanceCharges.per_page'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.balanceCharges.to'));
        $this->assertEquals($totalCount, $response->json('data.balanceCharges.total'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.balanceCharges.data'));
    }
}
