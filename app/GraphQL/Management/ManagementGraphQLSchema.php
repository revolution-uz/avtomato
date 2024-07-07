<?php

namespace App\GraphQL\Management;

use App\GraphQL\Customer\Types\AttachCardDataGraphQLType;
use App\GraphQL\Management\Mutations\AttachPermissionsToRoleGraphQLMutation;
use App\GraphQL\Management\Mutations\BalanceFillConfirmGraphQLMutation;
use App\GraphQL\Management\Mutations\BalanceFillGraphQLMutation;
use App\GraphQL\Management\Mutations\ChangeProfileAvatarGraphQLMutation;
use App\GraphQL\Management\Mutations\ChangeProfileGraphQLMutation;
use App\GraphQL\Management\Mutations\ChangeProfileLanguageGraphQLMutation;
use App\GraphQL\Management\Mutations\ChangeProfilePasswordGraphQLMutation;
use App\GraphQL\Management\Mutations\ChangeProfileThemeGraphQLMutation;
use App\GraphQL\Management\Mutations\CreateAdminGraphQLMutation;
use App\GraphQL\Management\Mutations\CreateBranchGraphQLMutation;
use App\GraphQL\Management\Mutations\CreateRoleGraphQLMutation;
use App\GraphQL\Management\Mutations\DeleteAdminGraphQLMutation;
use App\GraphQL\Management\Mutations\DeleteBranchGraphQLMutation;
use App\GraphQL\Management\Mutations\DeleteRoleGraphQLMutation;
use App\GraphQL\Management\Mutations\LogoutGraphQLMutation;
use App\GraphQL\Management\Mutations\RevokeAdminDeviceGraphQLMutation;
use App\GraphQL\Management\Mutations\RevokeDeviceGraphQLMutation;
use App\GraphQL\Management\Mutations\UpdateAdminGraphQLMutation;
use App\GraphQL\Management\Mutations\UpdateAdminPasswordGraphQLMutation;
use App\GraphQL\Management\Mutations\UpdateAdminRoleGraphQLMutation;
use App\GraphQL\Management\Mutations\UpdateBranchGraphQLMutation;
use App\GraphQL\Management\Mutations\UpdateRoleGraphQLMutation;
use App\GraphQL\Management\Queries\ActivityLogsGraphQLQuery;
use App\GraphQL\Management\Queries\AdminActivityLogsGraphQLQuery;
use App\GraphQL\Management\Queries\AdminDevicesGraphQLQuery;
use App\GraphQL\Management\Queries\AdminsGraphQLQuery;
use App\GraphQL\Management\Queries\BalanceChargesGraphQLQuery;
use App\GraphQL\Management\Queries\BalanceFundsGraphQLQuery;
use App\GraphQL\Management\Queries\BranchesGraphQLQuery;
use App\GraphQL\Management\Queries\CurrentProfileActivityLogsGraphQLQuery;
use App\GraphQL\Management\Queries\CurrentProfileDevicesGraphQLQuery;
use App\GraphQL\Management\Queries\CurrentProfileGraphQLQuery;
use App\GraphQL\Management\Queries\DashboardGraphQLQuery;
use App\GraphQL\Management\Queries\RolesGraphQLQuery;
use App\GraphQL\Management\Queries\SingleActivityLogGraphQLQuery;
use App\GraphQL\Management\Queries\SingleAdminGraphQLQuery;
use App\GraphQL\Management\Queries\SingleBranchGraphQLQuery;
use App\GraphQL\Management\Queries\SingleRoleGraphQLQuery;
use App\GraphQL\Management\Types\ActivityLogGraphQLType;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\GraphQL\Management\Types\AdminInputGraphQLType;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\GraphQL\Management\Types\BalanceChargeGraphQLType;
use App\GraphQL\Management\Types\BalanceFundGraphQLType;
use App\GraphQL\Management\Types\BranchGraphQLType;
use App\GraphQL\Management\Types\DashboardGraphQLType;
use App\GraphQL\Management\Types\DeviceGraphQLType;
use App\GraphQL\Management\Types\InstallmentAmountsGraphQLType;
use App\GraphQL\Management\Types\InstallmentMonthlyChangesGraphQLType;
use App\GraphQL\Management\Types\InstallmentMonthlyStatusesGraphQLType;
use App\GraphQL\Management\Types\InstallmentOverdueGraphQLType;
use App\GraphQL\Management\Types\PermissionBranchItemGraphQLType;
use App\GraphQL\Management\Types\PermissionEditableItemGraphQLType;
use App\GraphQL\Management\Types\PermissionGraphQLType;
use App\GraphQL\Management\Types\PermissionGroupGraphQLType;
use App\GraphQL\Management\Types\PermissionInputGraphQLType;
use App\GraphQL\Management\Types\RoleGraphQLType;
use App\GraphQL\Management\Types\TemplateThemeGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class ManagementGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'mutation' => [
                AttachPermissionsToRoleGraphQLMutation::class,
                BalanceFillGraphQLMutation::class,
                BalanceFillConfirmGraphQLMutation::class,
                ChangeProfileAvatarGraphQLMutation::class,
                ChangeProfileGraphQLMutation::class,
                ChangeProfileLanguageGraphQLMutation::class,
                ChangeProfilePasswordGraphQLMutation::class,
                ChangeProfileThemeGraphQLMutation::class,
                CreateAdminGraphQLMutation::class,
                CreateBranchGraphQLMutation::class,
                CreateRoleGraphQLMutation::class,
                DeleteAdminGraphQLMutation::class,
                DeleteBranchGraphQLMutation::class,
                DeleteRoleGraphQLMutation::class,
                LogoutGraphQLMutation::class,
                RevokeAdminDeviceGraphQLMutation::class,
                RevokeDeviceGraphQLMutation::class,
                UpdateAdminGraphQLMutation::class,
                UpdateAdminPasswordGraphQLMutation::class,
                UpdateAdminRoleGraphQLMutation::class,
                UpdateBranchGraphQLMutation::class,
                UpdateRoleGraphQLMutation::class,
            ],
            'query' => [
                AdminsGraphQLQuery::class,
                AdminActivityLogsGraphQLQuery::class,
                AdminDevicesGraphQLQuery::class,
                BalanceFundsGraphQLQuery::class,
                BalanceChargesGraphQLQuery::class,
                SingleAdminGraphQLQuery::class,
                CurrentProfileGraphQLQuery::class,
                CurrentProfileDevicesGraphQLQuery::class,
                CurrentProfileActivityLogsGraphQLQuery::class,
                SingleActivityLogGraphQLQuery::class,
                ActivityLogsGraphQLQuery::class,
                SingleRoleGraphQLQuery::class,
                RolesGraphQLQuery::class,
                BranchesGraphQLQuery::class,
                SingleBranchGraphQLQuery::class,
                DashboardGraphQLQuery::class,
            ],
            'types' => [
                AdminGraphQLType::class,
                AdminInputGraphQLType::class,
                AttachmentGraphQLType::class,
                AttachCardDataGraphQLType::class,
                BalanceFundGraphQLType::class,
                BalanceChargeGraphQLType::class,
                PermissionGraphQLType::class,
                RoleGraphQLType::class,
                TemplateThemeGraphQLType::class,
                DeviceGraphQLType::class,
                ActivityLogGraphQLType::class,
                PermissionInputGraphQLType::class,
                PermissionBranchItemGraphQLType::class,
                PermissionEditableItemGraphQLType::class,
                PermissionGroupGraphQLType::class,
                BranchGraphQLType::class,
                InstallmentMonthlyStatusesGraphQLType::class,
                InstallmentMonthlyChangesGraphQLType::class,
                DashboardGraphQLType::class,
                InstallmentAmountsGraphQLType::class,
                InstallmentOverdueGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
