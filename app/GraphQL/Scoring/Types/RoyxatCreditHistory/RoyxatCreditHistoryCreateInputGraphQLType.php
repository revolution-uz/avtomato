<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use App\GraphQL\GraphQLTypeHelper;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class RoyxatCreditHistoryCreateInputGraphQLType extends InputType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'RoyxatCreditHistoryCreateInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'passport_id' => [
                'name' => 'passport_id',
                'type' => Type::string(),
                'rules' => 'required_without:data.pinfl|string|regex:/^[A-Z]{2}\d{7}$/',
            ],
            'pinfl' => [
                'name' => 'pinfl',
                'type' => Type::string(),
                'rules' => 'required_without:data.passport_id|digits:14',
            ],
        ];
    }
}
