<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\GraphQL\LoyaltyProgram\Client\Types\ProfileGraphQLType;
use App\Models\Company\LoyaltyProgram\User;
use App\Rules\PhoneRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class EditRecipientGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'editRecipient',
    ];

    public function args(): array
    {
        return [
            'phone' => [
                'type' => Type::string(),
                'rules' => [
                    'nullable',
                    'digits:12',
                    new PhoneRule,
                    Rule::notIn([auth('loyalty-program-client')->user()->phone]),
                ],
            ],
        ];
    }

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProfileGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $distributor = auth('loyalty-program-client')->user();
        $phone = $args['phone'] ?? null;

        if (empty($phone)) {
            if ($distributor->recipient) {
                $distributor->recipient->update(['distributor_id' => null]);

                return $distributor->refresh();
            }

            throw ValidationException::withMessages(['phone' => __('api.no_recipient_attached')]);
        }

        if (
            $distributor->distributed_at && ($leftDays = (int) Carbon::now()->diffInDays($distributor->distributed_at->addMonth())) > 0
        ) {
            throw ValidationException::withMessages(['phone' => __('api.change_recipient_in_days', ['left_days' => $leftDays])]);
        }

        if ($recipient = User::query()->firstWhere('phone', $phone)) {
            $recipient->update(['distributor_id' => $distributor->id]);

        } else {
            User::query()->create(['phone' => $phone, 'distributor_id' => $distributor->id]);
        }

        $distributor->touch('distributed_at');

        return $distributor->refresh();
    }
}
