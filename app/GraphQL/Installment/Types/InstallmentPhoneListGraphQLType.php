<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Customer\Types\PhoneGraphQLType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentPhoneListGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentAddressListGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'phone' => [
                'type' => Type::nonNull(GraphQL::type(PhoneGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['phone'],
            ],
            'customer' => [
                'type' => Type::nonNull(GraphQL::type(InstallmentCustomerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['customer'],
            ],
        ];
    }
}
