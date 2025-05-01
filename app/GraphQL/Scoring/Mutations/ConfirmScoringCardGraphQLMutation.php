<?php

namespace App\GraphQL\Scoring\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Helpers\RbacHelper;
use App\Models\CentralScoringCard;
use App\Services\Scoring\CardScoringService;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class ConfirmScoringCardGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'confirmScoringCard',
    ];

    public function __construct(private readonly CardScoringService $cardScoringService) {}

    public function args(): array
    {
        return [
            'card_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'integer', Rule::exists(CentralScoringCard::class, 'id')],
            ],
            'otp' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'digits:6'],
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'integer', Rule::in(ScoringRequestTypeEnum::cardTypes())],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
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

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): true
    {
        $card = CentralScoringCard::query()->findOrFail($args['card_id']);

        $otp = $args['otp'];
        $type = ScoringRequestTypeEnum::from($args['type']);

        return $type === ScoringRequestTypeEnum::HUMO
            ? $this->cardScoringService->verifyHumo($card, $otp)
            : $this->cardScoringService->verifyUzcard($card, $otp);
    }
}
