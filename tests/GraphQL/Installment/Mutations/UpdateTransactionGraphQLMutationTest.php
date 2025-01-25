<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateTransactionGraphQLMutationTest extends GraphQLTestCase
{
    private const string GRAPHQL_ROUTE = 'graphql.installment';
    private const array STRUCTURE = [
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
    ];

    protected bool $tenancy = true;

    private Transaction $_transaction;
    private array $_input;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_transaction = Transaction::factory()->create();

        $this->_input = [
            'id' => $this->_transaction->id,
            'installment_id' => Installment::factory()->create()?->id,
            'customer_id' => Customer::factory()->create()?->id,
            'transaction_type_id' => TransactionType::factory()->create()?->id,
            'amount' => rand(100, 1000),
            'paid_at' => Carbon::today()->subDays(rand(0, 365))->format('Y-m-d'),
        ];
    }

    #[Test]
    public function successUpdateTransaction()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery($this->_input));

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function successUpdateTransactionWithoutNotRequiredArguments()
    {
        $this->_input = Arr::except($this->_input, ['installment_id', 'customer_id', 'transaction_type_id', 'paid_at']);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery($this->_input));

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorValidation(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery([
                    'id' => 999999999,
                    'installment_id' => 999999999,
                    'customer_id' => 999999999,
                    'transaction_type_id' => 999999999,
                    'amount' => -1,
                    'paid_at' => 'trfdcsx',
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'id']),
            $response->json('errors.0.extensions.validation')['id'][0]
        );
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'installment id']),
            $response->json('errors.0.extensions.validation')['installment_id'][0]
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
            trans('validation.min.numeric', ['attribute' => 'amount', 'min' => '0']),
            $response->json('errors.0.extensions.validation')['amount'][0]
        );
        $this->assertEquals(
            trans('validation.date_format', ['attribute' => 'paid at', 'format' => 'Y-m-d']),
            $response->json('errors.0.extensions.validation')['paid_at'][0]
        );
    }

    #[Test]
    public function successCheckForPermission()
    {
        /** @var Role $role */
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::TRANSACTION_EDIT)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        /** @var Installment $installment */
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id]);
        $this->_input = array_merge($this->_input, ['installment_id' => $installment->id]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery($this->_input));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery($this->_input));
        $this->assertSuccessResponse($response);
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
                    amount: $amount
                    installment_id: $installment_id
                    customer_id: $customer_id
                    transaction_type_id: $transaction_type_id
                    paid_at: $paid_at
                ) {' . $this->convertIntoQuery(self::STRUCTURE) . '}
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
                'updateTransaction' => self::STRUCTURE,
            ],
        ]);

        $this->_transaction->refresh();

        foreach ($this->_input as $key => $value) {
            if ($key == 'paid_at') {
                $value = Carbon::createFromDate($value);
            }
            $this->assertEquals($value, $this->_transaction->{$key});
        }

        $this->assertEquals($this->_transaction->id, $response->json('data.updateTransaction.id'));
        $this->assertEquals($this->_transaction->branch_id, $response->json('data.updateTransaction.branch_id'));
        $this->assertEquals($this->_transaction->admin_id, $response->json('data.updateTransaction.admin_id'));
        $this->assertEquals($this->_transaction->installment_id, $response->json('data.updateTransaction.installment_id'));
        $this->assertEquals($this->_transaction->customer_id, $response->json('data.updateTransaction.customer_id'));
        $this->assertEquals($this->_transaction->transaction_type_id, $response->json('data.updateTransaction.transaction_type_id'));
        $this->assertEquals($this->_transaction->card_id, $response->json('data.updateTransaction.card_id'));
        $this->assertEquals($this->_transaction->card_pan, $response->json('data.updateTransaction.card_pan'));
        $this->assertEquals($this->_transaction->external_id, $response->json('data.updateTransaction.external_id'));
        $this->assertEquals($this->_transaction->amount, (float) $response->json('data.updateTransaction.amount'));
        $this->assertEquals($this->_transaction->paid_at?->timestamp, $response->json('data.updateTransaction.paid_at'));
        $this->assertEquals($this->_transaction->created_at->timestamp, $response->json('data.updateTransaction.created_at'));
        $this->assertEquals($this->_transaction->updated_at->timestamp, $response->json('data.updateTransaction.updated_at'));
    }
}
