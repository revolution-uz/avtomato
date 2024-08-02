<?php

namespace App\GraphQL\Scoring\Types;

use App\Models\Company\ScoringRequest;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ScoringRequestGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'ScoringRequestGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Scoring Request GraphQL Type',
    ];

    public function fields(): array
    {
        return BaseScoringRequestGraphQLType::fields() + [
            'type_name' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Type Name of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->type->name;
                },
            ],
        ];
    }
}
