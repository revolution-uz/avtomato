<?php

namespace App\GraphQL\LoyaltyProgram\Client\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\LoyaltyProgram\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'UserGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->id,
            ],
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (User $root) => $root->phone,
            ],
            'first_name' => [
                'type' => Type::string(),
                'resolve' => fn (User $root) => $root->first_name,
            ],
            'last_name' => [
                'type' => Type::string(),
                'resolve' => fn (User $root) => $root->last_name,
            ],
            'avatar' => [
                'type' => Type::string(),
                'resolve' => fn (User $root) => $root->avatar,
            ],
            'birth_date' => [
                'type' => Type::int(),
                'resolve' => fn (User $root) => $root->birth_date?->timestamp,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (User $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
