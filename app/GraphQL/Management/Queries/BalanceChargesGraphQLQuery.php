<?php

namespace App\GraphQL\Management\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Management\Types\BalanceChargeGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\BalanceCharge;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class BalanceChargesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'balanceCharges',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(BalanceChargeGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'feature_id' => [
                'name' => 'feature_id',
                'type' => Type::int(),
                'rules' => [Rule::exists('features', 'id')],
            ],
            'sort' => [
                'name' => 'sort',
                'type' => Type::string(),
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            ],
            //            'search' => [
            //                'name' => 'search',
            //                'type' => Type::string(),
            //                'rules' => 'nullable|string|max:255',
            //            ],
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
        return RbacHelper::checkPermission(PermissionEnum::BALANCE_CHARGE_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return BalanceCharge::query()
//            ->when(isset($args['search']), function (Builder $q) use ($args) {
//                $search = '%' . $args['search'] . '%';
//                $q->where(function (Builder $query) use ($search) {
//                    $query->where('feature_id', $search);
//                });
//            })
            ->when(isset($args['feature_id']), fn (Builder $q) => $q->where('feature_id', $args['feature_id']))
            ->when(isset($args['sort']), fn (Builder|BalanceCharge $q) => $q->orderBySortable($args['sort']))
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
