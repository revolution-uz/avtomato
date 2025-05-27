<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Card;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DeleteCardGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteCard',
    ];

    public function args(): array
    {
        return [
            'ids' => [
                'type' => Type::nonNull(Type::listOf(Type::int())),
                'rules' => ['required', 'array', 'distinct', 'min:1'],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_CARD_DELETE);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): bool
    {
        Card::query()->whereIn('id', $args['ids'])->delete();

        return true;
    }

    protected function rules(array $args = []): array
    {
        return [
            'ids.*' => ['required', 'integer', 'exists:loyalty_program_cards,id,deleted_at,NULL'],
        ];
    }
}
