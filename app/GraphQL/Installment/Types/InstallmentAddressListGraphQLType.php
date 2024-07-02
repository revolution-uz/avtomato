<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Customer\Types\AddressGraphQLType;
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
            'customer' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentCustomerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['customer'],
            ],
        ];
    }
}
