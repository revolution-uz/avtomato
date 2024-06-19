<?php

namespace App\GraphQL;

use App\GraphQL\User\Mutations\ChangePasswordGraphQLMutation;
use App\GraphQL\User\Mutations\LogoutGraphQLMutation;
use App\GraphQL\User\Queries\ActivityLogsGraphQLQuery;
use App\GraphQL\User\Queries\CurrentProfileDevicesGraphQLQuery;
use App\GraphQL\User\Queries\CurrentProfileGraphQLQuery;
use App\GraphQL\User\Queries\CurrentProfileActivityLogsGraphQLQuery;
use App\GraphQL\User\Queries\SingleActivityLogGraphQLQuery;
use App\GraphQL\User\Types\AdminGraphQLType;
use App\GraphQL\User\Types\AttachmentGraphQLType;
use App\GraphQL\User\Types\DeviceGraphQLType;
use App\GraphQL\User\Types\ActivityLogGraphQLType;
use App\GraphQL\User\Types\PermissionGraphQLType;
use App\GraphQL\User\Types\RoleGraphQLType;
use App\GraphQL\User\Types\TemplateThemeGraphQLType;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class GraphQLSchema implements ConfigConvertible
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
            ],
            'mutation' => [
                LogoutGraphQLMutation::class,
                ChangePasswordGraphQLMutation::class,
            ],
            'types' => [
                AdminGraphQLType::class,
                AttachmentGraphQLType::class,
                PermissionGraphQLType::class,
                RoleGraphQLType::class,
                TemplateThemeGraphQLType::class,
                DeviceGraphQLType::class,
                ActivityLogGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
