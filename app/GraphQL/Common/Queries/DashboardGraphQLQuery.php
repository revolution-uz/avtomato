<?php

namespace App\GraphQL\Common\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Common\Types\DashboardGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Branch;
use App\Services\Installment\DashboardService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
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
            'from' => [
                'name' => 'from',
                'type' => Type::string(),
                'rules' => 'nullable|date_format:Y-m-d',
            ],
            'to' => [
                'name' => 'to',
                'type' => Type::string(),
                'rules' => 'nullable|date_format:Y-m-d|after_or_equal:from',
            ],
            'branch_id' => [
                'name' => 'branch_id',
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    Rule::exists(Branch::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return auth('company-admin')->check() && RbacHelper::checkPermission(PermissionEnum::DASHBOARD_INFO_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return new DashboardService($args['from'] ?? null, $args['to'] ?? null, $args['branch_id'] ?? null);
    }
}
