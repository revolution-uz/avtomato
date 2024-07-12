<?php

namespace App\GraphQL\Common\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentMonthlyInfoGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentMonthlyInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Installment Monthly Info GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Status Name',
                'resolve' => function (array $root) {
                    return $root['name'];
                },
            ],
            'type' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Type of Diagram',
                'resolve' => function (array $root) {
                    return $root['type'];
                },
            ],
            'stack' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Stack',
                'resolve' => function (array $root) {
                    return $root['stack'];
                },
            ],
            'data' => [
                'type' => Type::nonNull(Type::listOf(Type::int())),
                'description' => 'Data by status',
                'resolve' => function (array $root) {
                    return $root['data'];
                },
            ],
        ];
    }
}
