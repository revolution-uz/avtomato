<?php

namespace App\GraphQL\Scoring\Types\Card;

use App\GraphQL\Scoring\Types\BaseScoringRequestGraphQLType;
use App\Models\Company\ScoringRequest;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CardScoringGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'CardScoringGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return BaseScoringRequestGraphQLType::fields() + [
            'response' => [
                'type' => GraphQL::type(CardScoringResponseGraphQLType::class),
                'resolve' => fn (ScoringRequest $root) => $root->response,
            ],
        ];
    }
}
