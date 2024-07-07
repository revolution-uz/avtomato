<?php

namespace App\GraphQL\Management\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\DeviceGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\PersonalAccessToken;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AdminDevicesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'adminDevices',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(DeviceGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'admin_id' => [
                'name' => 'admin_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => [Rule::exists('admins', 'id')->whereNull('deleted_at')],
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
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_DEVICES);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return PersonalAccessToken::query()->where('tokenable_id', $args['admin_id'])->where('tokenable_type', CompanyAdmin::class)
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('name', 'ILIKE', $search)
                        ->orWhere('device_ip', 'ILIKE', $search)
                        ->orWhere('device_type', 'ILIKE', $search)
                        ->orWhere('device_os', 'ILIKE', $search);
                });
            })
            ->when(isset($args['sort']), fn (Builder|PersonalAccessToken $q) => $q->orderBySortable($args['sort']))
            ->paginate(
                perPage: $args['per_page'] ?? 10,
                page: $args['page'] ?? 1
            );
    }
}
