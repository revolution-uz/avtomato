<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentComment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteInstallmentCommentGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        InstallmentComment::query()->forceDelete();
        $branch = Branch::query()->firstOrFail();
        $installment = Installment::factory()->create(['branch_id' => $branch->id]);
        InstallmentComment::factory()->count(5)->create(['installment_id' => $installment->id]);
    }

    #[Test]
    public function successDeleteInstallmentComment()
    {
        $idsInput = InstallmentComment::query()->inRandomOrder()->limit(3)->pluck('id')->toArray();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['ids' => $idsInput])
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
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_DELETE_COMMENT)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id]);
        $installmentComments = InstallmentComment::factory()->count(5)->create(['installment_id' => $installment->id]);
        $idsInput = $installmentComments->take(3)->pluck('id')->toArray();

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['ids' => $idsInput]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.1']), $response->json('errors.0.extensions.validation')['ids.1'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.2']), $response->json('errors.0.extensions.validation')['ids.2'][0]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['ids' => $idsInput]));
        $this->assertSuccessResponse($response, $idsInput);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation DeleteInstallmentComment($ids: [Int]!) {
                deleteInstallmentComment(ids: $ids)
            }
        ';

        return $query;
    }

    private function assertSuccessResponse(TestResponse $response, $idsInput): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'deleteInstallmentComment',
            ],
        ]);
        $this->assertTrue($response->json('data.deleteInstallmentComment'));
        $this->assertTrue(InstallmentComment::query()->whereIn('id', $idsInput)->doesntExist());
    }
}
