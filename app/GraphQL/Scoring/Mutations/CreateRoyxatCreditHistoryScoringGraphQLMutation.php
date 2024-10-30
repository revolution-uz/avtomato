<?php

namespace App\GraphQL\Scoring\Mutations;

use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryInputDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Scoring\Types\RoyxatCreditHistory\RoyxatCreditHistoryCreateInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Scoring\RoyxatCreditHistoryService;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateRoyxatCreditHistoryScoringGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createRoyxatCreditHistoryScoring',
    ];

    public function __construct(
        private readonly RoyxatCreditHistoryService $royxatCreditHistoryService,
    ) {}

    public function args(): array
    {
        return [
            'data' => [
                'name' => 'data',
                'type' => Type::nonNull(GraphQL::type(RoyxatCreditHistoryCreateInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::int());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SCORING_ROYXAT_CREDIT_HISTORY_CREATE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->royxatCreditHistoryService->scoring(RoyxatCreditHistoryInputDTO::from($args['data']))->id;
    }

    protected function rules(array $args = []): array
    {
        return parent::rules($args);
    }
}
