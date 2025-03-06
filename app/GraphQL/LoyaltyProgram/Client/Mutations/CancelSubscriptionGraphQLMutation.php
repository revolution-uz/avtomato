<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\Models\Company\LoyaltyProgram\Subscription;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CancelSubscriptionGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'cancelSubscription',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(SubscriptionGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Subscription::class, 'id')
                        ->where('user_id', auth('loyalty-program-client')->user()->id),
                ],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $subscription = Subscription::query()->findOrFail($args['id']);

        if (! $subscription->is_active) {
            throw ValidationException::withMessages(['id' => 'The subscription is already inactive.']);
        }

        $subscription->is_active = false;
        $subscription->saveOrFail();

        return $subscription;
    }
}
