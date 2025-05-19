<?php

namespace App\GraphQL\Scoring\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\Debt\DebtScoringGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\ScoringRequest;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleDebtScoringRequestGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleDebtScoringRequest',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(DebtScoringGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => Rule::exists('scoring_requests', 'id')
                    ->where('status', ScoringRequestStatusEnum::SUCCESS)
                    ->whereIn('type', [ScoringRequestTypeEnum::PLUM_MIB_INFO, ScoringRequestTypeEnum::PLUM_TAX_INFO])
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return ScoringRequest::findOrFail($args['id'])->type === ScoringRequestTypeEnum::PLUM_MIB_INFO
            ? RbacHelper::checkPermission(PermissionEnum::SCORING_MIB_INFO_VIEW_ALL)
            : RbacHelper::checkPermission(PermissionEnum::SCORING_TAX_INFO_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ScoringRequest::findOrFail($args['id']);
    }
}
