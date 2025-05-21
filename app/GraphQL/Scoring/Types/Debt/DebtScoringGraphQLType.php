<?php

namespace App\GraphQL\Scoring\Types\Debt;

use App\GraphQL\Scoring\Types\BaseScoringRequestGraphQLType;
use App\Models\Company\ScoringRequest;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DebtScoringGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'DebtScoringGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return BaseScoringRequestGraphQLType::fields() + [
            'response' => [
                'type' => GraphQL::type(DebtScoringResponseGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ScoringRequest $root) => $root->response,
            ],
            'request' => [
                'type' => GraphQL::type(DebtScoringRequestGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ScoringRequest $root) => $root->request,
            ],
        ];
    }
}
