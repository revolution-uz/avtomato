<?php

namespace App\GraphQL\Scoring\Types\Debt;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class DebtCreateInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'DebtCreateInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'pinfl' => [
                'name' => 'pinfl',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|digits:14',
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::string(),
                'rules' => 'nullable|int|digits:12',
            ],
        ];
    }
}
