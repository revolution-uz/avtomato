<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use GraphQL\Type\Definition\Type;
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
            'passport' => [
                'type' => Type::string(),
                'rules' => 'required_without_all:pinfl,tin|nullable|string|max:9|min:9',
            ],
            'pinfl' => [
                'type' => Type::string(),
                'rules' => 'required_without_all:passport,tin|nullable|integer|digits:14',
            ],
            'tin' => [
                'type' => Type::string(),
                'rules' => 'required_without_all:passport,pinfl|nullable|integer|digits:9',
            ],
        ];
    }
}
