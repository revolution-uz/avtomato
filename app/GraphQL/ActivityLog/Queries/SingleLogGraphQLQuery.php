<?php

namespace App\GraphQL\ActivityLog\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\ActivityLog\Types\ActivityLogGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\ActivityLog;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleLogGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleLog',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ActivityLogGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ACTIVITY_LOG_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ActivityLog::query()->findOrFail($args['id']);
    }

    protected function rules(array $args = []): array
    {
        return [
            'id' => 'int|exists:activity_logs,id',
        ];
    }
}
