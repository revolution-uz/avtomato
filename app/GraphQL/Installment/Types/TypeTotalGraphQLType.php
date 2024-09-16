<?php

namespace App\GraphQL\Installment\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TypeTotalGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'TypeTotalGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (array $root) {
                    return $root['type'];
                },
            ],
            'total_amount' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => function (array $root) {
                    return number_format($root['total_amount'], 2, '.', '.');
                },
            ],
        ];
    }
}
