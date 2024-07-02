<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Customer\Types\CustomerGraphQLType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentAddressListGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentAddressListGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'address' => [
                'type' => Type::nonNull(GraphQL::type(AddressGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['address'],
            ],
            'customer_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (array $root) => $root['customer_id'],
            ],
            'is_guarantor' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (array $root) => $root['is_guarantor'],
            ],
            'customer' => [
                'type' => Type::nonNull(GraphQL::type(CustomerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['customer'],
            ],
        ];
    }
}
