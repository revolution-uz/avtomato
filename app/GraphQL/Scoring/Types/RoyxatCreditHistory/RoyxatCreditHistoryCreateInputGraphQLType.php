<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use App\GraphQL\Scalars\BigInteger;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;

class RoyxatCreditHistoryCreateInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'RoyxatCreditHistoryCreateInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'passport_id' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|max:9|min:9',
            ],
            'pinfl' => [
                'name' => 'pinfl',
                'type' => GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME),
                'rules' => 'nullable|integer|digits:14',
            ],
        ];
    }
}
