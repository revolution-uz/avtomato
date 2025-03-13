<?php

namespace App\GraphQL\LoyaltyProgram\Client\Types;

use App\GraphQL\GraphQLTypeHelper;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class QrGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'QrGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn ($root) => $root['id'],
            ],
            'expires_in' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn ($root) => $root['expires_in']->timestamp,
            ],
        ];
    }
}
