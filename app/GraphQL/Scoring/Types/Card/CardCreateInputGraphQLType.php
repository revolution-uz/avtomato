<?php

namespace App\GraphQL\Scoring\Types\Card;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class CardCreateInputGraphQLType extends InputType
{
    public const GRAPHQL_TYPE_NAME = 'CardCreateInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'number' => [
                'name' => 'number',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|integer|digits:16',
            ],
            'expiry' => [
                'name' => 'expiry',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|int|digits:4',
            ],
        ];
    }
}
