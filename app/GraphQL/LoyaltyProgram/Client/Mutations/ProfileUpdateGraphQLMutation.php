<?php

namespace App\GraphQL\LoyaltyProgram\Client\Mutations;

use App\GraphQL\LoyaltyProgram\Client\Types\ProfileGraphQLType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class ProfileUpdateGraphQLMutation extends Mutation
{
    protected $attributes = [
        'name' => 'profileUpdate',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(ProfileGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'first_name' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'last_name' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:255',
            ],
            'birth_date' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|date_format:Y-m-d',
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $user = auth('loyalty-program-client')->user();
        $user->update($args);

        return $user;
    }
}
