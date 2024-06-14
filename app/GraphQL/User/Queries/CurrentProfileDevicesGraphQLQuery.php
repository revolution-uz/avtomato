<?php

namespace App\GraphQL\User\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\User\Types\DeviceGraphQLType;
use App\Helpers\RbacHelper;
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
                'name' => 'sort',
                'type' => Type::string(),
            ],
            'search' => [
                'name' => 'search',
                'type' => Type::string(),
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
            ->when($args['search'] ?? null, function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('name', 'ILIKE', $search)
                        ->orWhere('device_ip', 'ILIKE', $search)
                        ->orWhere('device_type', 'ILIKE', $search)
                        ->orWhere('device_os', 'ILIKE', $search);
                });
            })
//            ->when($request->isSortable(), function (Builder $q) use ($request) {
//                $q->orderBy($request->sortField(), $request->sortDirection());
//            })
            ->paginate(
                perPage: $args['per_page'] ?? 10,
                page: $args['page'] ?? 1
            );
    }

    protected function rules(array $args = []): array
    {
        return [
            'sort' => 'string|regex:/^[-]?[a-z_]+$/',
            'search' => 'string|max:255',
        ];
    }
}
