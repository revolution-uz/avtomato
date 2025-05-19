<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class LoyaltyProgramStatisticsCardGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'LoyaltyProgramStatisticsCardGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'ranged_count' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['ranged_count'] ?? null,
            ],
            'count' => [
                'type' => Type::int(),
                'resolve' => fn (array $root) => $root['count'] ?? null,
            ],
        ];
    }
}
