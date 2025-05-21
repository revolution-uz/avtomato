<?php

namespace App\GraphQL\Common\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Common\Types\DashboardGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Installment\DashboardService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class DashboardGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'dashboard',
    ];

    public function type(): Type
    {
        return GraphQL::type(DashboardGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'transaction_by_type_start_date' => [
                'name' => 'transaction_by_type_start_date',
                'type' => Type::string(),
                'rules' => ['nullable', 'required_with:transaction_by_type_end_date', 'date_format:Y-m-d'],
            ],
            'transaction_by_type_end_date' => [
                'name' => 'transaction_by_type_end_date',
                'type' => Type::string(),
                'rules' => ['nullable', 'required_with:transaction_by_type_start_date', 'date_format:Y-m-d', 'after_or_equal:transaction_by_type_start_date'],
            ],
            'installment_by_age_start_date' => [
                'name' => 'installment_by_age_start_date',
                'type' => Type::string(),
                'rules' => ['nullable', 'required_with:installment_by_age_end_date', 'date_format:Y-m-d'],
            ],
            'installment_by_age_end_date' => [
                'name' => 'installment_by_age_end_date',
                'type' => Type::string(),
                'rules' => ['nullable', 'required_with:installment_by_age_start_date', 'date_format:Y-m-d', 'after_or_equal:installment_by_age_start_date'],
            ],
            'branch_id' => [
                'name' => 'branch_id',
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:branches,id,deleted_at,NULL'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return auth('company-admin')->check();
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (! RbacHelper::checkPermission(PermissionEnum::DASHBOARD_INFO_VIEW)) {
            return null;
        }

        return new DashboardService(
            $args['transaction_by_type_start_date'] ?? null,
            $args['transaction_by_type_end_date'] ?? null,
            $args['installment_by_age_start_date'] ?? null,
            $args['installment_by_age_end_date'] ?? null,
            $args['branch_id'] ?? null
        );
    }
}
