<?php

namespace App\GraphQL\User;

use App\GraphQL\User\Mutations\ChangePasswordGraphQLMutation;
use App\GraphQL\User\Mutations\LogoutGraphQLMutation;
use App\GraphQL\User\Queries\CurrentProfileDevicesGraphQLQuery;
use App\GraphQL\User\Queries\CurrentProfileGraphQLQuery;
use App\GraphQL\User\Types\AdminGraphQLType;
use App\GraphQL\User\Types\AttachmentGraphQLType;
use App\GraphQL\User\Types\DeviceGraphQLType;
use App\GraphQL\User\Types\PermissionDetailsGraphQLType;
use App\GraphQL\User\Types\PermissionGraphQLType;
use App\GraphQL\User\Types\RoleGraphQLType;
use App\GraphQL\User\Types\TemplateThemeGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class UsersGraphQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                CurrentProfileGraphQLQuery::class,
                CurrentProfileDevicesGraphQLQuery::class,
            ],
            'mutation' => [
                LogoutGraphQLMutation::class,
                ChangePasswordGraphQLMutation::class,
            ],
            'types' => [
                AdminGraphQLType::class,
                AttachmentGraphQLType::class,
                PermissionDetailsGraphQLType::class,
                PermissionGraphQLType::class,
                RoleGraphQLType::class,
                TemplateThemeGraphQLType::class,
                DeviceGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
