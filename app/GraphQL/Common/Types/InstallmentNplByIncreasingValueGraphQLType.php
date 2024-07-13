<?php

namespace App\GraphQL\Common\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentNplByIncreasingValueGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentNplByIncreasingValueGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Installment NPL Percent GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'value' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'NPL',
                'resolve' => function (array $root) {
                    return $root['value'];
                },
            ],
            'value_of_last_week' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'NPL of Last Week',
                'resolve' => function (array $root) {
                    return $root['value_of_last_week'];
                },
            ],
            'value_increasing_percent' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'The percent of increasing of NPL',
                'resolve' => function (array $root) {
                    return $root['value_increasing_percent'];
                },
            ],
        ];
    }
}
