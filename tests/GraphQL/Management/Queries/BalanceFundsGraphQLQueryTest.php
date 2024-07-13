<?php

namespace Management\Queries;

use App\Models\Company\BalanceFund;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class BalanceFundsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalBalanceFundCount = 30;

    private array $_structure = [
        'id',
        'amount',
        'phone_number',
        'external_transaction_id',
        'card_number',
        'created_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        BalanceFund::query()->forceDelete();
        BalanceFund::factory()->count($this->_totalBalanceFundCount)->create();
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

        $this->assertSuccessBalanceFundsResponse($response);
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

        $this->assertSuccessBalanceFundsResponse($response, page: $page);
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

        $this->assertSuccessBalanceFundsResponse($response, perPage: $perPage);
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

        $this->assertSuccessBalanceFundsResponse($response);
        $balanceFundIds = BalanceFund::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($balanceFundIds, $response->json('data.balanceFunds.data.*.id'));
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

        $this->assertSuccessBalanceFundsResponse($response);
        $balanceFundIds = BalanceFund::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($balanceFundIds, $response->json('data.balanceFunds.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $balanceFund = BalanceFund::query()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $balanceFund->phone_number])
            );
        $this->assertSuccessBalanceFundsResponse($response, total: 1);
        $this->assertEquals($balanceFund->id, $response->json('data.balanceFunds.data.0.id'));
        $this->assertEquals($balanceFund->amount, $response->json('data.balanceFunds.data.0.amount'));
        $this->assertEquals($balanceFund->phone_number, $response->json('data.balanceFunds.data.0.phone_number'));
        $this->assertEquals($balanceFund->external_transaction_id, $response->json('data.balanceFunds.data.0.external_transaction_id'));
        $this->assertEquals($balanceFund->card_number, $response->json('data.balanceFunds.data.0.card_number'));
        $this->assertEquals($balanceFund->created_at->timestamp, $response->json('data.balanceFunds.data.0.created_at'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $balanceFund->card_number])
            );
        $this->assertSuccessBalanceFundsResponse($response, total: 1);
        $this->assertEquals($balanceFund->id, $response->json('data.balanceFunds.data.0.id'));
        $this->assertEquals($balanceFund->amount, $response->json('data.balanceFunds.data.0.amount'));
        $this->assertEquals($balanceFund->phone_number, $response->json('data.balanceFunds.data.0.phone_number'));
        $this->assertEquals($balanceFund->external_transaction_id, $response->json('data.balanceFunds.data.0.external_transaction_id'));
        $this->assertEquals($balanceFund->card_number, $response->json('data.balanceFunds.data.0.card_number'));
        $this->assertEquals($balanceFund->created_at->timestamp, $response->json('data.balanceFunds.data.0.created_at'));
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
            query BalanceFunds($sort: String, $search: String, $page: Int, $per_page: Int) {
                balanceFunds(sort: $sort, search: $search, page: $page, per_page: $per_page) {
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

    private function assertSuccessBalanceFundsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'balanceFunds' => [
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
        $totalCount = $total ?? $this->_totalBalanceFundCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($currentPage, $response->json('data.balanceFunds.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.balanceFunds.from'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.balanceFunds.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.balanceFunds.last_page'));
        $this->assertEquals($currentPerPage, $response->json('data.balanceFunds.per_page'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.balanceFunds.to'));
        $this->assertEquals($totalCount, $response->json('data.balanceFunds.total'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.balanceFunds.data'));
    }

    private function convertIntoQuery(array $structure): string
    {
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                if ($key == '*') {
                    $structure[$key] = $this->convertIntoQuery($value);
                } else {
                    $structure[$key] = "{$key} {{$this->convertIntoQuery($value)}}";
                }
            }
        }

        return implode(' ', $structure);
    }
}
