<?php

namespace App\GraphQL\Common\Types;

use App\GraphQL\Scalars\BigFloat;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentOverdueGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'InstallmentOverdueGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'overdue_days' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['overdue_days'],
            ],
            'amount' => [
                'type' => Type::nonNull(GraphQL::type(BigFloat::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['amount'],
            ],
            'count' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (array $root) => $root['count'],
            ],
        ];
    }
}
