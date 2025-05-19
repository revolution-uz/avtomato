<?php

namespace App\GraphQL\Scoring\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\Card\CardScoringGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\ScoringRequest;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleCardScoringRequestGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleCardScoringRequest',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CardScoringGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => Rule::exists('scoring_requests', 'id')
                    ->where('status', ScoringRequestStatusEnum::SUCCESS)
                    ->whereIn('type', [ScoringRequestTypeEnum::HUMO, ScoringRequestTypeEnum::UZCARD])
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return ScoringRequest::findOrFail($args['id'])->type === ScoringRequestTypeEnum::HUMO
            ? RbacHelper::checkPermission(PermissionEnum::SCORING_HUMO_VIEW)
            : RbacHelper::checkPermission(PermissionEnum::SCORING_UZCARD_VIEW);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return ScoringRequest::findOrFail($args['id']);
    }
}
