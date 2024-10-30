<?php

namespace App\GraphQL\Scoring\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\ScoringRequest;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DeleteScoringRequestsGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteScoringRequests',
    ];

    public function args(): array
    {
        return [
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::int()))),
            ],
        ];
    }

    public function rules(array $args = []): array
    {
        return [
            'ids' => 'array',
            'ids.*' => 'integer|exists:scoring_requests,id',
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $authorized = true;
        /** @var ScoringRequest[] $scoringRequests */
        $scoringRequests = ScoringRequest::query()->whereIn('id', $args['ids'])->select('type')->get();
        foreach ($scoringRequests as $scoringRequest) {
            $authorized = $authorized && RbacHelper::checkPermission(
                match ($scoringRequest->type) {
                    ScoringRequestTypeEnum::KATM_CREDIT_HISTORY => PermissionEnum::SCORING_CREDIT_HISTORY_DELETE,
                    ScoringRequestTypeEnum::KATM_INPS_HISTORY => PermissionEnum::SCORING_INPS_HISTORY_DELETE,
                    ScoringRequestTypeEnum::KATM_PENSION_INFO => PermissionEnum::SCORING_PENSION_INFO_DELETE,
                    ScoringRequestTypeEnum::PLUM_MIB_INFO => PermissionEnum::SCORING_MIB_INFO_DELETE,
                    ScoringRequestTypeEnum::PLUM_TAX_INFO => PermissionEnum::SCORING_TAX_INFO_DELETE,
                    ScoringRequestTypeEnum::HUMO => PermissionEnum::SCORING_HUMO_DELETE,
                    ScoringRequestTypeEnum::UZCARD => PermissionEnum::SCORING_UZCARD_DELETE,
                    ScoringRequestTypeEnum::UZUM_NASIYA_LIMIT => PermissionEnum::SCORING_UZUM_NASIYA_LIMIT_DELETE,
                    ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY => PermissionEnum::SCORING_ROYXAT_CREDIT_DELETE,
                }
            );
        }

        return $authorized;
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return (bool) (ScoringRequest::query()->whereIn('id', $args['ids'])->delete());
    }
}
