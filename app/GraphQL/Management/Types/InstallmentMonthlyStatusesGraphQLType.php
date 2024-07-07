<?php

namespace App\GraphQL\Management\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentMonthlyStatusesGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentMonthlyStatusesGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Installment Monthly Statuses GraphQL Type',
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
            'data' => [
                'type' => Type::nonNull(Type::listOf(Type::float())),
                'description' => 'Data by status',
                'resolve' => function (array $root) {
                    return $root['data'];
                },
            ],
        ];
    }
}
