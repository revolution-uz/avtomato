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

class DetachGuarantorGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Installment $_installment;

    protected function setUp(): void
    {
        parent::setUp();

        $branch = Branch::query()->firstOrFail();
        Installment::factory()->count(5)->create(['branch_id' => $branch->id]);
        foreach (Installment::all() as $installment) {
            $installment->guarantors()->attach(Customer::factory()->count(5)->create(), ['any_card_charge' => true]);
        }
    }

    #[Test]
    public function successDetachGuarantor()
    {
        $this->_installment = Installment::query()->inRandomOrder()->first();
        $idsInput = $this->_installment->guarantors()->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'ids' => $idsInput])
            );

        $this->assertSuccessResponse($response, $idsInput);
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
                    'ids' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$ids" of non-null type "[Int]!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'installment_id' => 999999999,
                    'ids' => [999999999],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
        $this->assertEquals(trans('api.not found'), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_DETACH_GUARANTOR)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $this->_installment = Installment::factory()->create(['branch_id' => $branches[1]->id, 'status' => InstallmentStatusEnum::ACTIVE]);
        $this->_installment->guarantors()->attach(Customer::factory()->count(5)->create(), ['any_card_charge' => true]);
        $idsInput = $this->_installment->guarantors()->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'ids' => $idsInput])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['installment_id' => $this->_installment->id, 'ids' => $idsInput])
            );
        $this->assertSuccessResponse($response, $idsInput);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DetachGuarantor($installment_id: Int!, $ids: [Int]!) {
                detachGuarantor(installment_id: $installment_id, ids: $ids)
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
                'detachGuarantor',
            ],
        ]);
        $this->assertTrue($response->json('data.detachGuarantor'));
        $this->assertTrue(
            InstallmentGuarantor::query()->where('installment_id', $this->_installment->id)
                ->whereIn('customer_id', $idsInput)->doesntExist()
        );
    }
}
