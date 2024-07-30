<?php

namespace App\GraphQL\Scoring\Mutations;

use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\Katm\KatmCreateInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Scoring\KatmService;
use App\Services\User\BalanceService;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateKatmScoringGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createKatmScoring',
    ];

    public function __construct(
        private readonly BalanceService $balanceService,
        private readonly KatmService $katmService,
    ) {}

    public function args(): array
    {
        return [
            'data' => [
                'name' => 'data',
                'type' => Type::nonNull(GraphQL::type(KatmCreateInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::in([
                        ScoringRequestTypeEnum::KATM_CREDIT_HISTORY->value,
                        ScoringRequestTypeEnum::KATM_INPS_HISTORY->value,
                        ScoringRequestTypeEnum::KATM_PENSION_INFO->value,
                    ]),
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::int());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $type = ScoringRequestTypeEnum::from($args['type']);
        $permission = match ($type) {
            ScoringRequestTypeEnum::KATM_CREDIT_HISTORY => PermissionEnum::SCORING_CREDIT_HISTORY_CREATE,
            ScoringRequestTypeEnum::KATM_INPS_HISTORY => PermissionEnum::SCORING_INPS_HISTORY_CREATE,
            ScoringRequestTypeEnum::KATM_PENSION_INFO => PermissionEnum::SCORING_PENSION_INFO_CREATE,
            default => throw new Exception('Unexpected match value'),
        };

        return RbacHelper::checkPermission($permission) && (tenant()->status == CompanyStatusEnum::ACTIVE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $dto = KatmCreateRequestDTO::from($args['data']);
        $type = ScoringRequestTypeEnum::from($args['type']);

        return $this->katmService->createRequest($dto, $type)->id;
    }
}
