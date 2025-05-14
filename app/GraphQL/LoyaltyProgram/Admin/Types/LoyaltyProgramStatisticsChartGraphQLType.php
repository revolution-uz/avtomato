<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class LoyaltyProgramStatisticsChartGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'LoyaltyProgramStatisticsChartGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (array $root) => $root['count'],
            ],
            'date' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['date'],
            ],
        ];
    }
}
