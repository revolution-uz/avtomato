<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleTransactionTypeGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private TransactionType $_transaction_type;

    protected function setUp(): void
    {
        parent::setUp();

        TransactionType::query()->forceDelete();
        $this->_transaction_type = TransactionType::factory()->create();
        Transaction::factory()->count(5)->create(['transaction_type_id' => $this->_transaction_type->id]);
    }

    #[Test]
    public function successSingleTransactionType()
    {
        $response = $this->sendToGraphQL($this->_transaction_type->id);
        $this->assertSuccessSingleTransactionTypeResponse($response);
    }

    #[Test]
    public function errorSingleTransactionType()
    {
        // not found
        $response = $this->sendToGraphQL(999);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // deleted
        $this->_transaction_type->delete();
        $response = $this->sendToGraphQL($this->_transaction_type->id);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery($id): array
    {
        $query = [];
        $query['variables'] = ['id' => $id];
        $query['query'] = '
            query ($id: Int!) {
                singleTransactionType(id: $id) {
                    id
                    name
                    name_uz
                    name_ru
                    name_en
                    is_system
                    created_at
                    updated_at
                    deleted_at
                    transactions {
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
            }
        ';

        return $query;
    }

    private function assertSuccessSingleTransactionTypeResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleTransactionType' => [
                    'id',
                    'name',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'is_system',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'transactions' => [
                        '*' => [
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
                ],
            ],
        ]);
        $transactionType = TransactionType::query()->find($response->json('data.singleTransactionType.id'));
        $this->assertNotNull($transactionType);
        $transactionType = $this->_transaction_type;
        $this->assertEquals($transactionType->id, $response->json('data.singleTransactionType.id'));
        $this->assertEquals($transactionType->name, $response->json('data.singleTransactionType.name'));
        $this->assertEquals($transactionType->name_uz, $response->json('data.singleTransactionType.name_uz'));
        $this->assertEquals($transactionType->name_en, $response->json('data.singleTransactionType.name_en'));
        $this->assertEquals($transactionType->name_ru, $response->json('data.singleTransactionType.name_ru'));
        $this->assertEquals($transactionType->is_system, $response->json('data.singleTransactionType.is_system'));
        $this->assertEquals($transactionType->created_at->timestamp, $response->json('data.singleTransactionType.created_at'));
        $this->assertEquals($transactionType->updated_at->timestamp, $response->json('data.singleTransactionType.updated_at'));

        $this->assertEquals(5, count($response->json('data.singleTransactionType.transactions')));
        $this->assertEquals($transactionType->transactions[0]->id, $response->json('data.singleTransactionType.transactions.0.id'));
        $this->assertEquals($transactionType->transactions[0]->installment->branch_id, $response->json('data.singleTransactionType.transactions.0.branch_id'));
        $this->assertEquals($transactionType->transactions[0]->admin_id, $response->json('data.singleTransactionType.transactions.0.admin_id'));
        $this->assertEquals($transactionType->transactions[0]->installment_id, $response->json('data.singleTransactionType.transactions.0.installment_id'));
        $this->assertEquals($transactionType->transactions[0]->customer_id, $response->json('data.singleTransactionType.transactions.0.customer_id'));
        $this->assertEquals($transactionType->transactions[0]->transaction_type_id, $response->json('data.singleTransactionType.transactions.0.transaction_type_id'));
        $this->assertEquals($transactionType->transactions[0]->card_id, $response->json('data.singleTransactionType.transactions.0.card_id'));
        $this->assertEquals($transactionType->transactions[0]->external_id, $response->json('data.singleTransactionType.transactions.0.external_id'));
        $this->assertEquals($transactionType->transactions[0]->amount, $response->json('data.singleTransactionType.transactions.0.amount'));
        $this->assertEquals($transactionType->transactions[0]->paid_at?->timestamp, $response->json('data.singleTransactionType.transactions.0.paid_at'));
        $this->assertEquals($transactionType->transactions[0]->created_at->timestamp, $response->json('data.singleTransactionType.transactions.0.created_at'));
        $this->assertEquals($transactionType->transactions[0]->updated_at->timestamp, $response->json('data.singleTransactionType.transactions.0.updated_at'));
    }

    private function sendToGraphQL($id): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($id));
    }
}
