<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AttachGuarantorGraphQLMutationTest extends GraphQLTestCase
{
    private const string GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private Installment $_installment;
    private Customer $_customer;

    protected function setUp(): void
    {
        parent::setUp();
        Installment::query()->forceDelete();
        Customer::query()->forceDelete();
        $branch = Branch::query()->firstOrFail();

        $this->_installment = Installment::factory()->create(['branch_id' => $branch->id]);
        $this->_customer = Customer::factory()->create();
    }

    #[Test]
    public function successAttachGuarantor(): void
    {
        $response = $this->sendGraphQLRequest(
            $this->_installment->id,
            $this->_customer->id
        );
        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertSuccessResponse($response);

        // Reattachment of extinguished guarantor
        InstallmentGuarantor::query()
            ->where('installment_id', $this->_installment->id)
            ->where('customer_id', $this->_customer->id)
            ->delete();
        $response = $this->sendGraphQLRequest(
            $this->_installment->id,
            $this->_customer->id
        );
        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorDuplicateAttachGuarantor(): void
    {
        $this->successAttachGuarantor();
        $response = $this->sendGraphQLRequest(
            $this->_installment->id,
            $this->_customer->id
        );

        $response->assertJsonIsObject();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.unique', ['attribute' => 'customer id']),
            $response->json('errors.0.extensions.validation')['customer_id'][0]
        );
    }

    #[Test]
    public function errorValidation(): void
    {
        $response = $this->sendGraphQLRequest(9999, 6556);
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

        // Client cannot be attached as a guarantor.
        $response = $this->sendGraphQLRequest($this->_installment->id, $this->_installment->customer_id);
        $this->assertEquals(__('Client cannot be attached as a guarantor.'), $response->json('errors.0.extensions.validation')['customer_id'][0]);
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_ATTACH_GUARANTOR)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id, 'status' => InstallmentStatusEnum::ACTIVE]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery($installment->id, $this->_customer->id));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route(self::GRAPHQL_ROUTE), $this->buildGraphQLQuery($installment->id, $this->_customer->id));
        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorAttachGuarantorWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this->sendGraphQLRequest($this->_installment->id, $this->_customer->id);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function sendGraphQLRequest(int $installment_id, int $customer_id): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment_id, $customer_id)
            );
    }

    private function buildGraphQLQuery(int $installment_id, int $customer_id): array
    {
        return [
            'query' => 'mutation AttachGuarantor($installment_id: Int!, $customer_id: Int!) {
                attachGuarantor(installment_id: $installment_id, customer_id: $customer_id){
                    id
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
                        initial_fee
                        description
                        sms_notify
                        is_blacklist
                        period
                        canceled_at
                        cancellation_details
                        starts_in
                        next_pay_date
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
                        gender
                    }
                    any_card_charge
                    created_at
                    updated_at
                    deleted_at
                }
            }',
            'variables' => [
                'installment_id' => $installment_id,
                'customer_id' => $customer_id,
            ],
        ];
    }

    private function assertSuccessResponse($response): void
    {
        $response->assertJsonStructure([
            'data' => [
                'attachGuarantor' => [
                    'id',
                    'installment_id',
                    'installment' => [
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
                        'initial_fee',
                        'description',
                        'sms_notify',
                        'is_blacklist',
                        'period',
                        'canceled_at',
                        'cancellation_details',
                        'starts_in',
                        'next_pay_date',
                    ],
                    'customer_id',
                    'customer' => [
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
                        'gender',
                    ],
                    'any_card_charge',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);
    }
}
