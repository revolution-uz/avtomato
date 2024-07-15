<?php

namespace App\GraphQL\Installment\Types;

use App\GraphQL\Scalars\BigInteger;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class TariffInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'TariffInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => [
                    'required',
                    'string',
                    'max:40',
                    'min:3',
                ],
            ],
            'is_annual' => [
                'name' => 'is_annual',
                'type' => Type::nonNull(Type::boolean()),
                'rules' => [
                    'required',
                    'boolean',
                ],
            ],
            'percent' => [
                'name' => 'percent',
                'type' => Type::nonNull(Type::float()),
                'rules' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:100',
                ],
            ],
            'min_period' => [
                'name' => 'min_period',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    'min:0',
                    'max:24',
                ],
            ],
            'max_period' => [
                'name' => 'max_period',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                    'min:0',
                    'max:24',
                ],
            ],
            'min_amount' => [
                'name' => 'min_amount',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => [
                    'required',
                    'int',
                    'min:0',
                    'max:1000000000',
                ],
            ],
            'max_amount' => [
                'name' => 'max_amount',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => [
                    'required',
                    'int',
                    'min:0',
                    'max:1000000000',
                ],
            ],
            'min_initial_fee' => [
                'name' => 'min_initial_fee',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => [
                    'required',
                    'int',
                    'min:0',
                    'max:1000000000',
                ],
            ],
            'max_initial_fee' => [
                'name' => 'max_initial_fee',
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'rules' => [
                    'required',
                    'int',
                    'min:0',
                    'max:1000000000',
                ],
            ],
        ];
    }
}
