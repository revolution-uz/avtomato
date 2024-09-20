<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class TransactionsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalTransactionsCount = 33;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::query()->forceDelete();
        Transaction::query()->forceDelete();
        Transaction::factory()->count($this->_totalTransactionsCount)->create();
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this->sendToGraphQL();
        $this->assertSuccessTransactionsResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this->sendToGraphQL(['page' => 4]);
        $this->assertSuccessTransactionsResponse($response, page: 4);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this->sendToGraphQL(['per_page' => 25]);
        $this->assertSuccessTransactionsResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this->sendToGraphQL(['sort' => 'id']);
        $this->assertSuccessTransactionsResponse($response);

        $transactionIds = Transaction::orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($transactionIds, $response->json('data.transactions.data.*.id'));
    }

    #[Test]
    public function successId()
    {
        $transaction = Transaction::query()->first();
        $response = $this->sendToGraphQL(['id' => $transaction->id]);
        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals(Transaction::query()->first()->id, $response->json('data.transactions.data.0.id'));
    }

    #[Test]
    public function emptyId()
    {
        $response = $this->sendToGraphQL(['id' => 423]);
        $this->assertEmpty($response->json('data.transactions.data'));
    }

    #[Test]
    public function successInstallmentId()
    {
        $transaction = Transaction::query()->first();
        $response = $this->sendToGraphQL(['installment_id' => $transaction->installment_id]);
        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals(Transaction::query()->first()->installment->id, $response->json('data.transactions.data.0.installment_id'));
    }

    #[Test]
    public function errorInstallmentId()
    {
        $response = $this->sendToGraphQL(['installment_id' => 93]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
    }

    #[Test]
    public function successCustomerId()
    {
        $transaction = Transaction::query()->first();
        $response = $this->sendToGraphQL(['customer_id' => $transaction->customer_id]);
        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals(Transaction::query()->first()->customer->id, $response->json('data.transactions.data.0.customer_id'));
    }

    #[Test]
    public function emptyCustomerId()
    {
        $response = $this->sendToGraphQL(['customer_id' => 93]);
        $this->assertEmpty($response->json('data.transactions.data'));
    }

    #[Test]
    public function successCardId()
    {
        $transaction = Transaction::query()->first();
        $response = $this->sendToGraphQL(['card_id' => $transaction->card->id]);
        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals(Transaction::query()->first()->card_id, $response->json('data.transactions.data.0.card_id'));
    }

    #[Test]
    public function errorCardId()
    {
        $response = $this->sendToGraphQL(['card_id' => 93]);
        $this->assertEmpty($response->json('data.transactions.data'));
    }

    #[Test]
    public function successTransactionTypeId()
    {
        $transaction = Transaction::query()->first();
        $response = $this->sendToGraphQL(['transaction_type_id' => $transaction->transactionType->id]);
        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals(Transaction::query()->first()->transactionType->id, $response->json('data.transactions.data.0.transaction_type_id'));
    }

    #[Test]
    public function errorTransactionTypeId()
    {
        $response = $this->sendToGraphQL(['transaction_type_id' => 93]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'transaction type id']), $response->json('errors.0.extensions.validation.transaction_type_id.0'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this->sendToGraphQL(['sort' => '-id']);
        $this->assertSuccessTransactionsResponse($response);

        $transactionIds = Transaction::orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($transactionIds, $response->json('data.transactions.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $transaction = Transaction::query()->first();
        $response = $this->sendToGraphQL(['search' => $transaction->external_id]);
        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals($transaction->id, $response->json('data.transactions.data.0.id'));
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

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::TRANSACTION_VIEW_ALL)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id]);
        Transaction::factory()->count(10)->create(['installment_id' => $installment->id]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery()
            );
        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertArrayHasKey('total', $response->json('data.transactions'));
        $this->assertEquals(0, $response->json('data.transactions.total'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery()
            );
        $this->assertSuccessTransactionsResponse($response, total: 10);
        $this->assertEquals(
            Transaction::query()
                ->whereHas('installment', fn ($q) => $q->where('branch_id', $branches[1]->id))
                ->orderByDesc('paid_at')
                ->pluck('id')->toArray(),
            $response->json('data.transactions.data.*.id')
        );
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
                query Transactions' . ($variableTypes ? "({$variableTypes})" : '') . ' {
                    transactions' . ($variableArgs ? "({$variableArgs})" : '') . ' {
                        current_page
                        from
                        has_more_pages
                        last_page
                        per_page
                        to
                        total
                        data {
                            id
                            branch_id
                            branch {
                                id
                                name
                                inn
                                address
                                created_at
                                updated_at
                            }
                            admin_id
                            admin {
                                id
                                first_name
                                last_name
                                username
                                language
                                role_id
                            }
                            installment_id
                            installment {
                                id
                                customer_id
                                customer {
                                    id
                                    pinfl
                                    tin
                                    passport_series
                                    passport_number
                                    passport_given_date
                                    passport_expiration_date
                                    passport_given_place
                                    passport_citizenship
                                    first_name
                                    last_name
                                    middle_name
                                    birth_date
                                    birth_place
                                    gender
                                }
                                tariff_id
                                tariff {
                                    id
                                    name
                                    percent
                                    min_period
                                    max_period
                                    min_amount
                                    max_amount
                                    min_initial_fee
                                    max_initial_fee
                                }
                                type
                                amount
                                initial_fee
                                description
                                sms_notify
                                is_blacklist
                                period
                                canceled_at
                                cancellation_details
                                starts_in
                                next_pay_date
                            }
                            is_initial_fee
                            customer_id
                            customer {
                                id
                                pinfl
                                tin
                                passport_series
                                passport_number
                                passport_given_date
                                passport_expiration_date
                                passport_given_place
                                passport_citizenship
                                first_name
                                last_name
                                middle_name
                                birth_date
                                birth_place
                                gender
                            }
                            transaction_type_id
                            transaction_type {
                                id
                                name
                            }
                            card_id
                            card {
                                id
                                number
                                owner
                                expiry
                                phone
                                balance
                                owner
                                created_at
                            }
                            external_id
                            amount
                            paid_at
                            created_at
                            updated_at
                        }
                    }
                }
            ',
        ];

        return $query;
    }

    private function assertSuccessTransactionsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'transactions' => [
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
                            'branch_id',
                            'branch' => [
                                'id',
                                'name',
                                'inn',
                                'address',
                                'created_at',
                                'updated_at',
                            ],
                            'admin_id',
                            'admin',
                            'installment_id',
                            'installment' => [
                                'id',
                                'customer_id',
                                'customer' => [
                                    'id',
                                    'pinfl',
                                    'tin',
                                    'passport_series',
                                    'passport_number',
                                    'passport_given_date',
                                    'passport_expiration_date',
                                    'passport_given_place',
                                    'passport_citizenship',
                                    'first_name',
                                    'last_name',
                                    'middle_name',
                                    'birth_date',
                                    'birth_place',
                                    'gender',
                                ],
                                'tariff_id',
                                'tariff' => [
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
                                'type',
                                'amount',
                                'initial_fee',
                                'description',
                                'sms_notify',
                                'is_blacklist',
                                'period',
                                'canceled_at',
                                'cancellation_details',
                                'starts_in',
                                'next_pay_date',
                            ],
                            'is_initial_fee',
                            'customer_id',
                            'customer' => [
                                'id',
                                'pinfl',
                                'tin',
                                'passport_series',
                                'passport_number',
                                'passport_given_date',
                                'passport_expiration_date',
                                'passport_given_place',
                                'passport_citizenship',
                                'first_name',
                                'last_name',
                                'middle_name',
                                'birth_date',
                                'birth_place',
                                'gender',
                            ],
                            'transaction_type_id',
                            'transaction_type' => [
                                'id',
                                'name',
                            ],
                            'card_id',
                            'card' => [
                                'id',
                                'number',
                                'owner',
                                'expiry',
                                'phone',
                                'balance',
                                'owner',
                                'created_at',
                            ],
                            'external_id',
                            'amount',
                            'paid_at',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ],
        ]);

        $totalCount = $total ?? $this->_totalTransactionsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;

        $this->assertEquals($totalCount, $response->json('data.transactions.total'));
        $this->assertEquals($currentPerPage, $response->json('data.transactions.per_page'));
        $this->assertEquals($currentPage, $response->json('data.transactions.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.transactions.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.transactions.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.transactions.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.transactions.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.transactions.data'));
    }
}
