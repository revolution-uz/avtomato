<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteTransactionGraphQLMutationTest extends GraphQLTestCase
{
    private const string GRAPHQL_ROUTE = 'graphql.installment';

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
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery(['ids' => $idsInput]));

        $this->assertSuccessResponse($response, $idsInput);
    }

    #[Test]
    public function errorSystemPaymentTransactions(): void
    {
        $idsInput = Transaction::query()->where('transaction_type_id', '<', 100)
            ->inRandomOrder()->limit(1)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery(['ids' => $idsInput]));

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
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery(['ids' => null]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$ids" of non-null type "[Int]!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery(['ids' => [999999999]]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::TRANSACTION_DELETE)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id]);
        $transactions = Transaction::factory()->count(5)->create(['installment_id' => $installment->id]);
        $idsInput = $transactions->take(3)->pluck('id')->toArray();

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery(['ids' => $idsInput]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.1']), $response->json('errors.0.extensions.validation')['ids.1'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.2']), $response->json('errors.0.extensions.validation')['ids.2'][0]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery(['ids' => $idsInput]));
        $this->assertSuccessResponse($response, $idsInput);
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

    private function assertSuccessResponse(TestResponse $response, array $idsInput): void
    {
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
}
