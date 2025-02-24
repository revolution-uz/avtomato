<?php

namespace Tests\GraphQL\Common\Queries;

use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\UniredCard;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class InstallmentPinflCardsGraphQLQueryTest extends GraphQLTestCase
{
    private Installment $_installment;

    private int $_totalCardsCount = 29;

    private array $_structure = [
        'id',
        'customer_id',
        'pinfl',
        'uuid',
        'pan',
        'phone',
        'type',
        'owner',
        'auto',
        'is_verified',
        'is_blocked',
        'status',
        'block_reason',
        'blocked_at',
        'created_at',
        'updated_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $customer = Customer::factory()->create();
        $guarantor = Customer::factory()->create();

        UniredCard::factory()->count(floor($this->_totalCardsCount / 2))->create(['customer_id' => $customer->id]);
        UniredCard::factory()->count(ceil($this->_totalCardsCount / 2))->create(['customer_id' => $guarantor->id]);

        $this->_installment = Installment::factory()->create(['customer_id' => $customer->id]);
        $this->_installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id]));

        $this->assertSuccessInstallmentPinflCardsResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'page' => 3]));

        $this->assertSuccessInstallmentPinflCardsResponse($response, page: 3);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'per_page' => 25]));

        $this->assertSuccessInstallmentPinflCardsResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'sort' => 'id']));

        $this->assertSuccessInstallmentPinflCardsResponse($response);
        $cardIds = UniredCard::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($cardIds, $response->json('data.installmentPinflCards.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'sort' => '-id']));

        $this->assertSuccessInstallmentPinflCardsResponse($response);
        $cardIds = UniredCard::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($cardIds, $response->json('data.installmentPinflCards.data.*.id'));
    }

    #[Test]
    public function errorPage()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'page' => 'asnjfasnjfasn']));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$page" got invalid value "asnjfasnjfasn"; Int cannot represent non-integer value: "asnjfasnjfasn"', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'page' => 0]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    #[Test]
    public function errorPerPage()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'per_page' => 'asnjfasnjfasn']));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$per_page" got invalid value "asnjfasnjfasn"; Int cannot represent non-integer value: "asnjfasnjfasn"', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'per_page' => 0]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));

        // maximum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'per_page' => 101]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    #[Test]
    public function errorSort()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'sort' => 111]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" got invalid value 111; String cannot represent a non string value: 111', $response->json('errors.0.message'));

        // not exist column validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'sort' => 'non-exist-column']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'sort']), $response->json('errors.0.extensions.validation.sort.0'));
    }

    public function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query InstallmentPinflCards(
                    \$installment_id: Int!
                    \$sort: String
                    \$page: Int
                    \$per_page: Int
                ) {
                    installmentPinflCards(
                        installment_id: \$installment_id
                        sort: \$sort
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

    private function assertSuccessInstallmentPinflCardsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'installmentPinflCards' => [
                    'current_page',
                    'from',
                    'has_more_pages',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                    'data' => [
                        $this->_structure,
                    ],
                ],
            ],
        ]);
        $totalCount = $total ?? $this->_totalCardsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.installmentPinflCards.total'));
        $this->assertEquals($currentPerPage, $response->json('data.installmentPinflCards.per_page'));
        $this->assertEquals($currentPage, $response->json('data.installmentPinflCards.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.installmentPinflCards.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.installmentPinflCards.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.installmentPinflCards.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.installmentPinflCards.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.installmentPinflCards.data'));
    }
}
