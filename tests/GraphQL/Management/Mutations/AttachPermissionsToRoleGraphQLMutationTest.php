<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AttachPermissionsToRoleGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Role $_role;

    private array $_permissionInput;
    private Permission $_permission;

    private array $_structure = [
        'id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
        'permission_groups' => [
            '*' => [
                'id',
                'slug',
                'name',
                'permissions' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'branches' => [
                            '*' => [
                                'id',
                                'name',
                                'editable',
                                'enabled',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'permissions' => null,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_role = Role::factory()->create();
        $this->_permission = Permission::query()->inRandomOrder()->first();
        $branch = Branch::factory()->create();

        $this->_permissionInput = [
            'permission_id' => $this->_permission->id,
            'branch_id' => $branch->id,
            'enabled' => true,
        ];

        if (empty($this->_structure['permissions'])) {
            $this->_structure['permissions'] = array_map(fn ($case) => $case->value, PermissionEnum::cases());
        }
    }

    #[Test]
    public function successAttachPermissionsToRole()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($this->_role->id, $this->_permissionInput)
            );

        $response->assertOk();
        $this->assertSuccessAttachPermissionsToRole($response, $this->_role, $this->_permissionInput);
    }

    #[Test]
    public function errorRoleValidation()
    {
        $id = null;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(roleId: $id)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$role_id" of non-null type "Int!" must not be null.'));

        $id = Role::query()->orderByDesc('id')->first()->id;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($id + 1, $this->_permissionInput)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'validation'));
    }

    #[Test]
    public function errorPermissionValidation()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($this->_role->id)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$permissions" got invalid value [] at "permissions[0]"; Field "permission_id" of required type "Int!" was not provided.', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($this->_role->id, [
                    'permission_id' => null,
                    'branch_id' => null,
                    'enabled' => null,
                ])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$permissions" got invalid value null at "permissions[0].permission_id"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$permissions" got invalid value null at "permissions[0].enabled"; Expected non-nullable type "Boolean!" not to be null.'));
    }

    #[Test]
    public function errorAttachPermissionsToRoleWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($this->_role->id, $this->_permissionInput)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    #[Test]
    public function errorAttachPermissionsIsNotActiveFeature(): void
    {
        $this->_permission->feature->update(['is_active' => false]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($this->_role->id, $this->_permissionInput)
            );
        $errors = $response->json('errors');
        $this->assertEquals(__('api.feature_not_active'), $errors[0]['extensions']['validation']['permissions.0.permission_id'][0]);
    }

    private function buildGraphQLQuery(?int $roleId = null, array $permission = []): array
    {
        $query = [];
        $query['variables'] = ['role_id' => $roleId, 'permissions' => [$permission]];
        $query['query'] = '
            mutation AttachPermissionsToRole($role_id: Int! $permissions: [PermissionInputGraphQLType]!) {
                attachPermissionsToRole(role_id: $role_id, permissions: $permissions) {' . $this->convertIntoQuery($this->_structure) . '}
            }
        ';

        return $query;
    }

    private function assertSuccessAttachPermissionsToRole(TestResponse $response, ?Role $role, array $permission): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'attachPermissionsToRole' => $this->_structure,
            ],
        ]);

        $this->assertNotNull($role);
        $this->assertEquals($role->id, $response->json('data.attachPermissionsToRole.id'));
        $this->assertEquals(count($role->rolePermissions), 1);

        $this->assertEquals($permission['permission_id'], $role->rolePermissions[0]->permission_id);
        $this->assertEquals($permission['branch_id'], $role->rolePermissions[0]->branch_id);

        $this->assertEquals($role->id, $response->json('data.attachPermissionsToRole.id'));
        $this->assertEquals($role->name, $response->json('data.attachPermissionsToRole.name'));
        $this->assertEquals($role->created_at->timestamp, $response->json('data.attachPermissionsToRole.created_at'));
        $this->assertEquals($role->updated_at?->timestamp, $response->json('data.attachPermissionsToRole.updated_at'));
        $this->assertEquals($role->deleted_at?->timestamp, $response->json('data.attachPermissionsToRole.deleted_at'));

        $permissionGroups = $role->mapAllPermissionByBranch();
        $this->assertEquals($permissionGroups[0]['id'], $response->json('data.attachPermissionsToRole.permission_groups.0.id'));
        $this->assertEquals($permissionGroups[0]['slug']->value, $response->json('data.attachPermissionsToRole.permission_groups.0.slug'));
        $this->assertEquals($permissionGroups[0]['name'], $response->json('data.attachPermissionsToRole.permission_groups.0.name'));

        $permissions = $permissionGroups[0]['permissions'];
        $this->assertEquals($permissions[0]['id'], $response->json('data.attachPermissionsToRole.permission_groups.0.permissions.0.id'));
        $this->assertEquals($permissions[0]['slug']->value, $response->json('data.attachPermissionsToRole.permission_groups.0.permissions.0.slug'));
        $this->assertEquals($permissions[0]['name'], $response->json('data.attachPermissionsToRole.permission_groups.0.permissions.0.name'));

        $branches = $permissions[0]['branches'];

        $this->assertEquals($branches[0]['id'], $response->json('data.attachPermissionsToRole.permission_groups.0.permissions.0.branches.0.id'));
        $this->assertEquals($branches[0]['name'], $response->json('data.attachPermissionsToRole.permission_groups.0.permissions.0.branches.0.name'));
        $this->assertEquals($branches[0]['editable'], $response->json('data.attachPermissionsToRole.permission_groups.0.permissions.0.branches.0.editable'));
        $this->assertEquals($branches[0]['enabled'], $response->json('data.attachPermissionsToRole.permission_groups.0.permissions.0.branches.0.enabled'));

        foreach ($role->mapRolePermissionByBranch() as $key => $value) {
            $this->assertEquals(json_encode($value), $response->json("data.attachPermissionsToRole.permissions.{$key}"));
        }
    }
}
