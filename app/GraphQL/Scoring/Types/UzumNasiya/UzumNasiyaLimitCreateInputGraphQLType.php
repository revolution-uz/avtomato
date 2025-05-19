<?php

namespace App\GraphQL\Scoring\Types\UzumNasiya;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class UzumNasiyaLimitCreateInputGraphQLType extends InputType
{
    public const string GRAPHQL_TYPE_NAME = 'UzumNasiyaLimitCreateInputGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(Type::string()),
                'rules' => 'required|string|max:12|min:12',
            ],
        ];
    }
}
