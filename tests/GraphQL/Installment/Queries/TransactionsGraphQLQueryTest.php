<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use Database\Seeders\Company\AdminSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class TransactionsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalTransactionsCount = 33;

    private array $_structure = [
        'total',
        'per_page',
        'current_page',
        'from',
        'to',
        'last_page',
        'has_more_pages',
        'data' => [
            '*' => [
                'id',
                'branch_id',
                'admin_id',
                'installment_id',
                'is_initial_fee',
                'customer_id',
                'transaction_type_id',
                'card_id',
                'card_pan',
                'external_id',
                'amount',
                'status',
                'paid_at',
                'created_at',
                'updated_at',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Installment::query()->forceDelete();
        Transaction::factory()->count(5)->create(['paid_at' => Carbon::now()->subWeek()]);
        Transaction::factory()->count($this->_totalTransactionsCount - 5)->create();
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery());

        $this->assertSuccessTransactionsResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['page' => 4]));

        $this->assertSuccessTransactionsResponse($response, page: 4);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['per_page' => 25]));

        $this->assertSuccessTransactionsResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['sort' => 'id']));

        $this->assertSuccessTransactionsResponse($response);
        $transactionIds = Transaction::orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($transactionIds, $response->json('data.transactions.data.*.id'));
    }

    #[Test]
    public function successInstallmentId()
    {
        $transaction = Transaction::query()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => $transaction->installment_id]));

        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals(Transaction::query()->first()->installment->id, $response->json('data.transactions.data.0.installment_id'));
    }

    #[Test]
    public function errorInstallmentId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['installment_id' => 999999999]));

        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
    }

    #[Test]
    public function successCustomerId()
    {
        $transaction = Transaction::query()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['customer_id' => $transaction->customer_id]));

        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals(Transaction::query()->first()->customer->id, $response->json('data.transactions.data.0.customer_id'));
    }

    #[Test]
    public function emptyCustomerId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['customer_id' => 999999999]));

        $this->assertEmpty($response->json('data.transactions.data'));
    }

    #[Test]
    public function successCardId()
    {
        $transaction = Transaction::query()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['card_id' => $transaction->card->id]));

        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals(Transaction::query()->first()->card_id, $response->json('data.transactions.data.0.card_id'));
    }

    #[Test]
    public function errorCardId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['card_id' => 9999999999]));

        $this->assertEmpty($response->json('data.transactions.data'));
    }

    #[Test]
    public function successTransactionTypeId()
    {
        $transaction = Transaction::query()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['transaction_types' => [$transaction->transactionType->id]]));

        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals(Transaction::query()->first()->transactionType->id, $response->json('data.transactions.data.0.transaction_type_id'));
    }

    #[Test]
    public function successBranchesOnlyTransactionBranchId()
    {
        /** @var Branch $branch */
        $branch = Branch::factory()->create();
        $transactions = Transaction::factory()->count(5)->create(['branch_id' => $branch->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['branches' => [$branch->id]]));

        $this->assertSuccessTransactionsResponse($response, total: 5);
        foreach ($transactions as $transaction) {
            $this->assertNotNull(Arr::first($response->json('data.transactions.data.*.id'), fn ($item) => $item == $transaction->id));
        }
    }

    #[Test]
    public function successBranchesOnlyTransactionBranchIdIsNull()
    {
        /** @var Branch $branch */
        $branch = Branch::factory()->create();
        $installments = Installment::factory()->count(2)->create(['branch_id' => $branch->id]);
        $transactions = Transaction::factory()->count(5)->create(['installment_id' => $installments[0]->id, 'branch_id' => null]);
        Transaction::factory()->count(5)->create(['installment_id' => $installments[1]->id, 'branch_id' => null]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery([
                'installment_id' => $installments[0]->id,
                'branches' => [$branch->id],
            ]));

        $this->assertSuccessTransactionsResponse($response, total: 5);
        foreach ($transactions as $transaction) {
            $this->assertNotNull(Arr::first($response->json('data.transactions.data.*.id'), fn ($item) => $item == $transaction->id));
        }
    }

    #[Test]
    public function successBranchesOnlyInstallmentBranchId()
    {
        /** @var Branch $branch */
        $branch = Branch::factory()->create();
        $installment = Installment::factory()->create(['branch_id' => $branch->id]);
        $transactions = Transaction::factory()->count(5)->create(['branch_id' => null, 'installment_id' => $installment->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['branches' => [$branch->id]]));

        $this->assertSuccessTransactionsResponse($response, total: 5);
        foreach ($transactions as $transaction) {
            $this->assertNotNull(Arr::first($response->json('data.transactions.data.*.id'), fn ($item) => $item == $transaction->id));
        }
    }

    #[Test]
    public function successBranches()
    {
        /** @var Branch $branch */
        $branch = Branch::factory()->create();
        $transactionsWithBranch = Transaction::factory()->count(5)->create(['branch_id' => $branch->id]);
        $installment = Installment::factory()->create(['branch_id' => $branch->id]);
        $transactionsWithoutBranch = Transaction::factory()->count(5)->create(['branch_id' => null, 'installment_id' => $installment->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['branches' => [$branch->id]]));

        $this->assertSuccessTransactionsResponse($response, total: 10);
        foreach ($transactionsWithBranch->merge($transactionsWithoutBranch) as $transaction) {
            $this->assertNotNull(Arr::first($response->json('data.transactions.data.*.id'), fn ($item) => $item == $transaction->id));
        }
    }

    #[Test]
    public function errorTransactionTypeId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['transaction_types' => [93]]));

        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'transaction_types.0']), $response->json('errors.0.extensions.validation')['transaction_types.0'][0]);
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['sort' => '-id']));

        $this->assertSuccessTransactionsResponse($response);

        $transactionIds = Transaction::orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($transactionIds, $response->json('data.transactions.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        /** @var Branch $branch */
        $branch = Branch::factory()->create(['name' => 'Test Branch']);
        /** @var Installment $installment */
        $installment = Installment::factory()->create();
        /** @var Customer $customer */
        $customer = Customer::factory()->create(['first_name' => 'Test Firstname', 'last_name' => 'Test Lastname']);

        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->create([
            'external_id' => 'test_external_id',
            'branch_id' => $branch->id,
            'installment_id' => $installment->id,
            'customer_id' => $customer->id,
        ]);

        // ID
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['search' => (string) $transaction->id]));
        $this->assertSuccessTransactionsResponse($response, total: 'IGNORE');
        $this->assertNotNull(Arr::first($response->json('data.transactions.data'), fn ($item) => $item['id'] === $transaction->id));

        // External ID
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['search' => $transaction->external_id]));
        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals($transaction->id, $response->json('data.transactions.data.0.id'));

        // Branch name
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['search' => $branch->name]));
        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals($transaction->id, $response->json('data.transactions.data.0.id'));

        // Installment ID
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['search' => (string) $installment->id]));
        $this->assertSuccessTransactionsResponse($response, total: 'IGNORE');
        $this->assertNotNull(Arr::first($response->json('data.transactions.data'), fn ($item) => $item['id'] === $transaction->id));

        // Customer firstname
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['search' => $customer->first_name]));
        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals($transaction->id, $response->json('data.transactions.data.0.id'));

        // Customer lastname
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['search' => $customer->last_name]));
        $this->assertSuccessTransactionsResponse($response, total: 1);
        $this->assertEquals($transaction->id, $response->json('data.transactions.data.0.id'));
    }

    #[Test]
    public function successWithDateRange()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery([
                'from' => Carbon::now()->subWeek()->format('Y-m-d'),
                'to' => Carbon::now()->subDay()->format('Y-m-d'),
            ]));

        $this->assertSuccessTransactionsResponse($response, total: 5);
    }

    #[Test]
    public function errorWithDateRange()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['from' => 'dfghjkjk']));
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('validation.date_format', ['attribute' => 'from', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation.from.0'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['from' => Carbon::now()->toDateString(), 'to' => Carbon::now()->subDay()->toDateString()]));
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('validation.after_or_equal', ['attribute' => 'to', 'date' => 'from']), $response->json('errors.0.extensions.validation.to.0'));
    }

    #[Test]
    public function errorPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['page' => 'asnjfasnjfasn']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$page" got invalid value "asnjfasnjfasn"; Int cannot represent non-integer value: "asnjfasnjfasn"', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['page' => 0]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    #[Test]
    public function errorPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['per_page' => 'asnjfasnjfasn']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$per_page" got invalid value "asnjfasnjfasn"; Int cannot represent non-integer value: "asnjfasnjfasn"', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['per_page' => 0]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['per_page' => 101]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    #[Test]
    public function errorSort()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['sort' => 111]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" got invalid value 111; String cannot represent a non string value: 111', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['sort' => 'non-exist-column']));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'sort']), $response->json('errors.0.extensions.validation.sort.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        /** @var Role $role */
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::TRANSACTION_VIEW_ALL)->firstOrFail();

        /** @var Branch[] $branches */
        $branches = Branch::factory()->count(2)->create();
        Transaction::factory()->count(10)->create(['branch_id' => $branches[1]->id]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery());
        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertArrayHasKey('total', $response->json('data.transactions'));
        $this->assertEquals(0, $response->json('data.transactions.total'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery());
        $this->assertSuccessTransactionsResponse($response, total: 10);
        $this->assertEquals(
            Transaction::query()
                ->where('branch_id', $branches[1]->id)
                ->orderByDesc('paid_at')
                ->pluck('id')->toArray(),
            $response->json('data.transactions.data.*.id')
        );
    }

    #[Test]
    public function successWithSystemAdmins()
    {
        Artisan::call('tenants:seed', ['--class' => AdminSeeder::class, '--tenants' => [tenant('id')]]);
        $systemAdmin = CompanyAdmin::getIcanIntegrator();
        Transaction::factory()->count(10)->create(['admin_id' => $systemAdmin->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['admin_id' => $systemAdmin->id]));

        $this->assertSuccessTransactionsResponse($response, 10);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query Transactions(
                    \$sort: String
                    \$search: String
                    \$page: Int
                    \$per_page: Int
                    \$branches: [Int!]
                    \$admin_id: Int
                    \$installment_id: Int
                    \$customer_id: Int
                    \$card_id: Int
                    \$transaction_types: [Int!]
                    \$from: String
                    \$to: String
                ) {
                    transactions(
                        sort: \$sort
                        search: \$search
                        page: \$page
                        per_page: \$per_page
                        branches: \$branches
                        admin_id: \$admin_id
                        installment_id: \$installment_id
                        customer_id: \$customer_id
                        card_id: \$card_id
                        transaction_types: \$transaction_types
                        from: \$from
                        to: \$to
                    ) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessTransactionsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['transactions' => $this->_structure]]);

        if ($total == 'IGNORE') {
            $total = $response->json('data.transactions.total');
        }
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
