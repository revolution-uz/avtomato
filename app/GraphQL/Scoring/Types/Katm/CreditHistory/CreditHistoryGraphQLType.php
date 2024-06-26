<?php

namespace App\GraphQL\Scoring\Types\Katm\CreditHistory;

use App\GraphQL\Scoring\Types\BaseScoringRequestGraphQLType;
use App\GraphQL\Scoring\Types\Katm\KatmCreateRequestGraphQLType;
use App\Models\Company\ScoringRequest;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CreditHistoryGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'CreditHistoryGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return BaseScoringRequestGraphQLType::fields() + [
            'request' => [
                'type' => GraphQL::type(KatmCreateRequestGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Request of scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->request;
                },
            ],
            'response' => [
                'type' => GraphQL::type(KatmCreditHistoryResponseGraphQLType::GRAPHQL_TYPE_NAME),
                'description' => 'Request of Credit History scoring',
                'resolve' => function (ScoringRequest $root) {
                    return $root->response;
                },
            ],
        ];
    }
}
