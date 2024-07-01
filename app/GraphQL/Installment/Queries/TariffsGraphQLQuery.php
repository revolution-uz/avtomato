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
                'name' => 'sort',
                'type' => Type::string(),
            ],
            'search' => [
                'name' => 'search',
                'type' => Type::string(),
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
            ],
            'per_page' => [
                'name' => 'per_page',
                'type' => Type::int(),
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
            ->when($args['search'], function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('name', 'ILIKE', $search);
                });
            })
            ->when(isset($args['sort']), fn (Builder|Tariff $q) => $q->orderBySortable($args['sort']))

            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }

    protected function rules(array $args = []): array
    {
        return [
            'sort' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|int|min:1',
            'per_page' => 'nullable|int|min:5|max:100',
        ];
    }
}
