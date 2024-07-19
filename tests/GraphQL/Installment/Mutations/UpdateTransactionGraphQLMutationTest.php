<?php

namespace Installment\Mutations;

use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateTransactionGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('tenants:seed', ['--class' => TransactionTypeSeeder::class, '--tenants' => [tenant()->id]]);
        $transactionType = TransactionType::query()->where('id', '<', 100)->first();
        Transaction::factory()->count(5)->create(['transaction_type_id' => $transactionType->id]);
        Transaction::factory()->count(5)->create();
    }

    #[Test]
    public function successUpdateTransaction()
    {
        $transaction = Transaction::query()->where('transaction_type_id', '>=', 100)->inRandomOrder()->first();
        $installment = Installment::factory()->create();
        $customer = Customer::factory()->create();
        $transactionType = TransactionType::factory()->create();

        $installmentInput = [
            'id' => $transaction->id,
            'installment_id' => $installment->id,
            'customer_id' => $customer->id,
            'transaction_type_id' => $transactionType->id,
            'amount' => 1000000,
            'paid_at' => date('Y-m-d'),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($installmentInput)
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateTransaction' => [
                    'id',
                    'branch_id',
                    'admin_id',
                    'installment_id',
                    'customer_id',
                    'transaction_type_id',
                    'card_id',
                    'external_id',
                    'amount',
                    'paid_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $transaction->refresh();
        $this->assertEquals($transaction->installment_id, $installmentInput['installment_id']);
        $this->assertEquals($transaction->customer_id, $installmentInput['customer_id']);
        $this->assertEquals($transaction->transaction_type_id, $installmentInput['transaction_type_id']);
        $this->assertEquals($transaction->amount, $installmentInput['amount']);
        $this->assertEquals($transaction->paid_at->format('Y-m-d'), $installmentInput['paid_at']);

        $this->assertEquals($transaction->id, $response->json('data.updateTransaction.id'));
        $this->assertEquals($transaction->installment->branch_id, $response->json('data.updateTransaction.branch_id'));
        $this->assertEquals($transaction->admin_id, $response->json('data.updateTransaction.admin_id'));
        $this->assertEquals($transaction->installment_id, $response->json('data.updateTransaction.installment_id'));
        $this->assertEquals($transaction->customer_id, $response->json('data.updateTransaction.customer_id'));
        $this->assertEquals($transaction->transaction_type_id, $response->json('data.updateTransaction.transaction_type_id'));
        $this->assertEquals($transaction->card_id, $response->json('data.updateTransaction.card_id'));
        $this->assertEquals($transaction->external_id, $response->json('data.updateTransaction.external_id'));
        $this->assertEquals((float)$transaction->amount, (float)$response->json('data.updateTransaction.amount'));
        $this->assertEquals($transaction->paid_at->timestamp, $response->json('data.updateTransaction.paid_at'));
        $this->assertEquals($transaction->created_at->timestamp, $response->json('data.updateTransaction.created_at'));
        $this->assertEquals($transaction->updated_at->timestamp, $response->json('data.updateTransaction.updated_at'));
    }

    #[Test]
    public function errorUpdateSystemTransactionTypes(): void
    {
        $transaction = Transaction::query()->where('transaction_type_id', '<', 100)->inRandomOrder()->first();

        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => $transaction->id,
                    'amount' => 1000000,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }


    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => null,
                    'installment_id' => null,
                    'customer_id' => null,
                    'transaction_type_id' => null,
                    'amount' => null,
                    'paid_at' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$amount" of non-null type "BigFloat!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => 999999999,
                    'installment_id' => 999999999,
                    'customer_id' => 999999999,
                    'transaction_type_id' => 999999999,
                    'amount' => 1000000,
                    'paid_at' => 'qwertyuiop',
                ])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'customer id']), $response->json('errors.0.extensions.validation.customer_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'transaction type id']), $response->json('errors.0.extensions.validation.transaction_type_id.0'));
        $this->assertEquals(trans('validation.date', ['attribute' => 'paid at']), $response->json('errors.0.extensions.validation.paid_at.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateTransaction(
                $id: Int!
                $installment_id: Int
                $customer_id: Int
                $transaction_type_id: Int
                $amount: BigFloat!
                $paid_at: String
            ) {
                updateTransaction(
                    id: $id
                    installment_id: $installment_id
                    customer_id: $customer_id
                    transaction_type_id: $transaction_type_id
                    amount: $amount
                    paid_at: $paid_at
                ) {
                    id
                    branch_id
                    admin_id
                    installment_id
                    customer_id
                    transaction_type_id
                    card_id
                    external_id
                    amount
                    paid_at
                    created_at
                    updated_at
                }
            }
        ';

        return $query;
    }
}
