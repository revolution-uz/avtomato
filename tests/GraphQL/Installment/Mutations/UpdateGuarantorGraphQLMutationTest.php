<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateGuarantorGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private InstallmentGuarantor $_installmentGuarantor;
    private array $_input;

    protected function setUp(): void
    {
        parent::setUp();

        $branch = Branch::query()->firstOrFail();
        Installment::factory()->count(5)->create(['branch_id' => $branch->id]);
        foreach (Installment::all() as $installment) {
            $installment->guarantors()->attach(Customer::factory()->count(5)->create(), ['any_card_charge' => true]);
        }

        $this->_installmentGuarantor = InstallmentGuarantor::query()->inRandomOrder()->first();
        $this->_input = [
            'installment_id' => $this->_installmentGuarantor->installment_id,
            'guarantor_id' => $this->_installmentGuarantor->customer_id,
            'any_card_charge' => false,
        ];
    }

    #[Test]
    public function successUpdateGuarantor()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessResponse($response);
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
                    'installment_id' => null,
                    'guarantor_id' => null,
                    'any_card_charge' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$guarantor_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$any_card_charge" of non-null type "Boolean!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'installment_id' => 999999999,
                    'guarantor_id' => 999999999,
                    'any_card_charge' => false,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'guarantor id']), $response->json('errors.0.extensions.validation.guarantor_id.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_UPDATE_GUARANTOR)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id, 'status' => InstallmentStatusEnum::ACTIVE]);
        $installment->guarantors()->attach(Customer::factory()->create(), ['any_card_charge' => true]);
        $this->_installmentGuarantor = InstallmentGuarantor::query()->where('installment_id', $installment->id)->first();
        $this->_input = array_merge($this->_input, [
            'installment_id' => $installment->id,
            'guarantor_id' => $installment->guarantors()->first()->id,
        ]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($this->_input));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($this->_input));
        $this->assertSuccessResponse($response);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateGuarantor(
                $installment_id: Int!
                $guarantor_id: Int!
                $any_card_charge: Boolean!
            ) {
                updateGuarantor(
                    installment_id: $installment_id
                    guarantor_id: $guarantor_id
                    any_card_charge: $any_card_charge
                ) {
                    id
                    installment_id
                    customer_id
                    any_card_charge
                    created_at
                    updated_at
                    deleted_at
                }
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
                'updateGuarantor' => [
                    'id',
                    'installment_id',
                    'customer_id',
                    'any_card_charge',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);

        $this->_installmentGuarantor->refresh();
        $this->assertEquals($this->_installmentGuarantor->any_card_charge, $this->_input['any_card_charge']);

        $this->assertEquals($this->_installmentGuarantor->id, $response->json('data.updateGuarantor.id'));
        $this->assertEquals($this->_installmentGuarantor->installment_id, $response->json('data.updateGuarantor.installment_id'));
        $this->assertEquals($this->_installmentGuarantor->customer_id, $response->json('data.updateGuarantor.customer_id'));
        $this->assertEquals($this->_installmentGuarantor->any_card_charge, $response->json('data.updateGuarantor.any_card_charge'));
        $this->assertEquals($this->_installmentGuarantor->created_at->timestamp, $response->json('data.updateGuarantor.created_at'));
        $this->assertEquals($this->_installmentGuarantor->updated_at->timestamp, $response->json('data.updateGuarantor.updated_at'));
        $this->assertNull($response->json('data.updateGuarantor.deleted_at'));
    }
}
