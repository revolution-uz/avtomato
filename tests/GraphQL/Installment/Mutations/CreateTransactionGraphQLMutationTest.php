<?php

namespace GraphQL\Installment\Mutations;

use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateTransactionGraphQLMutationTest extends GraphQLTestCase
{
    private const GRAPHQL_ROUTE = 'graphql.installment';

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
        $this->_installment = Installment::factory()->create();
        $this->_customer = Customer::factory()->create();
        $this->_transactionType = TransactionType::factory()->create();
    }

    #[Test]
    public function successCreateTransaction()
    {
        $response = $this->sendGraphQLRequest(
            $this->_installment->id,
            $this->_customer->id,
            $this->_transactionType->id,
            1000,
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
            '0000-01-01'
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
            trans('validation.date', ['attribute' => 'paid at']),
            $response->json('errors.0.extensions.validation')['paid_at'][0]
        );
    }

    #[Test]
    public function dontCreateTransactionIfAmountIsLarge(): void
    {
        $response = $this->sendGraphQLRequest(
            $this->_installment->id,
            $this->_customer->id,
            $this->_transactionType->id,
            $this->_installment->amount * 2,
            '2021-01-01'
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            'The amount must not be greater than the installment amount.',
            $response->json('errors.0.extensions.validation')['amount'][0]
        );
    }

    private function sendGraphQLRequest(int $installment_id, int $customer_id, int $transaction_type_id, int $amount, ?string $paid_at): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment_id, $customer_id, $transaction_type_id, $amount, $paid_at)
            );
    }

    private function buildGraphQLQuery(int $installment_id, int $customer_id, int $transaction_type_id, int $amount, ?string $paid_at): array
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
            mutation ($installment_id: Int!, $customer_id: Int!, $transaction_type_id: Int!, $amount: BigFloat!, $paid_at: String!) {
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

    private function assertSuccessResponse(TestResponse $response): void
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

    private function assertSuccessCustomerResponse($response, $transaction): void
    {
        $this->assertEquals($transaction->customer_id, $response->json('data.createTransaction.customer_id'));
        $this->assertEquals($transaction->customer_id, $response->json('data.createTransaction.customer.id'));
        $this->assertEquals($transaction->customer->pinfl, $response->json('data.createTransaction.customer.pinfl'));
        $this->assertEquals($transaction->customer->tin, $response->json('data.createTransaction.customer.tin'));
        $this->assertEquals($transaction->customer->passport_series, $response->json('data.createTransaction.customer.passport_series'));
        $this->assertEquals($transaction->customer->passport_number, $response->json('data.createTransaction.customer.passport_number'));
        $this->assertEquals($transaction->customer->passport_given_date->format('d-m-Y'), $response->json('data.createTransaction.customer.passport_given_date'));
        $this->assertEquals($transaction->customer->passport_expiration_date->format('d-m-Y'), $response->json('data.createTransaction.customer.passport_expiration_date'));
        $this->assertEquals($transaction->customer->passport_given_place, $response->json('data.createTransaction.customer.passport_given_place'));
        $this->assertEquals($transaction->customer->passport_citizenship, $response->json('data.createTransaction.customer.passport_citizenship'));
        $this->assertEquals($transaction->customer->first_name, $response->json('data.createTransaction.customer.first_name'));
        $this->assertEquals($transaction->customer->last_name, $response->json('data.createTransaction.customer.last_name'));
        $this->assertEquals($transaction->customer->middle_name, $response->json('data.createTransaction.customer.middle_name'));
        $this->assertEquals($transaction->customer->birth_date->format('d-m-Y'), $response->json('data.createTransaction.customer.birth_date'));
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
