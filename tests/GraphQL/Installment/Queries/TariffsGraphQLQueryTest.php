<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Models\Company\Tariff;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class TariffsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalTariffsCount = 31;

    protected function setUp(): void
    {
        parent::setUp();

        Tariff::query()->forceDelete();
        Tariff::factory()->count($this->_totalTariffsCount)->create();
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this->sendToGraphQL();
        $this->assertSuccessTariffsResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this->sendToGraphQL(['page' => 4]);

        $this->assertSuccessTariffsResponse($response, page: 4);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this->sendToGraphQL(['per_page' => 25]);
        $this->assertSuccessTariffsResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this->sendToGraphQL(['sort' => 'id']);

        $this->assertSuccessTariffsResponse($response);
        $tariffIds = Tariff::orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($tariffIds, $response->json('data.tariffs.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this->sendToGraphQL(['sort' => '-id']);

        $this->assertSuccessTariffsResponse($response);
        $tariffIds = Tariff::orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($tariffIds, $response->json('data.tariffs.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $tariff = Tariff::first();
        $response = $this->sendToGraphQL(['search' => $tariff->name]);

        $this->assertSuccessTariffsResponse($response, total: 1);
        $this->assertEquals($tariff->id, $response->json('data.tariffs.data.0.id'));
    }

    #[Test]
    public function errorPage()
    {
        // graphql type test
        $response = $this->sendToGraphQL(['page' => 'asnjfasnjfasn']);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$page" of type "String!" used in position expecting type "Int".', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this->sendToGraphQL(['page' => 0]);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    #[Test]
    public function errorPerPage()
    {
        // graphql type test
        $response = $this->sendToGraphQL(['per_page' => 'asnjfasnjfasn']);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$per_page" of type "String!" used in position expecting type "Int".', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this->sendToGraphQL(['per_page' => 0]);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));

        // maximum validation test
        $response = $this->sendToGraphQL(['per_page' => 101]);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    #[Test]
    public function errorSort()
    {
        // graphql type test
        $response = $this->sendToGraphQL(['sort' => 111]);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" of type "Int!" used in position expecting type "String".', $response->json('errors.0.message'));

        // not exist column validation test
        $response = $this->sendToGraphQL(['sort' => 'non-exist-column']);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'sort']), $response->json('errors.0.extensions.validation.sort.0'));
    }

    private function sendToGraphQL(array $data = []): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($data));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $variableArgs = implode(', ', array_map(fn ($variableName) => "{$variableName}: \${$variableName}", array_keys($variables)));
        $variableTypes = implode(', ', array_map(fn ($variableName) => "\${$variableName}: " . $this->getGraphQLType($variables[$variableName]), array_keys($variables)));
        $query['query'] = '
            query Tariffs' . ($variableTypes ? "({$variableTypes})" : '') . ' {
                tariffs' . ($variableArgs ? "({$variableArgs})" : '') . ' {
                    current_page
                    from
                    has_more_pages
                    last_page
                    per_page
                    to
                    total
                    data {
                        id
                        name
                        is_annual
                        percent
                        min_period
                        max_period
                        min_amount
                        max_amount
                        min_initial_fee
                        max_initial_fee
                    }
                }
            }
        ';

        return $query;
    }

    private function assertSuccessTariffsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'tariffs' => [
                    'current_page',
                    'from',
                    'has_more_pages',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'percent',
                            'min_period',
                            'max_period',
                            'min_amount',
                            'max_amount',
                            'min_initial_fee',
                            'max_initial_fee',
                        ],
                    ],
                ],
            ],
        ]);
        $totalCount = $total ?? $this->_totalTariffsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.tariffs.total'));
        $this->assertEquals($currentPerPage, $response->json('data.tariffs.per_page'));
        $this->assertEquals($currentPage, $response->json('data.tariffs.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.tariffs.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.tariffs.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.tariffs.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.tariffs.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.tariffs.data'));
    }
}
