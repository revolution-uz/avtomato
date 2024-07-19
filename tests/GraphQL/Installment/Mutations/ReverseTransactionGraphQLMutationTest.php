<?php

namespace Installment\Mutations;

use App\Models\Company\Feature;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class ReverseTransactionGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('tenants:seed', ['--class' => TransactionTypeSeeder::class, '--tenants' => [tenant()->id]]);
        $transactionType = TransactionType::query()->where('id', '<', 100)->first();
        Transaction::factory()->count(5)->create(['transaction_type_id' => $transactionType->id]);
        Transaction::factory()->count(5)->create();
        $feature = Feature::findByName('atmos_card_pay');
        $feature->data = json_encode([
            'username' => 'username',
            'password' => 'password',
            'store_id' => '1',
            'expires_in' => 3600,
            'access_token' => 'access_token',
            'token_type' => 'token_type',
        ]);
        $feature->saveOrFail();
    }

    #[Test]
    public function successReverseTransaction()
    {
        AtmosFaker::getToken();
        AtmosFaker::reversePayment();

        $transaction_id = Transaction::query()->where('transaction_type_id', '>=', 100)->inRandomOrder()->first()->id;

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['id' => $transaction_id])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'reverseTransaction',
            ],
        ]);
        $this->assertTrue($response->json('data.reverseTransaction'));
        $this->assertTrue(Transaction::query()->where('id', $transaction_id)->doesntExist());
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['id' => null])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['id' => 999999999])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation ReverseTransaction($id: Int!) {
                reverseTransaction(id: $id)
            }
        ';

        return $query;
    }
}
