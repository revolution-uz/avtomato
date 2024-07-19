<?php

namespace Installment\Mutations;

use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteTransactionGraphQLMutationTest extends GraphQLTestCase
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
    public function successDeleteTransaction()
    {
        $idsInput = Transaction::query()->where('transaction_type_id', '>=', 100)
            ->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'deleteTransaction',
            ],
        ]);

        $this->assertTrue($response->json('data.deleteTransaction'));
        $this->assertTrue(Transaction::query()->whereIn('id', $idsInput)->doesntExist());
    }

    #[Test]
    public function errorSystemPaymentTransactions(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'ids' => Transaction::query()->where('transaction_type_id', '<', 100)
                        ->inRandomOrder()->limit(1)->pluck('id')->toArray()
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.it is system payment'), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }


    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['ids' => null])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$ids" of non-null type "[Int]!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['ids' => [999999999]])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.not found'), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DeleteTransaction($ids: [Int]!) {
                deleteTransaction(ids: $ids)
            }
        ';

        return $query;
    }
}
