<?php

namespace Installment\Mutations;

use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteTransactionTypeGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        TransactionType::query()->forceDelete();
        Artisan::call('tenants:seed', ['--class' => TransactionTypeSeeder::class, '--tenants' => [tenant()->id]]);
        Transaction::factory()->count(5)->create();
        TransactionType::factory()->count(5)->create();
    }

    #[Test]
    public function successDeleteTransactionType()
    {
        $idsInput = TransactionType::query()->where('id', '>=', 100)
            ->whereDoesntHave('transactions')->inRandomOrder()->limit(3)->pluck('id')->toArray();

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
                'deleteTransactionType',
            ],
        ]);

        $this->assertTrue($response->json('data.deleteTransactionType'));
        $this->assertTrue(TransactionType::query()->whereIn('id', $idsInput)->doesntExist());
    }

    #[Test]
    public function errorDeletingSystemTransactionTypes(): void
    {
        $transactionType = TransactionType::query()->where('id', '<', 100)
            ->whereDoesntHave('transactions')->inRandomOrder()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['ids' => $transactionType->id])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.You cannot delete', ['item' => $transactionType->name]), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    #[Test]
    public function errorWhenHasTransactions(): void
    {
        $transactionType = TransactionType::query()->where('id', '>=', 100)
            ->whereHas('transactions')->inRandomOrder()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['ids' => $transactionType->id])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('api.are used', ['item' => __('api.Transaction Type'), 'element' => __('api.Transaction')]), $response->json('errors.0.extensions.validation')['ids.0'][0]);
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$ids" of non-null type "[Int]!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'ids' => [999999999],
                ])
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
            mutation DeleteTransactionType($ids: [Int]!) {
                deleteTransactionType(ids: $ids)
            }
        ';

        return $query;
    }
}
