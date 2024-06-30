<?php

namespace App\GraphQL\Management\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\ActivityLogGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\ActivityLog;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AdminActivityLogsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'adminActivityLogs',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(ActivityLogGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'admin_id' => [
                'name' => 'admin_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [Rule::exists('users', 'id')->whereNull('deleted_at')],
            ],
            'sort' => [
                'name' => 'sort',
                'type' => Type::string(),
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            ],
            'search' => [
                'name' => 'search',
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'rules' => 'nullable|int|min:1',
            ],
            'per_page' => [
                'name' => 'per_page',
                'type' => Type::int(),
                'rules' => 'nullable|int|min:5|max:100',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ACTIVITY_LOG_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ActivityLog::query()
            ->with('causer')
            ->when($args['search'], function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('description', 'ILIKE', $search);
                });
            })
            ->when(isset($args['sort']), fn (Builder|ActivityLog $q) => $q->orderBySortable($args['sort']))
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
