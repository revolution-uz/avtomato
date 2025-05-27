<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateTransactionGraphQLMutationTest extends GraphQLTestCase
{
    private const string GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private Installment $_installment;
    private TransactionType $_transactionType;
    private Customer $_customer;

    protected function setUp(): void
    {
        parent::setUp();
        Installment::query()->forceDelete();
        Customer::query()->forceDelete();
        TransactionType::query()->forceDelete();
        $this->_installment = Installment::factory()->create([
            'amount' => 1000000,
            'initial_fee' => 100000,
            'percent' => 15,
            'paid_amount' => 100000,
            'status' => InstallmentStatusEnum::ACTIVE,
        ]);
        $this->_customer = Customer::factory()->create();
        $this->_transactionType = TransactionType::factory()->create();
    }

    #[Test]
    public function successCreateTransaction()
    {
        $paidAt = Carbon::createFromFormat('Y-m-d', '2020-01-01')->setTime(now()->hour, now()->minute);

        $response = $this->sendGraphQLRequest(
            $this->_installment->id,
            $this->_customer->id,
            $this->_transactionType->id,
            rand(0, 935000),
            $paidAt->format('Y-m-d'),
        );
        $this->assertSuccessResponse($response, $paidAt);
    }

    #[Test]
    public function successCreateTransactionWithoutPaidAt()
    {
        $response = $this->sendGraphQLRequest(
            $this->_installment->id,
            $this->_customer->id,
            $this->_transactionType->id,
            rand(0, 935000)
        );
        $this->assertSuccessResponse($response, Carbon::now());
    }

    #[Test]
    public function successCreateTransactionWithInactiveInstallment()
    {
        $this->_installment = Installment::factory()->create(['status' => InstallmentStatusEnum::INACTIVE]);
        $response = $this->sendGraphQLRequest(
            $this->_installment->id,
            $this->_customer->id,
            $this->_transactionType->id,
            $this->_installment->initial_fee,
            '2021-01-01'
        );
        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorValidation(): void
    {
        $response = $this->sendGraphQLRequest(
            312,
            234,
            123,
            '1000',
            '01-01-0000'
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'installment id']),
            $response->json('errors.0.extensions.validation')['installment_id'][0]
        );
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'customer id']),
            $response->json('errors.0.extensions.validation')['customer_id'][0]
        );
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'customer id']),
            $response->json('errors.0.extensions.validation')['customer_id'][0]
        );
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'transaction type id']),
            $response->json('errors.0.extensions.validation')['transaction_type_id'][0]
        );
        $this->assertEquals(
            trans('validation.date_format', ['attribute' => 'paid at', 'format' => 'Y-m-d']),
            $response->json('errors.0.extensions.validation')['paid_at'][0]
        );
    }

    #[Test]
    public function dontCreateTransactionIfAmountIsLarge(): void
    {
        $msgs = [
            'ru' => 'Сумма не может быть более 935000.',
            'en' => 'Amount must not be greater than 935000.',
            'uz' => 'Kiritiladigan miqdor 935000 dan katta bo‘lmasligi kerak.',
            'oz' => 'Киритиладиган миқдор 935000 дан катта бўлмаслиги керак.',
        ];

        foreach (config('app.supported_locales') as $locale) {
            $response = $this->sendGraphQLRequest(
                $this->_installment->id,
                $this->_customer->id,
                $this->_transactionType->id,
                935001,
                '2021-01-01',
                ['Accept-Language' => $locale],
            );

            $response->assertOk();
            $this->assertArrayHasKey('errors', $response->json());
            $this->assertStringContainsString('validation', $response->json('errors.0.message'));
            $this->assertEquals(
                $msgs[$locale],
                $response->json('errors.0.extensions.validation.amount.0')
            );
        }
    }

    #[Test]
    public function errorCreateTransactionWithInactiveInstallment()
    {
        $this->_installment = Installment::factory()->create(['status' => InstallmentStatusEnum::INACTIVE]);

        $response = $this->sendGraphQLRequest(
            $this->_installment->id,
            $this->_customer->id,
            $this->_transactionType->id,
            $this->_installment->initial_fee - 1,
            '2021-01-01'
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            __('api.Please create initial fee amount first'),
            $response->json('errors.0.extensions.validation.amount.0')
        );

        $response = $this->sendGraphQLRequest(
            $this->_installment->id,
            $this->_customer->id,
            $this->_transactionType->id,
            $this->_installment->initial_fee + 1,
            '2021-01-01'
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            __('api.Please create initial fee amount first'),
            $response->json('errors.0.extensions.validation.amount.0')
        );
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::TRANSACTION_CREATE)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $this->_installment = Installment::factory()->create(['branch_id' => $branches[1]->id, 'status' => InstallmentStatusEnum::ACTIVE]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery(
                    $this->_installment->id,
                    $this->_customer->id,
                    $this->_transactionType->id,
                    rand(0, 935000),
                    '2021-01-01'
                ),
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery(
                    $this->_installment->id,
                    $this->_customer->id,
                    $this->_transactionType->id,
                    rand(0, 935000),
                    '2021-01-01'
                ),
            );
        $this->assertSuccessResponse($response);
    }

    private function sendGraphQLRequest(int $installment_id, int $customer_id, int $transaction_type_id, float $amount, ?string $paid_at = null, array $headers = []): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment_id, $customer_id, $transaction_type_id, $amount, $paid_at),
                $headers
            );
    }

    private function buildGraphQLQuery(int $installment_id, int $customer_id, int $transaction_type_id, float $amount, ?string $paid_at): array
    {
        return [
            'variables' => [
                'installment_id' => $installment_id,
                'customer_id' => $customer_id,
                'transaction_type_id' => $transaction_type_id,
                'amount' => $amount,
                'paid_at' => $paid_at,
            ],
            'query' => '
            mutation ($installment_id: Int!, $customer_id: Int!, $transaction_type_id: Int!, $amount: BigFloat!, $paid_at: String) {
                createTransaction(
                    installment_id: $installment_id,
                    customer_id: $customer_id,
                    transaction_type_id: $transaction_type_id,
                    amount: $amount,
                    paid_at: $paid_at
                ) {
                    id
                    branch_id
                    branch {
                        id
                        name
                        address
                        created_at
                        updated_at
                    }
                    admin_id
                    admin {
                        id
                        first_name
                        last_name
                        created_at
                        updated_at
                    }
                    installment_id
                    installment {
                        id
                        cards {
                            id
                            customer_id
                            card_type
                            owner
                            expiry
                            is_connected
                            status
                            balance
                            created_at
                            updated_at
                        }
                        amount
                        created_at
                        updated_at
                    }
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
                    }
                    transaction_type_id
                    transaction_type {
                        id
                        name
                        created_at
                        updated_at
                    }
                    external_id
                    amount
                    paid_at
                    created_at
                    updated_at
                }
            }
        ',
        ];
    }

    private function assertSuccessResponse(TestResponse $response, ?Carbon $paidAt = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $branch = [
            'id',
            'name',
            'address',
            'created_at',
            'updated_at',
        ];
        $admin = [
            'id',
            'first_name',
            'last_name',
            'created_at',
            'updated_at',
        ];
        $installment = [
            'id',
            'cards' => [
                '*' => [
                    'id',
                    'customer_id',
                    'card_type',
                    'owner',
                    'expiry',
                    'is_connected',
                    'status',
                    'balance',
                    'created_at',
                    'updated_at',
                ],
            ],
            'amount',
            'created_at',
            'updated_at',
        ];
        $customer = [
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
        ];
        $transactionType = [
            'id',
            'name',
            'created_at',
            'updated_at',
        ];
        $response->assertJsonStructure([
            'data' => [
                'createTransaction' => [
                    'id',
                    'branch_id',
                    'branch' => $branch,
                    'admin_id',
                    'admin' => $admin,
                    'installment_id',
                    'installment' => $installment,
                    'customer_id',
                    'customer' => $customer,
                    'transaction_type_id',
                    'transaction_type' => $transactionType,
                    'external_id',
                    'amount',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $transaction = Transaction::query()->find($response->json('data.createTransaction.id'));
        $this->assertNotNull($transaction);
        $this->assertSuccessInstallmentResponse($response, $transaction);
        $this->assertSuccessAdminResponse($response, $transaction);
        $this->assertSuccessCustomerResponse($response, $transaction);
        $this->assertSuccessTransactionTypeResponse($response, $transaction);
        if ($paidAt) {
            $this->assertEquals($paidAt->format('Y-m-d H:i'), $transaction->paid_at->format('Y-m-d H:i'));
        }
    }

    private function assertSuccessInstallmentResponse($response, $transaction): void
    {

        $this->assertEquals($transaction->installment->branch_id, $response->json('data.createTransaction.branch_id'));
        $this->assertEquals($transaction->installment->branch->id, $response->json('data.createTransaction.branch.id'));
        $this->assertEquals($transaction->installment->branch->name, $response->json('data.createTransaction.branch.name'));
        $this->assertEquals($transaction->installment->branch->address, $response->json('data.createTransaction.branch.address'));
        $this->assertEquals($transaction->installment->branch->created_at->timestamp, $response->json('data.createTransaction.branch.created_at'));
        $this->assertEquals($transaction->installment->branch->updated_at->timestamp, $response->json('data.createTransaction.branch.updated_at'));

        // this installment

        $this->assertEquals($this->_installment->branch_id, $response->json('data.createTransaction.branch_id'));
        $this->assertEquals($this->_installment->branch->id, $response->json('data.createTransaction.branch.id'));
        $this->assertEquals($this->_installment->branch->name, $response->json('data.createTransaction.branch.name'));
        $this->assertEquals($this->_installment->branch->address, $response->json('data.createTransaction.branch.address'));
        $this->assertEquals($this->_installment->branch->created_at->timestamp, $response->json('data.createTransaction.branch.created_at'));
        $this->assertEquals($this->_installment->branch->updated_at->timestamp, $response->json('data.createTransaction.branch.updated_at'));
    }

    private function assertSuccessAdminResponse($response, $transaction): void
    {
        if ($transaction->admin_id) {
            $this->assertEquals($transaction->admin_id, $response->json('data.createTransaction.admin_id'));
            $this->assertEquals($transaction->admin->id, $response->json('data.createTransaction.admin.id'));
            $this->assertEquals($transaction->admin->id, $response->json('data.createTransaction.admin.id'));
        }
    }

    private function assertSuccessCustomerResponse(TestResponse $response, Transaction $transaction): void
    {
        $this->assertEquals($transaction->customer_id, $response->json('data.createTransaction.customer_id'));
        $this->assertEquals($transaction->customer_id, $response->json('data.createTransaction.customer.id'));
        $this->assertEquals($transaction->customer->pinfl, $response->json('data.createTransaction.customer.pinfl'));
        $this->assertEquals($transaction->customer->tin, $response->json('data.createTransaction.customer.tin'));
        $this->assertEquals($transaction->customer->passport_series, $response->json('data.createTransaction.customer.passport_series'));
        $this->assertEquals($transaction->customer->passport_number, $response->json('data.createTransaction.customer.passport_number'));
        $this->assertEquals($transaction->customer->passport_given_date->timestamp, $response->json('data.createTransaction.customer.passport_given_date'));
        $this->assertEquals($transaction->customer->passport_expiration_date->timestamp, $response->json('data.createTransaction.customer.passport_expiration_date'));
        $this->assertEquals($transaction->customer->passport_given_place, $response->json('data.createTransaction.customer.passport_given_place'));
        $this->assertEquals($transaction->customer->passport_citizenship, $response->json('data.createTransaction.customer.passport_citizenship'));
        $this->assertEquals($transaction->customer->first_name, $response->json('data.createTransaction.customer.first_name'));
        $this->assertEquals($transaction->customer->last_name, $response->json('data.createTransaction.customer.last_name'));
        $this->assertEquals($transaction->customer->middle_name, $response->json('data.createTransaction.customer.middle_name'));
        $this->assertEquals($transaction->customer->birth_date->timestamp, $response->json('data.createTransaction.customer.birth_date'));
        $this->assertEquals($transaction->customer->birth_place, $response->json('data.createTransaction.customer.birth_place'));
    }

    private function assertSuccessTransactionTypeResponse($response, $transaction): void
    {
        $this->assertEquals($transaction->transactionType->id, $response->json('data.createTransaction.transaction_type_id'));
        $this->assertEquals($transaction->transactionType->name, $response->json('data.createTransaction.transaction_type.name'));
        $this->assertEquals($transaction->transactionType->created_at->timestamp, $response->json('data.createTransaction.transaction_type.created_at'));
        $this->assertEquals($transaction->transactionType->updated_at->timestamp, $response->json('data.createTransaction.transaction_type.updated_at'));
    }
}
