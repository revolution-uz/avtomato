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
                'name' => 'sort',
                'type' => Type::string(),
            ],
            'pinfl' => [
                'name' => 'pinfl',
                'type' => Type::string(),
            ],
            'passport_series' => [
                'name' => 'passport_series',
                'type' => Type::string(),
            ],
            'passport_number' => [
                'name' => 'passport_number',
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
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Customer::query()
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
            ->when($args['pinfl'], function (Builder $q) use ($args) {
                $search = '%' . $args['pinfl'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('pinfl', 'ILIKE', $search);
                });
            })
            ->when($args['passport_series'] && $args['passport_number'], function (Builder $q) use ($args) {
                $passport_series = '%' . $args['passport_series'] . '%';
                $passport_number = '%' . $args['passport_number'] . '%';
                $q->where(function (Builder $query) use ($passport_series, $passport_number) {
                    $query->where('passport_series', 'ILIKE', $passport_series)
                        ->where('passport_number', 'ILIKE', $passport_number);
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
            'pinfl' => 'nullable|string|size:14',
            'passport_series' => 'nullable|string|size:2',
            'passport_number' => 'nullable|int|size:7',
            'page' => 'nullable|int|min:1',
            'per_page' => 'nullable|int|min:5|max:100',
        ];
    }
}
