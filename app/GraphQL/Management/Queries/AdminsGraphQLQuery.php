<?php

namespace App\GraphQL\Management\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\FeatureEnum;
use App\GraphQL\Management\Types\AdminGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Feature;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AdminsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'admins',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(AdminGraphQLType::GRAPHQL_TYPE_NAME);
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
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return CompanyAdmin::query()
            ->when(! Feature::isFeatureActive(FeatureEnum::ICAN), fn (Builder $q) => $q
                ->whereNotIn('username', [CompanyAdmin::ICAN_INTEGRATOR]))
            ->when(isset($args['search']), fn (Builder $q) => $q
                ->where('username', 'ILIKE', "%{$args['search']}%")
                ->orWhere('first_name', 'ILIKE', "%{$args['search']}%")
                ->orWhere('last_name', 'ILIKE', "%{$args['search']}%"))
            ->when(
                isset($args['sort']),
                fn (Builder|CompanyAdmin $q) => $q->orderBySortable($args['sort']),
                fn (Builder|CompanyAdmin $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
