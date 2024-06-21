<?php

namespace App\GraphQL\Management;

use App\GraphQL\Management\Mutations\AttachPermissionsToRoleGraphQLMutation;
use App\GraphQL\Management\Mutations\ChangeLanguageGraphQLMutation;
use App\GraphQL\Management\Mutations\ChangePasswordGraphQLMutation;
use App\GraphQL\Management\Mutations\ChangeProfileGraphQLMutation;
use App\GraphQL\Management\Mutations\ChangeThemeGraphQLMutation;
use App\GraphQL\Management\Mutations\CreateRoleGraphQLMutation;
use App\GraphQL\Management\Mutations\DeleteRoleGraphQLMutation;
use App\GraphQL\Management\Mutations\LogoutGraphQLMutation;
use App\GraphQL\Management\Mutations\RevokeDeviceGraphQLMutation;
use App\GraphQL\Management\Mutations\UpdateRoleGraphQLMutation;
use App\GraphQL\Management\Mutations\UploadAvatarGraphQLMutation;
use App\GraphQL\Management\Queries\ActivityLogsGraphQLQuery;
use App\GraphQL\Management\Queries\CurrentProfileActivityLogsGraphQLQuery;
use App\GraphQL\Management\Queries\CurrentProfileDevicesGraphQLQuery;
use App\GraphQL\Management\Queries\CurrentProfileGraphQLQuery;
use App\GraphQL\Management\Queries\RolesGraphQLQuery;
use App\GraphQL\Management\Queries\SingleActivityLogGraphQLQuery;
use App\GraphQL\Management\Queries\SingleRoleGraphQLQuery;
use App\GraphQL\Management\Types\ActivityLogGraphQLType;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\GraphQL\Management\Types\AttachmentGraphQLType;
use App\GraphQL\Management\Types\DeviceGraphQLType;
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
            'query' => [
                CurrentProfileGraphQLQuery::class,
                CurrentProfileDevicesGraphQLQuery::class,
                CurrentProfileActivityLogsGraphQLQuery::class,
                SingleActivityLogGraphQLQuery::class,
                ActivityLogsGraphQLQuery::class,
                SingleRoleGraphQLQuery::class,
                RolesGraphQLQuery::class,
            ],
            'mutation' => [
                LogoutGraphQLMutation::class,
                ChangePasswordGraphQLMutation::class,
                ChangeThemeGraphQLMutation::class,
                ChangeLanguageGraphQLMutation::class,
                ChangeProfileGraphQLMutation::class,
                ChangeThemeGraphQLMutation::class,
                RevokeDeviceGraphQLMutation::class,
                UploadAvatarGraphQLMutation::class,
                CreateRoleGraphQLMutation::class,
                UpdateRoleGraphQLMutation::class,
                AttachPermissionsToRoleGraphQLMutation::class,
                DeleteRoleGraphQLMutation::class,
            ],
            'types' => [
                AdminGraphQLType::class,
                AttachmentGraphQLType::class,
                PermissionGraphQLType::class,
                RoleGraphQLType::class,
                TemplateThemeGraphQLType::class,
                DeviceGraphQLType::class,
                ActivityLogGraphQLType::class,
                PermissionInputGraphQLType::class,
                PermissionBranchItemGraphQLType::class,
                PermissionEditableItemGraphQLType::class,
                PermissionGroupGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
