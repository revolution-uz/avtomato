<?php

namespace Tests\GraphQL\Management\Queries;

use App\Models\Company\ActivityLog;
use App\Models\Company\Attachment;
use App\Models\Company\Branch;
use App\Models\Company\Rbac\Role;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleManagementGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private $_user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createAdminUser();
        Attachment::factory()->create([
            'attachmentable_id' => $this->_user->id,
            'attachmentable_type' => get_class($this->_user),
        ]);
    }

    #[Test]
    public function currentProfile(): void
    {
        $field = 'currentProfile';

        $structure = [
            'avatar' => [
                'created_at',
                'deleted_at',
                'id',
                'mime',
                'name',
                'original_name',
                'type',
                'updated_at',
                'url',
            ],
            'created_at',
            'deleted_at',
            'first_name',
            'id',
            'language',
            'last_name',
            'phone',
            'role' => [
                'created_at',
                'deleted_at',
                'id',
                'name',
                'permission_groups' => [
                    '*' => [
                        'id',
                        'name',
                        'permissions' => [
                            '*' => [
                                'branches' => [
                                    '*' => [
                                        'editable',
                                        'enabled',
                                        'id',
                                        'name',
                                    ],
                                ],
                                'id',
                                'name',
                                // 'slug',
                            ],
                        ],
                        // 'slug',
                    ],
                ],
                'permissions' => [
                    'activity_log_view',
                    'activity_log_view_all',
                    'activity_log_view_own',
                    'admin_change_avatar',
                    'admin_change_password',
                    'admin_change_role',
                    'admin_change_theme',
                    'admin_create',
                    'admin_delete',
                    'admin_devices',
                    'admin_edit',
                    'admin_revoke_device',
                    'admin_view',
                    'admin_view_all',
                    'balance_charge_confirm',
                    'balance_charge_create',
                    'balance_charge_delete',
                    'balance_charge_view',
                    'balance_charge_view_all',
                    'balance_fund_confirm',
                    'balance_fund_create',
                    'balance_fund_delete',
                    'balance_fund_view',
                    'balance_fund_view_all',
                    'branch_create',
                    'branch_delete',
                    'branch_edit',
                    'branch_view',
                    'branch_view_all',
                    'calculate_installment',
                    'calculator_export',
                    'customer_card_edit',
                    'customer_card_remove',
                    'customer_card_view',
                    'customer_create',
                    'customer_edit',
                    'customer_info_by_passport',
                    'customer_view',
                    'customer_view_all',
                    'customer_view_installments',
                    'installment_attach_card',
                    'installment_attach_guarantor',
                    'installment_cancel',
                    'installment_create',
                    'installment_create_comment',
                    'installment_create_transaction',
                    'installment_customer_list',
                    'installment_delete',
                    'installment_delete_comment',
                    'installment_delete_document',
                    'installment_detach_card',
                    'installment_detach_guarantor',
                    'installment_edit',
                    'installment_finish',
                    'installment_freeze',
                    'installment_start',
                    'installment_update_comment',
                    'installment_update_guarantor',
                    'installment_upload_document',
                    'installment_view',
                    'installment_view_all',
                    'profile_change_avatar',
                    'profile_change_language',
                    'profile_change_password',
                    'profile_change_theme',
                    'profile_devices',
                    'profile_edit',
                    'profile_revoke_device',
                    'role_create',
                    'role_delete',
                    'role_edit',
                    'role_permissions',
                    'role_view',
                    'role_view_all',
                    'scoring_credit_history_create',
                    'scoring_credit_history_delete',
                    'scoring_credit_history_view',
                    'scoring_credit_history_view_all',
                    'scoring_humo_create',
                    'scoring_humo_delete',
                    'scoring_humo_view',
                    'scoring_humo_view_all',
                    'scoring_inps_history_create',
                    'scoring_inps_history_delete',
                    'scoring_inps_history_view',
                    'scoring_inps_history_view_all',
                    'scoring_mib_info_create',
                    'scoring_mib_info_delete',
                    'scoring_mib_info_view',
                    'scoring_mib_info_view_all',
                    'scoring_pension_info_create',
                    'scoring_pension_info_delete',
                    'scoring_pension_info_view',
                    'scoring_pension_info_view_all',
                    'scoring_tax_info_create',
                    'scoring_tax_info_delete',
                    'scoring_tax_info_view',
                    'scoring_tax_info_view_all',
                    'scoring_uzcard_create',
                    'scoring_uzcard_delete',
                    'scoring_uzcard_view',
                    'scoring_uzcard_view_all',
                    'tariff_create',
                    'tariff_delete',
                    'tariff_edit',
                    'tariff_view',
                    'tariff_view_all',
                    'transaction_cancel',
                    'transaction_create',
                    'transaction_delete',
                    'transaction_edit',
                    'transaction_type_create',
                    'transaction_type_delete',
                    'transaction_type_edit',
                    'transaction_type_view',
                    'transaction_type_view_all',
                    'transaction_view',
                    'transaction_view_all',
                ],
                'updated_at',
            ],
            'role_id',
            'status',
            'template_theme' => [
                'layout_name',
            ],
            'updated_at',
            'username',
        ];

        $response = $this
            ->actingAs($this->_user)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure)
            );

        $this->assertResponse($response, $field, $structure);
    }

    #[Test]
    public function singleActivityLog(): void
    {
        $field = 'singleActivityLog';

        $structure = [
            'id',
            'admin_id',
            'created_at',
            'description',
            'admin' => [
                'id',
                'username',
                'first_name',
                'last_name',
                'phone',
                'status',
                'language',
                'role_id',
                'created_at',
                'updated_at',
                'deleted_at',
                'avatar' => [
                    'id',
                    'type',
                    'url',
                    'name',
                    'original_name',
                    'mime',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'template_theme' => [
                    'layout_name',
                ],
                'role' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'permission_groups' => [
                        '*' => [
                            'id',
                            // 'slug',
                            'name',
                            'permissions' => [
                                '*' => [
                                    'id',
                                    // 'slug',
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
                ],
            ],
        ];

        $activityLog = ActivityLog::factory()->create([
            'causer_id' => $this->_user->id,
            'causer_type' => get_class($this->_user),
        ]);

        $response = $this
            ->actingAs($this->_user)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure, ['id' => $activityLog->id])
            );

        $this->assertResponse($response, $field, $structure);
    }

    #[Test]
    public function singleAdmin(): void
    {
        $field = 'singleAdmin';

        $structure = [
            'id',
            'username',
            'first_name',
            'last_name',
            'phone',
            'status',
            'role_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'avatar' => [
                'id',
                'type',
                'url',
                'name',
                'original_name',
                'mime',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            'language',
            'template_theme' => [
                'layout_name',
            ],
            'role' => [
                'id',
                'name',
                'created_at',
                'updated_at',
                'deleted_at',
                'permission_groups' => [
                    '*' => [
                        'id',
                        // 'slug',
                        'name',
                        'permissions' => [
                            '*' => [
                                'id',
                                // 'slug',
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
            ],
        ];

        $response = $this
            ->actingAs($this->_user)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure, ['id' => $this->_user->id])
            );

        $this->assertResponse($response, $field, $structure);
    }

    #[Test]
    public function singleBranch(): void
    {
        $field = 'singleBranch';

        $structure = [
            'address',
            'created_at',
            'deleted_at',
            'id',
            'inn',
            'name',
            'updated_at',
        ];

        $branch = Branch::factory()->create();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure, ['id' => $branch->id])
            );

        $this->assertResponse($response, $field, $structure);
    }

    #[Test]
    public function singleRole(): void
    {
        $field = 'singleRole';

        $structure = [
            'id',
            'name',
            'created_at',
            'updated_at',
            'deleted_at',
            'permission_groups' => [
                '*' => [
                    'id',
                    // 'slug',
                    'name',
                    'permissions' => [
                        '*' => [
                            'id',
                            // 'slug',
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

        $role = Role::factory()->create();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure, ['id' => $role->id])
            );

        $this->assertResponse($response, $field, $structure);
    }

    private function buildGraphQLQuery(string $field, array $structure, array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $variableArgs = implode(', ', array_map(fn ($variableName) => "{$variableName}: \${$variableName}", array_keys($variables)));
        $variableTypes = implode(', ', array_map(fn ($variableName) => "\${$variableName}: " . $this->getGraphQLType($variables[$variableName]), array_keys($variables)));
        $dataStructure = $this->convertIntoQuery($structure);
        $query['query'] = '
            query ' . ($variableTypes ? "({$variableTypes})" : '') . ' {
                ' . $field . ($variableArgs ? "({$variableArgs})" : '') . '{' . $dataStructure . '}
            }
        ';

        return $query;
    }

    private function assertResponse(TestResponse $response, string $field, array $structure): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                $field => $structure,
            ],
        ]);
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
