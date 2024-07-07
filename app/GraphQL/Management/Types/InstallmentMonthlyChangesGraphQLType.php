<?php

namespace App\GraphQL\Management\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentMonthlyChangesGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentMonthlyChangesGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Installment Monthly Changes GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'date' => [
                'type' => Type::nonNull(Type::listOf(Type::string())),
                'description' => 'Date',
                'resolve' => function (array $root) {
                    return $root['date'];
                },
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Type of changes of installment',
                'resolve' => function (array $root) {
                    return $root['type'];
                },
            ],
            'data' => [
                'type' => Type::nonNull(Type::listOf(Type::float())),
                'description' => 'Data',
                'resolve' => function (array $root) {
                    return $root['data'];
                },
            ],
        ];
    }
}
