<?php

namespace Management\Mutations;

use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateRoleGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

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
            ],
        ],
        'permissions' => [
            'profile_edit',
            'profile_change_password',
            'profile_change_language',
            'profile_change_avatar',
            'profile_devices',
            'profile_revoke_device',
            'balance_fund_view_all',
            'balance_fund_view',
            'balance_fund_delete',
            'balance_fund_create',
            'balance_fund_confirm',
            'balance_charge_view_all',
            'balance_charge_view',
            'balance_charge_delete',
            'balance_charge_create',
            'balance_charge_confirm',
            'admin_view_all',
            'admin_view',
            'admin_create',
            'admin_edit',
            'admin_delete',
            'admin_change_password',
            'admin_change_avatar',
            'admin_change_role',
            'admin_devices',
            'admin_revoke_device',
            'profile_change_theme',
            'admin_change_theme',
            'role_view_all',
            'role_view',
            'role_create',
            'role_edit',
            'role_delete',
            'role_permissions',
            'customer_info_by_passport',
            'customer_create',
            'customer_edit',
            'customer_view_all',
            'customer_view',
            'customer_view_installments',
            'customer_card_view',
            'customer_card_edit',
            'customer_card_remove',
            'calculate_installment',
            'calculator_export',
            'installment_view_all',
            'installment_view',
            'installment_start',
            'installment_cancel',
            'installment_freeze',
            'installment_finish',
            'installment_customer_list',
            'installment_create',
            'installment_edit',
            'installment_delete',
            'installment_attach_card',
            'installment_detach_card',
            'installment_attach_guarantor',
            'installment_update_guarantor',
            'installment_detach_guarantor',
            'installment_create_comment',
            'installment_create_transaction',
            'installment_update_comment',
            'installment_delete_comment',
            'installment_upload_document',
            'installment_delete_document',
            'dashboard_info_view',
            'tariff_view_all',
            'tariff_view',
            'tariff_edit',
            'tariff_delete',
            'tariff_create',
            'transaction_view_all',
            'transaction_view',
            'transaction_create',
            'transaction_edit',
            'transaction_delete',
            'transaction_cancel',
            'transaction_type_view_all',
            'transaction_type_view',
            'transaction_type_create',
            'transaction_type_edit',
            'transaction_type_delete',
            'branch_view_all',
            'branch_view',
            'branch_edit',
            'branch_delete',
            'branch_create',
            'scoring_credit_history_view_all',
            'scoring_credit_history_view',
            'scoring_credit_history_create',
            'scoring_credit_history_delete',
            'scoring_inps_history_view_all',
            'scoring_inps_history_view',
            'scoring_inps_history_create',
            'scoring_inps_history_delete',
            'scoring_pension_info_view_all',
            'scoring_pension_info_view',
            'scoring_pension_info_create',
            'scoring_pension_info_delete',
            'scoring_mib_info_view_all',
            'scoring_mib_info_view',
            'scoring_mib_info_create',
            'scoring_mib_info_delete',
            'scoring_tax_info_view_all',
            'scoring_tax_info_view',
            'scoring_tax_info_create',
            'scoring_tax_info_delete',
            'scoring_humo_view_all',
            'scoring_humo_view',
            'scoring_humo_create',
            'scoring_humo_delete',
            'scoring_uzcard_view_all',
            'scoring_uzcard_view',
            'scoring_uzcard_create',
            'scoring_uzcard_delete',
            'activity_log_view',
            'activity_log_view_all',
            'activity_log_view_own',
        ],
    ];

    #[Test]
    public function successCreateRole()
    {
        $roleInput = [
            'name' => Str::random(255),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($roleInput)
            );

        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'createRole' => $this->_structure,
            ],
        ]);

        $role = Role::query()->find($response->json('data.createRole.id'));
        $this->assertNotNull($role);

        $this->assertEquals($role->name, $roleInput['name']);

        $this->assertEquals($role->id, $response->json('data.createRole.id'));
        $this->assertEquals($role->name, $response->json('data.createRole.name'));
        $this->assertEquals($role->created_at->timestamp, $response->json('data.createRole.created_at'));
        $this->assertEquals($role->updated_at->timestamp, $response->json('data.createRole.updated_at'));
        $this->assertNull($response->json('data.createRole.deleted_at'));

        $this->assertNotNull($role);
        $this->assertEquals($role->id, $response->json('data.createRole.id'));
        $this->assertEquals($role->name, $response->json('data.createRole.name'));
        $this->assertEquals($role->created_at->timestamp, $response->json('data.createRole.created_at'));
        $this->assertEquals($role->updated_at?->timestamp, $response->json('data.createRole.updated_at'));
        $this->assertEquals($role->deleted_at?->timestamp, $response->json('data.createRole.deleted_at'));

        $permissionGroups = $role->mapAllPermissionByBranch();
        $this->assertEquals($permissionGroups[0]['id'], $response->json('data.createRole.permission_groups.0.id'));
        $this->assertEquals($permissionGroups[0]['slug']->value, $response->json('data.createRole.permission_groups.0.slug'));
        $this->assertEquals($permissionGroups[0]['name'], $response->json('data.createRole.permission_groups.0.name'));

        foreach ($role->mapRolePermissionByBranch() as $key => $value) {
            $this->assertEquals(json_encode($value), $response->json("data.createRole.permissions.{$key}"));
        }
    }

    #[Test]
    public function errorUnique()
    {
        $roleInput = [
            'name' => Role::query()->inRandomOrder()->first()->name,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($roleInput)
            );

        $response->assertOk();
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'name',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'Имя']), $response->json('errors.0.extensions.validation.name.0'));
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $roleInput = [
            'name' => null,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($roleInput)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name" of non-null type "String!" must not be null.'));

        $roleInput = [
            'name' => Str::random(256),
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($roleInput)
            );
        $response->assertOk();
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'name',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'Имя', 'max' => 255]), $response->json('errors.0.extensions.validation.name.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation CreateRole($name: String!) {
                createRole(name: $name) {' . $this->convertIntoQuery($this->_structure) . '}
            }
        ';

        return $query;
    }

    private function convertIntoQuery(array $structure): string
    {
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                if ($key == '*') {
                    $structure[$key] = $this->convertIntoQuery($value);
                } else {
                    $structure[$key] = "{$key} {{$this->convertIntoQuery($value)}}";
                }
            }
        }

        return implode(' ', $structure);
    }
}
