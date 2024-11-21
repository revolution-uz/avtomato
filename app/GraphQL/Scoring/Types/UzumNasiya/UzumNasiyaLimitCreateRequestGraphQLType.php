<?php

namespace App\GraphQL\Scoring\Types\UzumNasiya;

use App\DTOs\Scoring\UzumNasiya\UzumNasiyaLimitCreateRequestDTO;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UzumNasiyaLimitCreateRequestGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'UzumNasiyaLimitCreateRequestGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'phone' => [
                'name' => 'phone',
                'type' => Type::nonNull(Type::int()),
                'resolve' => function (UzumNasiyaLimitCreateRequestDTO $root) {
                    return $root->phone;
                },
            ],
        ];
    }
}
