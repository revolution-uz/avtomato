<?php

namespace App\GraphQL\Scoring\Types\RoyxatCreditHistory;

use App\GraphQL\Scoring\Types\BaseScoringRequestGraphQLType;
use App\Models\Company\ScoringRequest;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class RoyxatCreditHistoryGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'RoyxatCreditHistoryGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {

        return BaseScoringRequestGraphQLType::fields() + [
            'request' => [
                'type' => GraphQL::type(RoyxatCreditHistoryCreateRequestGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ScoringRequest $root) => $root->request,
            ],
            'response' => [
                'type' => Type::listOf(GraphQL::type(RoyxatCreditHistoryResponseGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ScoringRequest $root) => $root->response,
            ],
        ];
    }
}
