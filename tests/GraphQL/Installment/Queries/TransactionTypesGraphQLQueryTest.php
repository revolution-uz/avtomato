<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Models\Company\Installment;
use App\Models\Company\TransactionType;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class TransactionTypesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalTransactionTypeCount = 33;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::query()->forceDelete();
        TransactionType::query()->forceDelete();
        TransactionType::factory()->count($this->_totalTransactionTypeCount)->create();
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this->sendToGraphQL();
        $this->assertSuccessTransactionTypeResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this->sendToGraphQL(['page' => 4]);
        $this->assertSuccessTransactionTypeResponse($response, page: 4);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this->sendToGraphQL(['per_page' => 25]);
        $this->assertSuccessTransactionTypeResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this->sendToGraphQL(['sort' => 'id']);
        $this->assertSuccessTransactionTypeResponse($response);

        $transactionTypeIds = TransactionType::orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($transactionTypeIds, $response->json('data.transactionTypes.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this->sendToGraphQL(['sort' => '-id']);
        $this->assertSuccessTransactionTypeResponse($response);

        $transactionTypeIds = TransactionType::orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($transactionTypeIds, $response->json('data.transactionTypes.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $transactionType = TransactionType::query()->first();
        $response = $this->sendToGraphQL(['search' => $transactionType->name_en]);
        $this->assertSuccessTransactionTypeResponse($response, total: 1);
        $this->assertEquals($transactionType->id, $response->json('data.transactionTypes.data.0.id'));
    }

    #[Test]
    public function errorPage()
    {
        $response = $this->sendToGraphQL(['page' => 'asnjfasnjfasn']);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$page" of type "String!" used in position expecting type "Int".', $response->json('errors.0.message'));

        $response = $this->sendToGraphQL(['page' => 0]);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    #[Test]
    public function errorPerPage()
    {
        $response = $this->sendToGraphQL(['per_page' => 'asnjfasnjfasn']);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$per_page" of type "String!" used in position expecting type "Int".', $response->json('errors.0.message'));

        $response = $this->sendToGraphQL(['per_page' => 0]);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));

        $response = $this->sendToGraphQL(['per_page' => 101]);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    #[Test]
    public function errorSort()
    {
        $response = $this->sendToGraphQL(['sort' => 111]);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" of type "Int!" used in position expecting type "String".', $response->json('errors.0.message'));

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
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($data));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $variableArgs = implode(', ', array_map(fn ($variableName) => "{$variableName}: \${$variableName}", array_keys($variables)));
        $variableTypes = implode(', ', array_map(fn ($variableName) => "\${$variableName}: " . $this->getGraphQLType($variables[$variableName]), array_keys($variables)));
        $query = [
            'variables' => $variables,
            'query' => '
                query TransactionType' . ($variableTypes ? "({$variableTypes})" : '') . ' {
                    transactionTypes' . ($variableArgs ? "({$variableArgs})" : '') . ' {
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
                            name_uz
                            name_ru
                            name_en
                            is_system
                            created_at
                            updated_at
                            deleted_at
                        }
                    }
                }
            ',
        ];

        return $query;
    }

    private function assertSuccessTransactionTypeResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'transactionTypes' => [
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
                            'name_uz',
                            'name_ru',
                            'name_en',
                            'is_system',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ],
        ]);

        $totalCount = $total ?? $this->_totalTransactionTypeCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;

        $this->assertEquals($totalCount, $response->json('data.transactionTypes.total'));
        $this->assertEquals($currentPerPage, $response->json('data.transactionTypes.per_page'));
        $this->assertEquals($currentPage, $response->json('data.transactionTypes.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.transactionTypes.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.transactionTypes.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.transactionTypes.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.transactionTypes.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.transactionTypes.data'));
    }
}
