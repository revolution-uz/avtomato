<?php

namespace App\GraphQL\Management\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\DeviceGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\PersonalAccessToken;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CurrentProfileDevicesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'currentProfileDevices',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(DeviceGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'sort' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            ],
            'search' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:1',
            ],
            'per_page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:5|max:100',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PROFILE_DEVICES);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return auth()->user()->tokens()
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('name', 'ILIKE', $search)
                        ->orWhere('device_ip', 'ILIKE', $search)
                        ->orWhere('device_type', 'ILIKE', $search)
                        ->orWhere('device_os', 'ILIKE', $search);
                });
            })
            ->when(
                isset($args['sort']),
                fn (Builder|PersonalAccessToken $q) => $q->orderBySortable($args['sort']),
                fn (Builder|PersonalAccessToken $q) => $q->orderByDesc('id')
            )
            ->paginate(
                perPage: $args['per_page'] ?? 10,
                page: $args['page'] ?? 1
            );
    }
}
