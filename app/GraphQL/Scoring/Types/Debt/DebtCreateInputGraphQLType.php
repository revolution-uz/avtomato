<?php

namespace App\GraphQL\Scoring\Types\Debt;

use App\GraphQL\Scalars\BigInteger;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
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
                'type' => GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME),
                'rules' => 'required|integer|digits:14',
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::string(),
                'rules' => 'nullable|int|digits:12',
            ],
        ];
    }
}
