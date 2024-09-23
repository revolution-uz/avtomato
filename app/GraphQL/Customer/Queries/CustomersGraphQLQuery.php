<?php

namespace App\GraphQL\Customer\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class CustomersGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'customers',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(CustomerGraphQLType::GRAPHQL_TYPE_NAME);
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
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Customer::query()->with('tags')
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('first_name', 'ILIKE', $search)
                        ->orWhere('last_name', 'ILIKE', $search)
                        ->orWhere('middle_name', 'ILIKE', $search)
                        ->orWhere('pinfl', 'ILIKE', $search)
                        ->orWhere('passport_series', 'ILIKE', $search)
                        ->orWhere('passport_number', 'ILIKE', $search)
                        ->orWhereHas('tags', function (Builder $q) use ($search) {
                            $q->where('name_uz', 'ILIKE', $search)
                                ->orWhere('name_oz', 'ILIKE', $search)
                                ->orWhere('name_ru', 'ILIKE', $search)
                                ->orWhere('name_en', 'ILIKE', $search);
                        });
                });
            })
            ->when(
                isset($args['sort']),
                fn (Builder|Customer $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Customer $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
