<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\TransactionAttemptTypeEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionAttempt;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class TransactionAttemptsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalTransactionAttemptsCount = 30;

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
                'type',
                'branch_id',
                'installment_id',
                'customer_id',
                'transaction_id',
                'card_id',
                'card_pan',
                'amount',
                'status',
                'created_at',
                'updated_at',
                'customer_type',
                'transaction_type_id',
                'branch' => [
                    'id',
                    'name',
                    'address',
                    'inn',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'installment' => [
                    'id',
                    'customer_id',
                    'status',
                    'branch_id',
                    'admin_id',
                    'tariff_id',
                    'type',
                    'amount',
                    'initial_fee',
                    'sms_notify',
                    'auto_pay',
                    'auto_pay_by_pinfl',
                    'is_blacklist',
                    'period',
                    'description',
                    'cancellation_details',
                    'starts_in',
                    'next_pay_date',
                    'created_at',
                    'updated_at',
                    'canceled_at',
                    'deleted_at',
                    'debt',
                    'monthly_payment',
                ],
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
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'transaction' => [
                    'id',
                    'branch_id',
                    'admin_id',
                    'installment_id',
                    'customer_id',
                    'transaction_type_id',
                    'card_id',
                    'card_pan',
                    'external_id',
                    'amount',
                    'paid_at',
                    'created_at',
                    'updated_at',
                ],
                'card' => [
                    'id',
                    'customer_id',
                    'number',
                    'card_type',
                    'owner',
                    'expiry',
                    'is_connected',
                    'phone',
                    'status',
                    'balance',
                    'created_at',
                    'updated_at',
                ],
                'transaction_type' => [
                    'id',
                    'name',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'is_system',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('tenants:seed', ['--class' => TransactionTypeSeeder::class, '--tenants' => [tenant()->id]]);
        TransactionAttempt::query()->forceDelete();
        TransactionAttempt::factory()->count($this->_totalTransactionAttemptsCount)->create();
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery()
            );

        $this->assertSuccessTransactionAttemptsResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $page = 2;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['page' => $page])
            );

        $this->assertSuccessTransactionAttemptsResponse($response, page: $page);
    }

    #[Test]
    public function successPerPage()
    {
        $perPage = 25;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['per_page' => $perPage])
            );
        $this->assertSuccessTransactionAttemptsResponse($response, perPage: $perPage);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['sort' => 'id'])
            );
        $this->assertSuccessTransactionAttemptsResponse($response);

        $transactionAttemptIds = TransactionAttempt::orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($transactionAttemptIds, $response->json('data.transactionAttempts.data.*.id'));
    }

    #[Test]
    public function successId()
    {
        $transactionAttempt = TransactionAttempt::query()->inRandomOrder()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['id' => $transactionAttempt->id])
            );

        $this->assertSuccessTransactionAttemptsResponse($response, total: 1);
        $this->assertEquals($transactionAttempt->id, $response->json('data.transactionAttempts.data.0.id'));
    }

    #[Test]
    public function emptyId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['id' => 999999999])
            );
        $this->assertEmpty($response->json('data.transactionAttempts.data'));
    }

    #[Test]
    public function successInstallmentId()
    {
        $transactionAttempt = TransactionAttempt::query()->inRandomOrder()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['installment_id' => $transactionAttempt->installment_id])
            );
        $this->assertSuccessTransactionAttemptsResponse($response, total: 1);
        $this->assertEquals($transactionAttempt->installment_id, $response->json('data.transactionAttempts.data.0.installment_id'));
    }

    #[Test]
    public function errorInstallmentId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['installment_id' => 999999999])
            );
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
    }

    #[Test]
    public function successCustomerId()
    {
        $transactionAttempt = TransactionAttempt::query()->inRandomOrder()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['customer_id' => $transactionAttempt->customer_id])
            );
        $this->assertSuccessTransactionAttemptsResponse($response, total: 1);
        $this->assertEquals($transactionAttempt->customer_id, $response->json('data.transactionAttempts.data.0.customer_id'));
    }

    #[Test]
    public function emptyCustomerId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['customer_id' => 999999999])
            );
        $this->assertEmpty($response->json('data.transactionAttempts.data'));
    }

    #[Test]
    public function successCardId()
    {
        $transactionAttempt = TransactionAttempt::query()->inRandomOrder()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['card_id' => $transactionAttempt->card_id])
            );
        $this->assertSuccessTransactionAttemptsResponse($response, total: 1);
        $this->assertEquals($transactionAttempt->card_id, $response->json('data.transactionAttempts.data.0.card_id'));
    }

    #[Test]
    public function errorCardId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['card_id' => 999999999])
            );
        $this->assertEmpty($response->json('data.transactionAttempts.data'));
    }

    #[Test]
    public function successTransactionId()
    {
        $transactionAttempt = TransactionAttempt::query()->inRandomOrder()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['transaction_id' => $transactionAttempt->transaction_id])
            );
        $this->assertSuccessTransactionAttemptsResponse($response, total: 1);
        $this->assertEquals($transactionAttempt->transaction_id, $response->json('data.transactionAttempts.data.0.transaction_id'));
    }

    #[Test]
    public function errorTransactionId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['transaction_id' => 999999999])
            );
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'transaction id']), $response->json('errors.0.extensions.validation.transaction_id.0'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['sort' => '-id'])
            );
        $this->assertSuccessTransactionAttemptsResponse($response);
        $transactionAttemptIds = TransactionAttempt::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($transactionAttemptIds, $response->json('data.transactionAttempts.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $transactionAttempt = TransactionAttempt::query()->orderByDesc('id')->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['search' => (string) $transactionAttempt->id])
            );
        $this->assertSuccessTransactionAttemptsResponse($response, total: 1);
        $this->assertEquals($transactionAttempt->id, $response->json('data.transactionAttempts.data.0.id'));
    }

    #[Test]
    public function successMonthlyPayment()
    {
        $installment = Installment::factory()->create([
            'amount' => 1000000,
            'percent' => 15,
            'initial_fee' => 100,
            'period' => 10,
            'paid_amount' => 0,
            'starts_in' => Carbon::now(),
        ]);

        $transaction = Transaction::factory()->create([
            'installment_id' => $installment->id,
            'amount' => 50000,
        ]);

        $transactionAttempt = TransactionAttempt::factory()->create([
            'installment_id' => $installment->id,
            'transaction_id' => $transaction->id,
            'amount' => 50000,
        ]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['id' => $transactionAttempt->id])
            );

        $this->assertSuccessTransactionAttemptsResponse($response, total: 1);
        $this->assertEquals(50000, $response->json('data.transactionAttempts.data.0.amount'));
        $this->assertEquals(64988.5, $response->json('data.transactionAttempts.data.0.installment.debt'));
        $this->assertEquals(114988.5, $response->json('data.transactionAttempts.data.0.installment.monthly_payment'));
    }

    #[Test]
    public function errorPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['page' => 'qwertyuiop'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$page" got invalid value "qwertyuiop"; Int cannot represent non-integer value: "qwertyuiop"', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['page' => 0])
            );
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
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['per_page' => 'qwertyuiop'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$per_page" got invalid value "qwertyuiop"; Int cannot represent non-integer value: "qwertyuiop"', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['per_page' => 0])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['per_page' => 101])
            );
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
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['sort' => 111])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" got invalid value 111; String cannot represent a non string value: 111', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['sort' => 'non-exist-column'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'sort']), $response->json('errors.0.extensions.validation.sort.0'));
    }

    #[Test]
    public function successOnTypePayWithCard()
    {
        $transactionAttempt = TransactionAttempt::factory()->create([
            'type' => TransactionAttemptTypeEnum::ATMOS_PAY_WITH_CARD,
        ]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['id' => $transactionAttempt->id])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertEquals(1, $response->json('data.transactionAttempts.total'));
        $this->assertEquals($transactionAttempt->id, $response->json('data.transactionAttempts.data.0.id'));
        $this->assertNotNull($response->json('data.transactionAttempts.data.0.card_id'));
        $this->assertEquals($transactionAttempt->card_id, $response->json('data.transactionAttempts.data.0.card_id'));
        $this->assertEquals($transactionAttempt->card->pan, $response->json('data.transactionAttempts.data.0.card_pan'));
    }

    #[Test]
    public function successOnTypePayWithPinfl()
    {
        $transactionAttempt = TransactionAttempt::factory()->create([
            'card_id' => null,
            'type' => TransactionAttemptTypeEnum::ATMOS_PAY_WITH_PINFL,
        ]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['id' => $transactionAttempt->id])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertEquals(1, $response->json('data.transactionAttempts.total'));
        $this->assertEquals($transactionAttempt->id, $response->json('data.transactionAttempts.data.0.id'));
        $this->assertEquals($transactionAttempt->card_id, $response->json('data.transactionAttempts.data.0.card_id'));
        $this->assertEquals($transactionAttempt->transaction->card_pan, $response->json('data.transactionAttempts.data.0.card_pan'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::TRANSACTION_ATTEMPT_VIEW_ALL)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id]);
        TransactionAttempt::factory()->count(10)->create(['installment_id' => $installment->id]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery()
            );
        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertArrayHasKey('total', $response->json('data.transactionAttempts'));
        $this->assertEquals(0, $response->json('data.transactionAttempts.total'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery()
            );
        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertArrayHasKey('total', $response->json('data.transactionAttempts'));
        $this->assertEquals(10, $response->json('data.transactionAttempts.total'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query TransactionAttempts(
                $sort: String
                $search: String
                $page: Int
                $per_page: Int
                $id: Int
                $installment_id: Int
                $customer_id: Int
                $card_id: Int
                $transaction_id: Int
            ) {
                transactionAttempts(
                    sort: $sort
                    search: $search
                    page: $page
                    per_page: $per_page
                    id: $id
                    installment_id: $installment_id
                    customer_id: $customer_id
                    card_id: $card_id
                    transaction_id: $transaction_id
                ) {
                    ' . $this->convertIntoQuery($this->_structure) . '
                }
            }
        ';

        return $query;
    }

    private function assertSuccessTransactionAttemptsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'transactionAttempts' => $this->_structure,
            ],
        ]);

        $totalCount = $total ?? $this->_totalTransactionAttemptsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;

        $this->assertEquals($totalCount, $response->json('data.transactionAttempts.total'));
        $this->assertEquals($currentPerPage, $response->json('data.transactionAttempts.per_page'));
        $this->assertEquals($currentPage, $response->json('data.transactionAttempts.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.transactionAttempts.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.transactionAttempts.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.transactionAttempts.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.transactionAttempts.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.transactionAttempts.data'));

        $transactionAttempt = TransactionAttempt::query()
            ->with(['installment', 'customer', 'transaction', 'installment.branch'])
            ->findOrFail($response->json('data.transactionAttempts.data.0.id'));

        $this->assertEquals($transactionAttempt->id, $response->json('data.transactionAttempts.data.0.id'));
        $this->assertEquals($transactionAttempt->type->value, $response->json('data.transactionAttempts.data.0.type'));
        $this->assertEquals($transactionAttempt->installment->branch_id, $response->json('data.transactionAttempts.data.0.branch_id'));
        $this->assertEquals($transactionAttempt->installment_id, $response->json('data.transactionAttempts.data.0.installment_id'));
        $this->assertEquals($transactionAttempt->customer_id, $response->json('data.transactionAttempts.data.0.customer_id'));
        $this->assertEquals($transactionAttempt->transaction_id, $response->json('data.transactionAttempts.data.0.transaction_id'));
        $this->assertEquals($transactionAttempt->card_id, $response->json('data.transactionAttempts.data.0.card_id'));
        $this->assertEquals($transactionAttempt->amount, $response->json('data.transactionAttempts.data.0.amount'));
        $this->assertEquals($transactionAttempt->status->value, $response->json('data.transactionAttempts.data.0.status'));
        $this->assertEquals($transactionAttempt->created_at->timestamp, $response->json('data.transactionAttempts.data.0.created_at'));
        $this->assertEquals($transactionAttempt->updated_at->timestamp, $response->json('data.transactionAttempts.data.0.updated_at'));
        $this->assertEquals($transactionAttempt->customer_type, $response->json('data.transactionAttempts.data.0.customer_type'));
        $this->assertEquals($transactionAttempt->transaction_type_id, $response->json('data.transactionAttempts.data.0.transaction_type_id'));

        // branch
        $this->assertEquals($transactionAttempt->installment->branch->id, $response->json('data.transactionAttempts.data.0.branch.id'));
        $this->assertEquals($transactionAttempt->installment->branch->name, $response->json('data.transactionAttempts.data.0.branch.name'));
        $this->assertEquals($transactionAttempt->installment->branch->address, $response->json('data.transactionAttempts.data.0.branch.address'));
        $this->assertEquals($transactionAttempt->installment->branch->inn, $response->json('data.transactionAttempts.data.0.branch.inn'));
        $this->assertEquals($transactionAttempt->installment->branch->created_at->timestamp, $response->json('data.transactionAttempts.data.0.branch.created_at'));
        $this->assertEquals($transactionAttempt->installment->branch->updated_at->timestamp, $response->json('data.transactionAttempts.data.0.branch.updated_at'));
        $this->assertEquals($transactionAttempt->installment->branch->deleted_at?->timestamp, $response->json('data.transactionAttempts.data.0.branch.deleted_at'));

        // installment
        $this->assertEquals($transactionAttempt->installment->id, $response->json('data.transactionAttempts.data.0.installment.id'));
        $this->assertEquals($transactionAttempt->installment->customer_id, $response->json('data.transactionAttempts.data.0.installment.customer_id'));
        $this->assertEquals($transactionAttempt->installment->status->value, $response->json('data.transactionAttempts.data.0.installment.status'));
        $this->assertEquals($transactionAttempt->installment->branch_id, $response->json('data.transactionAttempts.data.0.installment.branch_id'));
        $this->assertEquals($transactionAttempt->installment->admin_id, $response->json('data.transactionAttempts.data.0.installment.admin_id'));
        $this->assertEquals($transactionAttempt->installment->tariff_id, $response->json('data.transactionAttempts.data.0.installment.tariff_id'));
        $this->assertEquals($transactionAttempt->installment->type->value, $response->json('data.transactionAttempts.data.0.installment.type'));
        $this->assertEquals($transactionAttempt->installment->amount, $response->json('data.transactionAttempts.data.0.installment.amount'));
        $this->assertEquals($transactionAttempt->installment->initial_fee, $response->json('data.transactionAttempts.data.0.installment.initial_fee'));
        $this->assertEquals($transactionAttempt->installment->sms_notify, $response->json('data.transactionAttempts.data.0.installment.sms_notify'));
        $this->assertEquals($transactionAttempt->installment->auto_pay, $response->json('data.transactionAttempts.data.0.installment.auto_pay'));
        $this->assertEquals($transactionAttempt->installment->any_card_charge, $response->json('data.transactionAttempts.data.0.installment.auto_pay_by_pinfl'));
        $this->assertEquals($transactionAttempt->installment->is_blacklist, $response->json('data.transactionAttempts.data.0.installment.is_blacklist'));
        $this->assertEquals($transactionAttempt->installment->period, $response->json('data.transactionAttempts.data.0.installment.period'));
        $this->assertEquals($transactionAttempt->installment->description, $response->json('data.transactionAttempts.data.0.installment.description'));
        $this->assertEquals($transactionAttempt->installment->cancellation_details, $response->json('data.transactionAttempts.data.0.installment.cancellation_details'));
        $this->assertEquals($transactionAttempt->installment->starts_in?->timestamp, $response->json('data.transactionAttempts.data.0.installment.starts_in'));
        $this->assertEquals($transactionAttempt->installment->nextPaymentDate?->timestamp, $response->json('data.transactionAttempts.data.0.installment.next_pay_date'));
        $this->assertEquals($transactionAttempt->installment->created_at?->timestamp, $response->json('data.transactionAttempts.data.0.installment.created_at'));
        $this->assertEquals($transactionAttempt->installment->updated_at?->timestamp, $response->json('data.transactionAttempts.data.0.installment.updated_at'));
        $this->assertEquals($transactionAttempt->installment->canceled_at?->timestamp, $response->json('data.transactionAttempts.data.0.installment.canceled_at'));
        $this->assertEquals($transactionAttempt->installment->deleted_at?->timestamp, $response->json('data.transactionAttempts.data.0.installment.deleted_at'));
        $this->assertEquals($transactionAttempt->installment->debtAmount, $response->json('data.transactionAttempts.data.0.installment.debt'));
        $this->assertEquals($transactionAttempt->installment->monthlyPayment, $response->json('data.transactionAttempts.data.0.installment.monthly_payment'));

        // customer
        $this->assertEquals($transactionAttempt->customer->id, $response->json('data.transactionAttempts.data.0.customer.id'));
        $this->assertEquals($transactionAttempt->customer->pinfl, $response->json('data.transactionAttempts.data.0.customer.pinfl'));
        $this->assertEquals($transactionAttempt->customer->tin, $response->json('data.transactionAttempts.data.0.customer.tin'));
        $this->assertEquals($transactionAttempt->customer->passport_series, $response->json('data.transactionAttempts.data.0.customer.passport_series'));
        $this->assertEquals($transactionAttempt->customer->passport_number, $response->json('data.transactionAttempts.data.0.customer.passport_number'));
        $this->assertEquals($transactionAttempt->customer->passport_given_date?->format('d-m-Y'), $response->json('data.transactionAttempts.data.0.customer.passport_given_date'));
        $this->assertEquals($transactionAttempt->customer->passport_expiration_date?->format('d-m-Y'), $response->json('data.transactionAttempts.data.0.customer.passport_expiration_date'));
        $this->assertEquals($transactionAttempt->customer->passport_given_place, $response->json('data.transactionAttempts.data.0.customer.passport_given_place'));
        $this->assertEquals($transactionAttempt->customer->passport_citizenship, $response->json('data.transactionAttempts.data.0.customer.passport_citizenship'));
        $this->assertEquals($transactionAttempt->customer->first_name, $response->json('data.transactionAttempts.data.0.customer.first_name'));
        $this->assertEquals($transactionAttempt->customer->last_name, $response->json('data.transactionAttempts.data.0.customer.last_name'));
        $this->assertEquals($transactionAttempt->customer->middle_name, $response->json('data.transactionAttempts.data.0.customer.middle_name'));
        $this->assertEquals($transactionAttempt->customer->birth_date?->format('d-m-Y'), $response->json('data.transactionAttempts.data.0.customer.birth_date'));
        $this->assertEquals($transactionAttempt->customer->birth_place, $response->json('data.transactionAttempts.data.0.customer.birth_place'));
        $this->assertEquals($transactionAttempt->customer->gender->value, $response->json('data.transactionAttempts.data.0.customer.gender'));
        $this->assertEquals($transactionAttempt->customer->created_at->timestamp, $response->json('data.transactionAttempts.data.0.customer.created_at'));
        $this->assertEquals($transactionAttempt->customer->updated_at->timestamp, $response->json('data.transactionAttempts.data.0.customer.updated_at'));
        $this->assertEquals($transactionAttempt->customer->deleted_at?->timestamp, $response->json('data.transactionAttempts.data.0.customer.deleted_at'));

        // transaction
        $this->assertEquals($transactionAttempt->transaction->id, $response->json('data.transactionAttempts.data.0.transaction.id'));
        $this->assertEquals($transactionAttempt->transaction->branch_id, $response->json('data.transactionAttempts.data.0.transaction.branch_id'));
        $this->assertEquals($transactionAttempt->transaction->admin_id, $response->json('data.transactionAttempts.data.0.transaction.admin_id'));
        $this->assertEquals($transactionAttempt->transaction->installment_id, $response->json('data.transactionAttempts.data.0.transaction.installment_id'));
        $this->assertEquals($transactionAttempt->transaction->customer_id, $response->json('data.transactionAttempts.data.0.transaction.customer_id'));
        $this->assertEquals($transactionAttempt->transaction->transaction_type_id, $response->json('data.transactionAttempts.data.0.transaction.transaction_type_id'));
        $this->assertEquals($transactionAttempt->transaction->card_id, $response->json('data.transactionAttempts.data.0.transaction.card_id'));
        $this->assertEquals($transactionAttempt->transaction->external_id, $response->json('data.transactionAttempts.data.0.transaction.external_id'));
        $this->assertEquals($transactionAttempt->transaction->amount, $response->json('data.transactionAttempts.data.0.transaction.amount'));
        $this->assertEquals($transactionAttempt->transaction->paid_at?->timestamp, $response->json('data.transactionAttempts.data.0.transaction.paid_at'));
        $this->assertEquals($transactionAttempt->transaction->created_at->timestamp, $response->json('data.transactionAttempts.data.0.transaction.created_at'));
        $this->assertEquals($transactionAttempt->transaction->updated_at->timestamp, $response->json('data.transactionAttempts.data.0.transaction.updated_at'));

        // card
        $this->assertEquals($transactionAttempt->card->id, $response->json('data.transactionAttempts.data.0.card.id'));
        $this->assertEquals($transactionAttempt->card->customer_id, $response->json('data.transactionAttempts.data.0.card.customer_id'));
        $this->assertEquals($transactionAttempt->card->pan, $response->json('data.transactionAttempts.data.0.card.number'));
        $this->assertEquals($transactionAttempt->card->card_type, $response->json('data.transactionAttempts.data.0.card.card_type'));
        $this->assertEquals($transactionAttempt->card->owner, $response->json('data.transactionAttempts.data.0.card.owner'));
        $this->assertEquals($transactionAttempt->card->expiry, $response->json('data.transactionAttempts.data.0.card.expiry'));
        $this->assertEquals($transactionAttempt->card->is_connected, $response->json('data.transactionAttempts.data.0.card.is_connected'));
        $this->assertEquals($transactionAttempt->card->phone, $response->json('data.transactionAttempts.data.0.card.phone'));
        $this->assertEquals($transactionAttempt->card->status->value, $response->json('data.transactionAttempts.data.0.card.status'));
        $this->assertEquals($transactionAttempt->card->balance, $response->json('data.transactionAttempts.data.0.card.balance'));
        $this->assertEquals($transactionAttempt->card->created_at->timestamp, $response->json('data.transactionAttempts.data.0.card.created_at'));
        $this->assertEquals($transactionAttempt->card->updated_at->timestamp, $response->json('data.transactionAttempts.data.0.card.updated_at'));

        // transaction_type
        $this->assertEquals($transactionAttempt->transaction_type->id, $response->json('data.transactionAttempts.data.0.transaction_type.id'));
        $this->assertEquals($transactionAttempt->transaction_type->name, $response->json('data.transactionAttempts.data.0.transaction_type.name'));
        $this->assertEquals($transactionAttempt->transaction_type->name_uz, $response->json('data.transactionAttempts.data.0.transaction_type.name_uz'));
        $this->assertEquals($transactionAttempt->transaction_type->name_ru, $response->json('data.transactionAttempts.data.0.transaction_type.name_ru'));
        $this->assertEquals($transactionAttempt->transaction_type->name_en, $response->json('data.transactionAttempts.data.0.transaction_type.name_en'));
        $this->assertEquals($transactionAttempt->transaction_type->is_system, $response->json('data.transactionAttempts.data.0.transaction_type.is_system'));
        $this->assertEquals($transactionAttempt->transaction_type->created_at?->timestamp, $response->json('data.transactionAttempts.data.0.transaction_type.created_at'));
        $this->assertEquals($transactionAttempt->transaction_type->updated_at?->timestamp, $response->json('data.transactionAttempts.data.0.transaction_type.updated_at'));
        $this->assertEquals($transactionAttempt->transaction_type->deleted_at?->timestamp, $response->json('data.transactionAttempts.data.0.transaction_type.deleted_at'));
    }
}
