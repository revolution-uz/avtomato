<?php

namespace App\GraphQL\Scoring\Mutations;

use App\Clients\AsaxiyClient;
use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoCreateRequestDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\Debt\DebtCreateInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Scoring\MibService;
use App\Services\Scoring\TaxService;
use App\Services\User\BalanceService;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateDebtScoringGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createDebtScoring',
    ];

    public function args(): array
    {
        return [
            'data' => [
                'name' => 'data',
                'type' => Type::nonNull(GraphQL::type(DebtCreateInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'integer',
                    Rule::in([
                        ScoringRequestTypeEnum::PLUM_TAX_INFO->value,
                        ScoringRequestTypeEnum::PLUM_MIB_INFO->value,
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
            ScoringRequestTypeEnum::PLUM_MIB_INFO => PermissionEnum::SCORING_MIB_INFO_CREATE,
            ScoringRequestTypeEnum::PLUM_TAX_INFO => PermissionEnum::SCORING_TAX_INFO_CREATE,
            default => throw new Exception('Unexpected match value'),
        };

        return RbacHelper::checkPermission($permission) && (new BalanceService)->isBalanceEnoughForFeature($permission->getMyFeature());
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $type = ScoringRequestTypeEnum::from($args['type']);

        return $type === ScoringRequestTypeEnum::PLUM_MIB_INFO
            ? (new MibService(new AsaxiyClient, new BalanceService))->scoringMib(MibInfoCreateRequestDTO::from($args))->id
            : (new TaxService(new AsaxiyClient, new BalanceService))->scoringTax(TaxInfoCreateRequestDTO::from($args))->id;
    }

    protected function rules(array $args = []): array
    {
        if ($args['type'] === ScoringRequestTypeEnum::PLUM_TAX_INFO) {
            return parent::rules($args) + [
                'data.phone' => 'required',
            ];
        }

        return parent::rules($args);
    }
}
