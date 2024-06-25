<?php

namespace App\GraphQL\Customer\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\CardGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Card;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CardsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'cards',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(CardGraphQLType::GRAPHQL_TYPE_NAME);
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
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::int(),
                'rules' => ['nullable', 'exists:customers,id'],
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
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARD_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Card::query()
            ->when($args['search'], function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('first_name', 'ILIKE', $search)
                        ->orWhere('last_name', 'ILIKE', $search)
                        ->orWhere('middle_name', 'ILIKE', $search)
                        ->orWhere('passport_series', 'ILIKE', $search)
                        ->orWhere('passport_number', 'ILIKE', $search);
                });
            })
            ->when($args['customer_id'], function (Builder $q) use ($args) {
                $q->where(function (Builder $query) use ($args) {
                    $query->where('customer_id', $args['customer_id']);
                });
            })
            // TODO sortable
//            ->when($request->isSortable(), function (Builder $q) use ($request) {
//                $q->orderBy($request->sortField(), $request->sortDirection());
//            })
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }

    protected function rules(array $args = []): array
    {
        return [
            'sort' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            'search' => 'nullable|string|max:255',
            'customer_id' => 'nullable|int',
            'page' => 'nullable|int|min:1',
            'per_page' => 'nullable|int|min:5|max:100',
        ];
    }
}
