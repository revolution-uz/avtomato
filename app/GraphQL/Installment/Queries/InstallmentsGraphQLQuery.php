<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class InstallmentsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'installments',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(InstallmentGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
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
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return
            Installment::query()
                ->with(['customer', 'branch', 'tariff', 'graphs', 'cards', 'guarantors', 'documents', 'comments', 'transactions'])
                ->when(isset($args['search']), fn (Builder $q) => $q
                    ->whereHas('customer', fn (Builder $q) => $q
                        ->where('first_name', 'like', "%{$args['search']}%")
                        ->orWhere('last_name', 'ilike', "%{$args['search']}%")
                        ->orWhere('middle_name', 'ilike', "%{$args['search']}%")
                    )
                    ->orWhereHas('branch', fn (Builder $q) => $q->where('name', 'ilike', "%{$args['search']}%"))
                    ->orWhereHas('tariff', fn (Builder $q) => $q->where('name', 'ilike', "%{$args['search']}%"))
                )
                ->when(isset($args['sort']), fn (Builder|Installment $q) => $q->orderBySortable($args['sort']))
                ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
