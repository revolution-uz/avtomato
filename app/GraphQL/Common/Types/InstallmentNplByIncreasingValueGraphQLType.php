<?php

namespace App\GraphQL\Common\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentNplByIncreasingValueGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentNplByIncreasingValueGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'value' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (array $root) => $root['value'],
            ],
            'value_of_last_week' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (array $root) => $root['value_of_last_week'],
            ],
            'value_increasing_percent' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (array $root) => $root['value_increasing_percent'],
            ],
        ];
    }
}
