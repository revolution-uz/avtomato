<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\Models\Company\LoyaltyProgram\Card;
use App\Models\Company\LoyaltyProgram\Tariff;
use App\Models\Company\LoyaltyProgram\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateSubscriptionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createSubscription',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(SubscriptionGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'card_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Card::class, 'id')->whereNull('deleted_at'),
                ],
            ],
            'tariff_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Tariff::class, 'id')->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var User $user */
        $user = auth('loyalty-program-client')->user();

        if (
            $user->subscriptions()->where('card_id', $args['card_id'])->where('tariff_id', $args['tariff_id'])->exists()
        ) {
            throw ValidationException::withMessages(['tariff_id' => 'A subscription with this card and tariff already exists.']);
        }

        return $user->subscriptions()->create([...$args]);
    }
}
