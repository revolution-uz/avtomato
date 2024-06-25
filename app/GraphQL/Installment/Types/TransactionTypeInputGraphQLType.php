<?php

namespace App\GraphQL\TransactionType\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class TransactionTypeInputGraphQLType extends InputType
{
    public const GRAPHQL_TYPE_NAME = 'TransactionTypeInputGraphQLType';

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
                ],
            ],
        ];
    }
}
