<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Queries;

use App\Enums\Company\LoyaltyProgram\LoyaltyProgramStatisticsChartEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\LoyaltyProgramStatisticsGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\LoyaltyProgram\LoyaltyProgramStatisticsService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class LoyaltyProgramStatisticsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'loyaltyProgramStatistics',
    ];

    public function type(): Type
    {
        return GraphQL::type(LoyaltyProgramStatisticsGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'chart_type' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', Rule::enum(LoyaltyProgramStatisticsChartEnum::class)],
            ],
            'from' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'required_with:to', 'date_format:Y-m-d'],
            ],
            'to' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'required_with:from', 'date_format:Y-m-d', 'after_or_equal:from'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_STATISTICS);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return new LoyaltyProgramStatisticsService($args['chart_type'], $args['from'] ?? null, $args['to'] ?? null);
    }
}
