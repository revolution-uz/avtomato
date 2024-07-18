<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\InstallmentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
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
            'status' => [
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'int',
                    Rule::in(InstallmentStatusEnum::values()),
                ],
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
                ->with(['customer', 'branch', 'tariff', 'cards', 'guarantors', 'documents', 'comments', 'transactions'])
                ->when(isset($args['search']), fn (Builder $q) => $q
                    ->whereHas('customer', fn (Builder $q) => $q
                        ->where('first_name', 'like', "%{$args['search']}%")
                        ->orWhere('last_name', 'ilike', "%{$args['search']}%")
                        ->orWhere('middle_name', 'ilike', "%{$args['search']}%")
                    )
                    ->orWhereHas('branch', fn (Builder $q) => $q->where('name', 'ilike', "%{$args['search']}%"))
                    ->orWhereHas('tariff', fn (Builder $q) => $q->where('name', 'ilike', "%{$args['search']}%"))
                )
                ->when(isset($args['status']), fn (Builder $q) => $q->where('status', $args['status']))
                ->when(
                    isset($args['sort']),
                    fn (Builder|Installment $q) => $q->orderBySortable($args['sort']),
                    fn (Builder|Installment $q) => $q->orderByDesc('id')
                )
                ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
