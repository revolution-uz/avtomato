<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\GraphQL\LoyaltyProgram\Client\Types\ProfileGraphQLType;
use App\Models\Company\LoyaltyProgram\User;
use App\Rules\PhoneRule;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class AttachShopmateGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'attachShopmate',
    ];

    public function args(): array
    {
        return [
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'digits:12',
                    new PhoneRule,
                    Rule::exists(User::class, 'phone')
                        ->whereNot('id', auth('loyalty-program-client')->id())
                        ->whereNull('deleted_at'),
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
        $user = auth('loyalty-program-client')->user();

        $user->shopmate()->associate(
            User::query()->where('phone', $args['phone'])->firstOrFail()
        );

        return $user;
    }
}
