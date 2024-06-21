<?php

namespace App\GraphQL\ActivityLog;

use App\GraphQL\ActivityLog\Queries\OwnLogsGraphQLQuery;
use App\GraphQL\ActivityLog\Queries\SingleLogGraphQLQuery;
use App\GraphQL\ActivityLog\Types\ActivityLogGraphQLType;
use App\GraphQL\User\Queries\ActivityLogsGraphQLQuery;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class ActivityLogQLSchema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            'query' => [
                ActivityLogsGraphQLQuery::class,
                OwnLogsGraphQLQuery::class,
                SingleLogGraphQLQuery::class
            ],
            'mutation' => [
            ],
            'types' => [
                ActivityLogGraphQLType::class,
            ],
            'middleware' => ['auth:sanctum'],
        ];
    }
}
