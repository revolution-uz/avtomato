<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\LoyaltyProgramStatisticsGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\LoyaltyProgram\LoyaltyProgramStatisticsService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
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
        $from = isset($args['from']) ? Carbon::createFromFormat('Y-m-d', $args['from']) : null;
        $to = isset($args['to']) ? Carbon::createFromFormat('Y-m-d', $args['to']) : null;

        if ($from && $to && $from->diffInDays($to) > 365) {
            throw ValidationException::withMessages(['to' => 'The range between from and to must not exceed 365 days.']);
        }

        return new LoyaltyProgramStatisticsService($from, $to);
    }
}
