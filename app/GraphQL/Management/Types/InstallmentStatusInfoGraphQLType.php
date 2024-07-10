<?php

namespace App\GraphQL\Management\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentStatusInfoGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentStatusInfoGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Installment Status Info GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Status',
                'resolve' => function (array $root) {
                    return $root['status'];
                },
            ],
            'status_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Status Name',
                'resolve' => function (array $root) {
                    return $root['status_name'];
                },
            ],
            'count' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The number of installments',
                'resolve' => function (array $root) {
                    return $root['count'];
                },
            ],
        ];
    }
}
