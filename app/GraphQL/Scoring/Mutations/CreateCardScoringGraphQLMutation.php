<?php

namespace App\GraphQL\Scoring\Mutations;

use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\GraphQL\Scoring\Types\Card\CardCreateInputGraphQLType;
use App\GraphQL\Scoring\Types\Card\CreateCardScoringGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Customer;
use App\Services\Scoring\CardScoringService;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
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
            'customer_id' => [
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'int',
                    Rule::exists(Customer::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'data' => [
                'type' => Type::nonNull(GraphQL::type(CardCreateInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'integer', Rule::in(ScoringRequestTypeEnum::cardTypes())],
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
        $phone = $args['data']['phone'] ?? null;
        $customer_id = $args['customer_id'] ?? null;

        return $this->cardScoringService->scoringCard($dto, $type, $phone, $customer_id);
    }
}
