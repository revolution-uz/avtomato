<?php

namespace App\GraphQL\Installment\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InstallmentContactListGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'InstallmentContactListGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'addresses' => [
                'type' => Type::listOf(GraphQL::type(InstallmentAddressListGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['addresses'],
            ],
            'phones' => [
                'type' => Type::listOf(GraphQL::type(InstallmentPhoneListGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (array $root) => $root['phones'],
            ],
        ];
    }
}
