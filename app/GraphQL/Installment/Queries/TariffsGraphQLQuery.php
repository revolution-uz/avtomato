<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\TariffGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Tariff;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class TariffsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'tariffs',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(TariffGraphQLType::GRAPHQL_TYPE_NAME);
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
        return RbacHelper::checkPermission(PermissionEnum::TARIFF_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Tariff::query()
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('name', 'ILIKE', $search);
                });
            })
            ->when(
                isset($args['sort']),
                fn (Builder|Tariff $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Tariff $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
