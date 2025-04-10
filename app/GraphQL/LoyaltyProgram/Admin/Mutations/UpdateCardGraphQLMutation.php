<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Mutations;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\CardGraphQLType;
use App\GraphQL\LoyaltyProgram\Admin\Types\CardInputGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\Card;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class UpdateCardGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateCard',
    ];

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'int', 'exists:loyalty_program_cards,id,deleted_at,NULL'],
            ],
            'card' => [
                'type' => Type::nonNull(GraphQL::type(CardInputGraphQLType::GRAPHQL_TYPE_NAME)),
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(CardGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_CARD_EDIT);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $model = Card::findOrFail($args['id']);
        $model->fill($args['card']);
        $model->saveOrFail();

        return $model;
    }
}
