<?php

namespace App\GraphQL\Common\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Common\Types\DashboardGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Installment\InstallmentService;
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

    public function __construct(
        private readonly InstallmentService $installmentService
    ) {}

    public function type(): Type
    {
        return GraphQL::type(DashboardGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return auth()->check() && RbacHelper::checkPermission(PermissionEnum::DASHBOARD_INFO_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->installmentService->getDashboardInfo();
    }
}
