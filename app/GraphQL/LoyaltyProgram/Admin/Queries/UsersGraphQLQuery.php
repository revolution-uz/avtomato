<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Client\Types\UserGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\User;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class UsersGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'users',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(UserGraphQLType::GRAPHQL_TYPE_NAME);
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

    /**
     * @throws Error
     */
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_USERS_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return User::query()
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('phone', 'ILIKE', $search)
                        ->orWhere('first_name', 'ILIKE', $search)
                        ->orWhere('last_name', 'ILIKE', $search);
                });
            })
            ->when(
                isset($args['sort']),
                fn (Builder|User $q) => $q->orderBySortable($args['sort']),
                fn (Builder|User $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
