<?php

namespace App\GraphQL\Common\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
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
            'month_name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Name of Month',
                'resolve' => function (array $root) {
                    return $root['month_name'];
                },
            ],
            'timestamp' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Timestamp of Month',
                'resolve' => function (array $root) {
                    return $root['timestamp'];
                },
            ],
            'data' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type(InstallmentStatusInfoGraphQLType::GRAPHQL_TYPE_NAME))),
                'description' => 'Data by status',
                'resolve' => function (array $root) {
                    return $root['data'];
                },
            ],
        ];
    }
}
