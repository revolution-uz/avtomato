<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Customer\Types\CustomerGraphQLType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentCustomerGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentCustomerGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (array $root) => $root['id'],
            ],
            'full_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['full_name'],
            ],
            'type' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (array $root) => $root['type'],
            ],
            'customer' => [
                'type' => Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['customer'],
            ],
        ];
    }
}
