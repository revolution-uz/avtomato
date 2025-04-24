<?php

namespace App\GraphQL\Scoring\Mutations;

use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\Card\CardCreateInputGraphQLType;
use App\GraphQL\Scoring\Types\Card\CreateCardScoringGraphQLType;
use App\Helpers\RbacHelper;
use App\Services\Scoring\CardScoringService;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateCardScoringGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createCardScoring',
    ];

    public function __construct(private readonly CardScoringService $cardScoringService) {}

    public function args(): array
    {
        return [
            'data' => [
                'type' => Type::nonNull(GraphQL::type(CardCreateInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'integer', Rule::in(ScoringRequestTypeEnum::cardTypes())],
            ],
            'from' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'date_format:Y-m-d'],
            ],
            'to' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CreateCardScoringGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $type = ScoringRequestTypeEnum::from($args['type']);
        $permission = match ($type) {
            ScoringRequestTypeEnum::HUMO => PermissionEnum::SCORING_HUMO_CREATE,
            ScoringRequestTypeEnum::UZCARD => PermissionEnum::SCORING_UZCARD_CREATE,
            default => throw new Exception('Unexpected match value'),
        };

        return RbacHelper::checkPermission($permission);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): array
    {
        $type = ScoringRequestTypeEnum::from($args['type']);
        $dto = CardCreateRequestDTO::from($args['data']);
        $dateRanges = Arr::only($args, ['from', 'to']);
        $phone = $args['data']['phone'] ?? null;

        return $type === ScoringRequestTypeEnum::HUMO
            ? $this->cardScoringService->scoringHumo($dto, $dateRanges, $phone)
            : $this->cardScoringService->scoringUzcard($dto, $dateRanges);
    }
}
