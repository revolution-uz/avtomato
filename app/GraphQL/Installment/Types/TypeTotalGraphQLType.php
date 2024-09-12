<?php

namespace App\GraphQL\Installment\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TypeTotalGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'TypeTotalGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Total amount grouped by type',
    ];

    public function fields(): array
    {
        return [
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The type identifier',
            ],
            'total_amount' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'The total amount for the given type',
            ],
        ];
    }
}
