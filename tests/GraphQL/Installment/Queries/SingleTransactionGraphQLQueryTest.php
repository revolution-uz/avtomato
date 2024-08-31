<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleTransactionGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Installment $_installment;
    private Transaction $_transaction;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::query()->forceDelete();
        Transaction::query()->forceDelete();
        $this->_installment = Installment::factory()->create();
        $this->_transaction = Transaction::factory()->create(['installment_id' => $this->_installment->id]);
    }

    #[Test]
    public function successSingleTransaction()
    {
        $response = $this->sendToGraphQL($this->_transaction->id);
        $this->assertSuccessSingleTransactionResponse($response);
    }

    #[Test]
    public function errorSingleTransaction()
    {
        // not found
        $response = $this->sendToGraphQL(999);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // deleted
        $this->_transaction->delete();
        $response = $this->sendToGraphQL($this->_transaction->id);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::TRANSACTION_VIEW)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $this->_installment = Installment::factory()->create(['branch_id' => $branches[1]->id]);
        $this->_transaction = Transaction::factory()->create(['installment_id' => $this->_installment->id]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($this->_transaction->id));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($this->_transaction->id));
        $this->assertSuccessSingleTransactionResponse($response);
    }

    private function buildGraphQLQuery($id): array
    {
        $query = [];
        $query['variables'] = ['id' => $id];
        $query['query'] = '
            query ($id: Int!) {
                singleTransaction(id: $id) {
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
                    card_id
                    card {
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
                    external_id
                    amount
                    created_at
                    updated_at
                }
            }
        ';

        return $query;
    }

    private function assertSuccessSingleTransactionResponse(TestResponse $response): void
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
        $card = [
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
        if ($response->json('data.singleTransaction.admin_id') == null) {
            $admin = [];
        }
        $response->assertJsonStructure([
            'data' => [
                'singleTransaction' => [
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
                    'card_id',
                    'card' => $card,
                    'external_id',
                    'amount',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $transaction = Transaction::query()->find($response->json('data.singleTransaction.id'));
        $this->assertNotNull($transaction);
        $this->assertSuccessInstallmentResponse($response);
        $this->assertSuccessAdminResponse($response);
        $this->assertSuccessCardResponse($response);
        $this->assertSuccessCustomerResponse($response);
        $this->assertSuccessTransactionTypeResponse($response);
    }

    private function sendToGraphQL($id): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($id));
    }

    private function assertSuccessInstallmentResponse($response): void
    {

        $transaction = $this->_transaction;
        $this->assertEquals($transaction->installment->branch_id, $response->json('data.singleTransaction.branch_id'));
        $this->assertEquals($transaction->installment->branch->id, $response->json('data.singleTransaction.branch.id'));
        $this->assertEquals($transaction->installment->branch->name, $response->json('data.singleTransaction.branch.name'));
        $this->assertEquals($transaction->installment->branch->address, $response->json('data.singleTransaction.branch.address'));
        $this->assertEquals($transaction->installment->branch->created_at->timestamp, $response->json('data.singleTransaction.branch.created_at'));
        $this->assertEquals($transaction->installment->branch->updated_at->timestamp, $response->json('data.singleTransaction.branch.updated_at'));

        // this installment

        $this->assertEquals($this->_installment->branch_id, $response->json('data.singleTransaction.branch_id'));
        $this->assertEquals($this->_installment->branch->id, $response->json('data.singleTransaction.branch.id'));
        $this->assertEquals($this->_installment->branch->name, $response->json('data.singleTransaction.branch.name'));
        $this->assertEquals($this->_installment->branch->address, $response->json('data.singleTransaction.branch.address'));
        $this->assertEquals($this->_installment->branch->created_at->timestamp, $response->json('data.singleTransaction.branch.created_at'));
        $this->assertEquals($this->_installment->branch->updated_at->timestamp, $response->json('data.singleTransaction.branch.updated_at'));
    }

    private function assertSuccessAdminResponse($response): void
    {
        $transaction = $this->_transaction;
        if ($transaction->admin_id) {
            $this->assertEquals($transaction->admin_id, $response->json('data.singleTransaction.admin_id'));
            $this->assertEquals($transaction->admin->id, $response->json('data.singleTransaction.admin.id'));
            $this->assertEquals($transaction->admin->id, $response->json('data.singleTransaction.admin.id'));
        }
    }

    private function assertSuccessCustomerResponse($response): void
    {
        $transaction = $this->_transaction;
        $this->assertEquals($transaction->customer_id, $response->json('data.singleTransaction.customer_id'));
        $this->assertEquals($transaction->customer_id, $response->json('data.singleTransaction.customer.id'));
        $this->assertEquals($transaction->customer->pinfl, $response->json('data.singleTransaction.customer.pinfl'));
        $this->assertEquals($transaction->customer->tin, $response->json('data.singleTransaction.customer.tin'));
        $this->assertEquals($transaction->customer->passport_series, $response->json('data.singleTransaction.customer.passport_series'));
        $this->assertEquals($transaction->customer->passport_number, $response->json('data.singleTransaction.customer.passport_number'));
        $this->assertEquals($transaction->customer->passport_given_date->format('d-m-Y'), $response->json('data.singleTransaction.customer.passport_given_date'));
        $this->assertEquals($transaction->customer->passport_expiration_date->format('d-m-Y'), $response->json('data.singleTransaction.customer.passport_expiration_date'));
        $this->assertEquals($transaction->customer->passport_given_place, $response->json('data.singleTransaction.customer.passport_given_place'));
        $this->assertEquals($transaction->customer->passport_citizenship, $response->json('data.singleTransaction.customer.passport_citizenship'));
        $this->assertEquals($transaction->customer->first_name, $response->json('data.singleTransaction.customer.first_name'));
        $this->assertEquals($transaction->customer->last_name, $response->json('data.singleTransaction.customer.last_name'));
        $this->assertEquals($transaction->customer->middle_name, $response->json('data.singleTransaction.customer.middle_name'));
        $this->assertEquals($transaction->customer->birth_date->format('d-m-Y'), $response->json('data.singleTransaction.customer.birth_date'));
        $this->assertEquals($transaction->customer->birth_place, $response->json('data.singleTransaction.customer.birth_place'));
    }

    private function assertSuccessCardResponse($response): void
    {
        $transaction = $this->_transaction;
        $this->assertEquals($transaction->card->id, $response->json('data.singleTransaction.card_id'));
        $this->assertEquals($transaction->card->customer_id, $response->json('data.singleTransaction.card.customer_id'));
        $this->assertEquals($transaction->card->card_type, $response->json('data.singleTransaction.card.card_type'));
        $this->assertEquals($transaction->card->owner, $response->json('data.singleTransaction.card.owner'));
        $this->assertEquals($transaction->card->expiry, $response->json('data.singleTransaction.card.expiry'));
        $this->assertEquals($transaction->card->is_connected, $response->json('data.singleTransaction.card.is_connected'));
        $this->assertEquals(CardStatusEnum::tryFrom($transaction->card->status->value)->value, $response->json('data.singleTransaction.card.status'));
        $this->assertEquals($transaction->card->balance, $response->json('data.singleTransaction.card.balance'));
        $this->assertEquals($transaction->card->created_at->timestamp, $response->json('data.singleTransaction.card.created_at'));
        $this->assertEquals($transaction->card->updated_at->timestamp, $response->json('data.singleTransaction.card.updated_at'));
    }

    private function assertSuccessTransactionTypeResponse($response): void
    {
        $transaction = $this->_transaction;
        $this->assertEquals($transaction->transactionType->id, $response->json('data.singleTransaction.transaction_type_id'));
        $this->assertEquals($transaction->transactionType->name, $response->json('data.singleTransaction.transaction_type.name'));
        $this->assertEquals($transaction->transactionType->created_at->timestamp, $response->json('data.singleTransaction.transaction_type.created_at'));
        $this->assertEquals($transaction->transactionType->updated_at->timestamp, $response->json('data.singleTransaction.transaction_type.updated_at'));
    }
}
