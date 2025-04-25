<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\Models\Company\LoyaltyProgram\Card;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Mutation;

class DetachCardGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'detachCards',
    ];

    public function args(): array
    {
        return [
            'ids' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::int()))),
                'rules' => [
                    'required',
                    'array',
                    'distinct',
                    Rule::exists(Card::class, 'id')->where('user_id', auth('loyalty-program-client')->id())->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Card::query()->whereIn('id', $args['ids'])->delete();
    }
}
