<?php

namespace App\GraphQL\Scoring\Mutations;

use App\Clients\AsaxiyClient;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\KatmCreateInputGraphQLType;
use App\GraphQL\Scoring\Types\PensionHistory\PensionInfoGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Scoring\KatmService;
use App\Services\User\BalanceService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreatePensionInfoScoringGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createPensionInfoScoring',
    ];

    public function args(): array
    {
        return [
            'data' => [
                'name' => 'data',
                'type' => Type::nonNull(GraphQL::type(KatmCreateInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(PensionInfoGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SCORING_PENSION_INFO_CREATE) && (new BalanceService)->isBalanceEnoughForFeature(PermissionEnum::SCORING_PENSION_INFO_CREATE->getMyFeature());
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $dto = KatmCreateRequestDTO::from($args['data']);

        return (new KatmService(new AsaxiyClient))->createRequest($dto, ScoringRequestTypeEnum::KATM_PENSION_INFO);
    }
}
