<?php

namespace App\GraphQL\Installment\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class TariffInputGraphQLType extends InputType
{
    public const GRAPHQL_TYPE_NAME = 'TariffInputGraphQLType';

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
                ],
            ],
            'min_period' => [
                'name' => 'min_period',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                ],
            ],
            'max_period' => [
                'name' => 'max_period',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                ],
            ],
            'min_amount' => [
                'name' => 'min_amount',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                ],
            ],
            'max_amount' => [
                'name' => 'max_amount',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                ],
            ],
            'min_initial_fee' => [
                'name' => 'min_initial_fee',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                ],
            ],
            'max_initial_fee' => [
                'name' => 'max_initial_fee',
                'type' => Type::nonNull(Type::int()),
                'rules' => [
                    'required',
                    'int',
                ],
            ],
        ];
    }
}
