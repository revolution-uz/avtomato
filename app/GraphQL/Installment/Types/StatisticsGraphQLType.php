<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class StatisticsGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'StatisticsGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'date' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['date']?->format('Y-m-d'),
            ],
            'debt' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['debt'],
            ],
            'paid' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['paid'],
            ],
        ];
    }
}
