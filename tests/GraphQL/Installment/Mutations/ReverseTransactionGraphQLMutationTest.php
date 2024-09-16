<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Feature;
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
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class ReverseTransactionGraphQLMutationTest extends GraphQLTestCase
{
    private const string GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private Transaction $_transaction;

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

        $this->_transaction = Transaction::query()->where('transaction_type_id', '<', 100)->inRandomOrder()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery(['id' => $this->_transaction->id]));

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function successReverseSimpleTransaction()
    {
        $this->_transaction = Transaction::query()->where('transaction_type_id', '>=', 100)->inRandomOrder()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery(['id' => $this->_transaction->id]));

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery(['id' => null])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery(['id' => 999999999])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // already cancelled transaction
        $this->_transaction = Transaction::query()->where('transaction_type_id', '>=', 100)->inRandomOrder()->first();
        $this->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery(['id' => $this->_transaction->id])
            );
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery(['id' => $this->_transaction->id])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('api.already_canceled_transaction'), $response->json('errors.0.extensions.validation.id.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::TRANSACTION_REVERSE)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id]);
        $this->_transaction = Transaction::factory()->create(['installment_id' => $installment->id]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery(['id' => $this->_transaction->id]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery(['id' => $this->_transaction->id]));
        $this->assertSuccessResponse($response);
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

    private function assertSuccessResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'reverseTransaction',
            ],
        ]);
        $this->assertTrue($response->json('data.reverseTransaction'));
        $transaction = Transaction::query()->find($this->_transaction->id);
        $this->assertTrue($response->json('data.reverseTransaction'));
        $this->assertNotNull($transaction);
        $this->assertTrue($transaction->installment->status === InstallmentStatusEnum::CANCELED);
    }
}
