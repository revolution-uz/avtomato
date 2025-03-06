<?php

namespace App\GraphQL\Common\Types;

use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ChartItemGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'ChartItemGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'label' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['label'],
            ],
            'value' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['value'],
            ],
        ];
    }
}
