<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\Enums\Company\CardStatusEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\SubscriptionGraphQLType;
use App\Models\Company\LoyaltyProgram\Subscription;
use App\Models\Company\LoyaltyProgram\Tariff;
use App\Models\Company\LoyaltyProgram\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Carbon;
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
            'tariff_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    Rule::exists(Tariff::class, 'id')->where('is_active', true)->whereNull('deleted_at'),
                ],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        /** @var User $user */
        $user = auth('loyalty-program-client')->user();

        if (! $user->first_name || ! $user->last_name || ! $user->birth_date) {
            throw ValidationException::withMessages(['user' => 'Please complete your profile information first.']);
        }

        if ($user->cards()->where('status', CardStatusEnum::ACTIVE)->count() == 0) {
            throw ValidationException::withMessages(['cards' => 'You need to add at least one active card to create a subscription.']);
        }

        if ($user->subscriptions()->where('tariff_id', $args['tariff_id'])->exists()) {
            throw ValidationException::withMessages(['tariff_id' => 'A subscription with this tariff already exists.']);
        }

        $tariff = Tariff::query()->findOrFail($args['tariff_id']);

        $subscription = new Subscription([
            'user_id' => $user->id,
            'tariff_id' => $tariff->id,
            'remaining_limit_amount' => $tariff->limit_amount,
            'expired_at' => Carbon::now()->addMonths(
                $user->subscriptions()->count() == 0 || $tariff->is_free_for_previous_subscribers
                    ? $tariff->free_months ?: $tariff->subscription_months
                    : $tariff->subscription_months
            ),
        ]);

        $subscription->saveOrFail();

        return $subscription->refresh();
    }
}
